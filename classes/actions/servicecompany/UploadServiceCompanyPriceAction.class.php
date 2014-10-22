<?php

require_once (CLASSES_PATH . "/actions/servicecompany/ServiceCompanyAction.class.php");
require_once (CLASSES_PATH . "/managers/ServiceCompanyManager.class.php");
require_once (CLASSES_PATH . "/managers/UserManager.class.php");
require_once (CLASSES_PATH . "/managers/ServiceCompanyDealersManager.class.php");
require_once (CLASSES_PATH . "/managers/ServiceCompaniesPriceListManager.class.php");
require_once (CLASSES_PATH . "/managers/EmailSenderManager.class.php");
require_once (CLASSES_PATH . "/managers/SentSmsManager.class.php");
require_once (CLASSES_PATH . "/managers/CustomerAlertsManager.class.php");
require_once (CLASSES_PATH . "/managers/OnlineUsersManager.class.php");
require_once (CLASSES_PATH . "/managers/PriceTextsManager.class.php");

/**
 * @author Vahagn Sookiasian
 */
class UploadServiceCompanyPriceAction extends ServiceCompanyAction {

    private $supported_file_formats = array('xls', 'xlsx', 'pdf', 'doc', 'docx', 'txt', 'csv');

    public function service() {
//getting parameters
        ini_set('upload_max_filesize', '7M');
        $name = $_FILES['company_price']['name'];
        $type = $_FILES['company_price']['type'];
        $tmp_name = $_FILES['company_price']['tmp_name'];
        $size = $_FILES['company_price']['size'];

        $response = $this->checkInputFile('company_price');

        if ($response !== 'ok') {
            $jsonArr = array('status' => "err", "errText" => $response);
            echo "<script>var l= new parent.ngs.UploadServiceCompanyPriceAction(); l.afterAction('" . json_encode($jsonArr) . "'); </script>";
            return false;
        }

        if (!$size || $size > 7 * 1024 * 1024) {
            $jsonArr = array('status' => "err", "errText" => "Maximum file size can be 7MB");
            echo "<script>var l= new parent.ngs.UploadServiceCompanyPriceAction(); l.afterAction('" . json_encode($jsonArr) . "'); </script>";
            return false;
        }

//start to save new price file

        $fname = explode('.', $name);
        end($fname);
        $newFileExt = current($fname);

        if (!in_array($newFileExt, $this->supported_file_formats)) {
            $jsonArr = array('status' => "err", "errText" => "Not supported file format!");
            echo "<script>var l= new parent.ngs.UploadServiceCompanyPriceAction(); l.afterAction('" . json_encode($jsonArr) . "'); </script>";
            return false;
        }


        $userLevel = $this->getUserLevel();
        if ($userLevel === UserGroups::$ADMIN) {
            $serviceCompanyId = $this->secure($_REQUEST["up_selected_service_company"]);
        } else if ($userLevel === UserGroups::$SERVICE_COMPANY) {
            $serviceCompanyId = $this->getUserId();
            assert($serviceCompanyId == $this->secure($_REQUEST["up_selected_service_company"]));
        } else {
            $jsonArr = array('status' => "err", "errText" => "Not Access!");
            echo "<script>var l= new parent.ngs.UploadServiceCompanyPriceAction(); l.afterAction('" . json_encode($jsonArr) . "'); </script>";
            return false;
        }


        $dir = DATA_DIR . "/service_companies_prices/";
        if (!is_dir($dir)) {
            mkdir($dir, 0777);
        }

        $dir = DATA_DIR . "/service_companies_prices/" . $serviceCompanyId . '/';
        if (!is_dir($dir)) {
            mkdir($dir, 0777);
        }
        $company_duplicated_price_upload_hours = $this->getCmsVar('company_duplicated_price_upload_hours');
        $serviceCompaniesPriceListManager = ServiceCompaniesPriceListManager::getInstance($this->config, $this->args);

        $company_price_upload_a_day_max_count = $this->getCmsVar('company_price_upload_a_day_max_count');
        if (isset($_REQUEST['merge_into_last_price']) && $_REQUEST['merge_into_last_price'] == 1) {
            $duplicatedUpload = $this->checkIfSamePriceAlreadyExists($serviceCompanyId, $tmp_name);
            $companyLastPriceMinutes = $serviceCompaniesPriceListManager->getCompanyLastPriceMinutes($serviceCompanyId);
            if ($companyLastPriceMinutes / 60 < $company_duplicated_price_upload_hours && $duplicatedUpload) {
                $jsonArr = array('status' => "err", "errText" => "Same Price already exists! please try in " . $company_duplicated_price_upload_hours . " hours.");
                 echo "<script>var l= new parent.ngs.UploadServiceCompanyPriceAction(); l.afterAction('" . json_encode($jsonArr) . "'); </script>";
                return false;
            }
            $companyLastPriceDtos = $serviceCompaniesPriceListManager->getCompanyLastPrices($serviceCompanyId);
            $companyLastPriceInfoDto = end($companyLastPriceDtos);
            $lastPriceName = $companyLastPriceInfoDto->getFileName();
            $newFileName = $lastPriceName . '_' . (count($companyLastPriceDtos) + 1);
            $newFileFullName = $dir . $newFileName . '.' . $newFileExt;
            move_uploaded_file($tmp_name, $newFileFullName);
            $serviceCompaniesPriceListManager->addCompanyPrice($serviceCompanyId, $newFileName, $newFileExt, $userLevel == UserGroups::$ADMIN ? "admin" : "servicecompany", $this->getUserId());
            $jsonArr = array('status' => "ok");
            echo "<script>var l= new parent.ngs.UploadCompanyPriceAction(); l.afterAction('" . json_encode($jsonArr) . "'); </script>";
            return true;
        }

        $companyTodayPriceUploadedTimes = $serviceCompaniesPriceListManager->getCompanyTodayPriceUploadedTimes($serviceCompanyId);
        if ($companyTodayPriceUploadedTimes >= $company_price_upload_a_day_max_count) {
            $jsonArr = array('status' => "err", "errText" => "You exeeded your daily maximum upload count! (max:" . $company_price_upload_a_day_max_count . " times a day)");
            echo "<script>var l= new parent.ngs.UploadServiceCompanyPriceAction(); l.afterAction('" . json_encode($jsonArr) . "'); </script>";
            return false;
        }

        $serviceCompanyLastPriceMinutes = $serviceCompaniesPriceListManager->getCompanyLastPriceMinutes($serviceCompanyId);
        $duplicatedUpload = $this->checkIfSamePriceAlreadyExists($serviceCompanyId, $tmp_name);
        if ($serviceCompanyLastPriceMinutes / 60 < $company_duplicated_price_upload_hours && $duplicatedUpload) {
            $jsonArr = array('status' => "err", "errText" => "Sorry You can not upload same price in " . $company_duplicated_price_upload_hours . " hours. Your company last uploaded price seams to be same as this one!");
            echo "<script>var l= new parent.ngs.UploadServiceCompanyPriceAction(); l.afterAction('" . json_encode($jsonArr) . "'); </script>";
            return false;
        }

        $companyLastPriceDtos = $serviceCompaniesPriceListManager->getCompanyLastPrices($serviceCompanyId);

        if (!empty($companyLastPriceDtos)) {
            $companyLastPriceFirstUploadedDto = end($companyLastPriceDtos);
            $lastPriceFiles = array();
            $lastPriceFileName = $companyLastPriceFirstUploadedDto->getFileName();
            foreach ($companyLastPriceDtos as $key => $companyLastPriceDto) {
                $lastPriceName = $companyLastPriceDto->getFileName();
                $lastPriceExt = $companyLastPriceDto->getFileExt();
                $lastPriceFiles [] = array($dir . $lastPriceName . '.' . $lastPriceExt, $lastPriceName . '.'.$lastPriceExt);
            }            
            $this->createZip($lastPriceFiles, $dir . $lastPriceFileName . '.zip');
            $lastPriceUploadedDateTime = $companyLastPriceFirstUploadedDto->getUploadDateTime();
            $lastPriceUploaderType = $companyLastPriceFirstUploadedDto->getUploaderType();
            $lastPriceUploaderId = $companyLastPriceFirstUploadedDto->getUploaderId();
            foreach ($companyLastPriceDtos as $key => $companyLastPriceDto) {
                $lastPriceName = $companyLastPriceDto->getFileName();
                $lastPriceExt = $companyLastPriceDto->getFileExt();
                if (is_file($dir . $lastPriceName . '.' . $lastPriceExt)) {
                    unlink($dir . $lastPriceName . '.' . $lastPriceExt);
                }
                $serviceCompaniesPriceListManager->deleteByPK($companyLastPriceDto->getId());
            }
            $serviceCompaniesPriceListManager->addCompanyPrice($serviceCompanyId, $lastPriceFileName, 'zip', $lastPriceUploaderType, $lastPriceUploaderId, $lastPriceUploadedDateTime);
        }

        $now = date("Y-m-d-H-i-s");
        $newFileName = 'price_' . $now;
        $newFileFullName = $dir . $newFileName . '.' . $newFileExt;
        move_uploaded_file($tmp_name, $newFileFullName);
        $serviceCompaniesPriceListManager->addCompanyPrice($serviceCompanyId, $newFileName, $newFileExt, $userLevel == UserGroups::$ADMIN ? "admin" : "servicecompany", $this->getUserId());

        $jsonArr = array('status' => "ok");
        echo "<script>var l= new parent.ngs.UploadServiceCompanyPriceAction(); l.afterAction('" . json_encode($jsonArr) . "'); </script>";
        $serviceCompanyManager = new ServiceCompanyManager($this->config, $this->args);
        $serviceCompany = $serviceCompanyManager->selectByPK($serviceCompanyId);

        //$this->sendNewEmailUploadedToAllCompanyAccessedCustomers($company);		
        if ($this->getCmsVar('enable_upload_price_alert') == 1) {
            $this->addEventIntoEventsTableForOnlineCustomers($serviceCompany);
        }
        $this->sendSmsToAdminIfUploaderIsNotItself($serviceCompany->getName());
        return true;
    }

    public function addEventIntoEventsTableForOnlineCustomers($serviceCompany) {
        $customerAlertsManager = CustomerAlertsManager::getInstance($this->config, $this->args);
        $serviceCompanyDealersManager = ServiceCompanyDealersManager::getInstance($this->config, $this->args);
        $onlineUsersManager = OnlineUsersManager::getInstance($this->config, $this->args);
        $userManager = UserManager::getInstance($this->config, $this->args);
        $onlineRegisteredCustomers = $onlineUsersManager->getOnlineRegisteredCustomers();
        foreach ($onlineRegisteredCustomers as $onlineUsersDto) {
            $customerType = $userManager->getCustomerTypeByEmail($onlineUsersDto->getEmail());
            if ($customerType === UserGroups::$USER) {
                $userCustomer = $userManager->getUserByEmail($onlineUsersDto->getEmail());
                $dealerDto = $serviceCompanyDealersManager->getByCompanyIdAndUserId($userCustomer->getId(), $serviceCompany->getId());
                if (isset($dealerDto)) {
                    $customerAlertsManager->addPriceUploadCustomerAlert($onlineUsersDto->getEmail(), $serviceCompany, $onlineUsersDto->getLanguageCode());
                }
            } elseif ($customerType === UserGroups::$SERVICE_COMPANY || $customerType === UserGroups::$COMPANY || $customerType === UserGroups::$ADMIN) {
                $customerAlertsManager->addPriceUploadCustomerAlert($onlineUsersDto->getEmail(), $serviceCompany);
            }
        }
    }

    function createZip($sources, $destination) {
        $zip = new ZipArchive();
        if ($zip->open($destination, ZipArchive::CREATE) === TRUE) {
            foreach ($sources as $source) {
                $zip->addFile($source[0], $source[1]);
            }
            $zip->close();
            return true;
        }
        return false;
    }

    public function sendSmsToAdminIfUploaderIsNotItself($companyName) {
        $adminManager = AdminManager::getInstance($this->config, $this->args);
        $adminsToReceiveSms = $adminManager->getSmsEnabledAdmins();
        $sentSmsManager = SentSmsManager::getInstance($this->config, $this->args);
        foreach ($adminsToReceiveSms as $key => $admin) {
            if ($this->getUserLevel() === UserGroups::$ADMIN && $this->getUserId() == $admin->getId()) {
                continue;
            }
            $numberToReceiveSmsOnPriceUpload = $admin->getNumberToReceiveSmsOnPriceUpload();
            if (!empty($numberToReceiveSmsOnPriceUpload)) {
                $sentSmsManager->sendSmsToArmenia($numberToReceiveSmsOnPriceUpload, "'" . $companyName . "' just uploaded price on PcStore!    Best Regards www.pcstore.am");
            }
        }
    }

    public function checkIfSamePriceAlreadyExists($companyId, $tmp_name) {
        $serviceCompaniesPriceListManager = ServiceCompaniesPriceListManager::getInstance($this->config, $this->args);
        $companyLastPrices = $serviceCompaniesPriceListManager->getCompanyLastPrices($companyId);
        $uploadedFileContentMd5 = md5_file($tmp_name);
        $duplicatedUpload = false;
        foreach ($companyLastPrices as $companyLastPrice) {
            $lastPriceContentMd5 = md5_file($serviceCompaniesPriceListManager->getPricePath($companyLastPrice));
            if ($uploadedFileContentMd5 === $lastPriceContentMd5) {
                $duplicatedUpload = true;
                break;
            }
        }
        return $duplicatedUpload;
    }

}

?>