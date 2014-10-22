<?php

require_once (CLASSES_PATH . "/actions/GuestAction.class.php");
require_once (CLASSES_PATH . "/managers/CompanyManager.class.php");
require_once (CLASSES_PATH . "/managers/UserManager.class.php");
require_once (CLASSES_PATH . "/managers/CompanyDealersManager.class.php");
require_once (CLASSES_PATH . "/managers/CompaniesPriceListManager.class.php");
require_once (CLASSES_PATH . "/managers/AdminManager.class.php");
require_once (CLASSES_PATH . "/managers/EmailSenderManager.class.php");
require_once (CLASSES_PATH . "/managers/SentSmsManager.class.php");
require_once (CLASSES_PATH . "/managers/CustomerAlertsManager.class.php");
require_once (CLASSES_PATH . "/managers/OnlineUsersManager.class.php");
require_once (CLASSES_PATH . "/managers/PriceTextsManager.class.php");

/**
 * @author Vahagn Sookiasian
 */
class UploadCompanyPriceAction extends GuestAction {

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
            echo "<script>var l= new parent.ngs.UploadCompanyPriceAction(); l.afterAction('" . json_encode($jsonArr) . "'); </script>";
            return false;
        }

        if (!$size || $size > 7 * 1024 * 1024) {
            $jsonArr = array('status' => "err", "errText" => "Maximum file size can be 7MB");
            echo "<script>var l= new parent.ngs.UploadCompanyPriceAction(); l.afterAction('" . json_encode($jsonArr) . "'); </script>";
            return false;
        }

//start to save new price file

        $fname = explode('.', $name);
        end($fname);
        $newFileExt = current($fname);

        if (!in_array($newFileExt, $this->supported_file_formats)) {
            $jsonArr = array('status' => "err", "errText" => "Not supported file format!");
            echo "<script>var l= new parent.ngs.UploadCompanyPriceAction(); l.afterAction('" . json_encode($jsonArr) . "'); </script>";
            return false;
        }

        $userLevel = $this->getUserLevel();
        if ($userLevel === UserGroups::$ADMIN) {
            $companyId = $this->secure($_REQUEST["up_selected_company"]);
        } else if ($userLevel === UserGroups::$COMPANY) {
            $companyId = $this->getUserId();
            assert($companyId == $this->secure($_REQUEST["up_selected_company"]));
        } else {
            $jsonArr = array('status' => "err", "errText" => "Not Access!");
            echo "<script>var l= new parent.ngs.UploadCompanyPriceAction(); l.afterAction('" . json_encode($jsonArr) . "'); </script>";
            return false;
        }

        $dir = DATA_DIR . "/companies_prices/";
        if (!is_dir($dir)) {
            mkdir($dir, 0777);
        }

        $dir = DATA_DIR . "/companies_prices/" . $companyId . '/';
        if (!is_dir($dir)) {
            mkdir($dir, 0777);
        }
        $company_duplicated_price_upload_hours = $this->getCmsVar('company_duplicated_price_upload_hours');

        $companiesPriceListManager = CompaniesPriceListManager::getInstance($this->config, $this->args);
        if (isset($_REQUEST['merge_into_last_price']) && $_REQUEST['merge_into_last_price'] == 1) {
            $duplicatedUpload = $this->checkIfSamePriceAlreadyExists($companyId, $tmp_name);
            $companyLastPriceMinutes = $companiesPriceListManager->getCompanyLastPriceMinutes($companyId);
            if ($companyLastPriceMinutes / 60 < $company_duplicated_price_upload_hours && $duplicatedUpload) {
                $jsonArr = array('status' => "err", "errText" => "Same Price already exists! please try in " . $company_duplicated_price_upload_hours . " hours.");
                echo "<script>var l= new parent.ngs.UploadCompanyPriceAction(); l.afterAction('" . json_encode($jsonArr) . "'); </script>";
                return false;
            }
            $companyLastPriceDtos = $companiesPriceListManager->getCompanyLastPrices($companyId);
            $companyLastPriceInfoDto = end($companyLastPriceDtos);
            $lastPriceName = $companyLastPriceInfoDto->getFileName();
            $newFileName = $lastPriceName . '_' . (count($companyLastPriceDtos) + 1);
            $newFileFullName = $dir . $newFileName . '.' . $newFileExt;
            move_uploaded_file($tmp_name, $newFileFullName);
            $companiesPriceListManager->addCompanyPrice($companyId, $newFileName, $newFileExt, $userLevel == UserGroups::$ADMIN ? "admin" : "company", $this->getUserId());
            $this->updateCompanyPriceText($companyId, count($companyLastPriceDtos));
            $jsonArr = array('status' => "ok");
            echo "<script>var l= new parent.ngs.UploadCompanyPriceAction(); l.afterAction('" . json_encode($jsonArr) . "'); </script>";
            return true;
        }

        $companyTodayPriceUploadedTimes = $companiesPriceListManager->getCompanyTodayPriceUploadedTimes($companyId);
        $company_price_upload_a_day_max_count = $this->getCmsVar('company_price_upload_a_day_max_count');
        if ($companyTodayPriceUploadedTimes >= $company_price_upload_a_day_max_count) {
            $jsonArr = array('status' => "err", "errText" => "You exeeded your daily maximum upload count! (max:" . $company_price_upload_a_day_max_count . " times a day)");
            echo "<script>var l= new parent.ngs.UploadCompanyPriceAction(); l.afterAction('" . json_encode($jsonArr) . "'); </script>";
            return false;
        }

        $companyLastPriceMinutes = $companiesPriceListManager->getCompanyLastPriceMinutes($companyId);
        $duplicatedUpload = $this->checkIfSamePriceAlreadyExists($companyId, $tmp_name);
        if ($companyLastPriceMinutes / 60 < $company_duplicated_price_upload_hours && $duplicatedUpload) {
            $jsonArr = array('status' => "err", "errText" => "Sorry You can not upload same price in " . $company_duplicated_price_upload_hours . " hours. Your company last uploaded price seams to be same as this one!");
            echo "<script>var l= new parent.ngs.UploadCompanyPriceAction(); l.afterAction('" . json_encode($jsonArr) . "'); </script>";
            return false;
        } else {
            if ($companyLastPriceMinutes < 10 && !isset($_REQUEST['new_price_confirmed'])) {
                $jsonArr = array('status' => "war");
                echo "<script>var l= new parent.ngs.UploadCompanyPriceAction(); l.afterAction('" . json_encode($jsonArr) . "'); </script>";
                return false;
            }
        }

        $companyLastPriceDtos = $companiesPriceListManager->getCompanyLastPrices($companyId);

        if (!empty($companyLastPriceDtos)) {
            $companyLastPriceFirstUploadedDto = end($companyLastPriceDtos);
            $lastPriceFiles = array();
            $lastPriceFileName = $companyLastPriceFirstUploadedDto->getFileName();
            foreach ($companyLastPriceDtos as $key => $companyLastPriceDto) {
                $lastPriceName = $companyLastPriceDto->getFileName();
                $lastPriceExt = $companyLastPriceDto->getFileExt();
                $lastPriceFiles [] = array($dir . $lastPriceName . '.' . $lastPriceExt, $lastPriceName . '.' . $lastPriceExt);
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
                $companiesPriceListManager->deleteByPK($companyLastPriceDto->getId());
            }
            $companiesPriceListManager->addCompanyPrice($companyId, $lastPriceFileName, 'zip', $lastPriceUploaderType, $lastPriceUploaderId, $lastPriceUploadedDateTime);
        }

        $now = date("Y-m-d-H-i-s");
        $newFileName = 'price_' . $now;
        $newFileFullName = $dir . $newFileName . '.' . $newFileExt;
        move_uploaded_file($tmp_name, $newFileFullName);
        $companiesPriceListManager->addCompanyPrice($companyId, $newFileName, $newFileExt, $userLevel == UserGroups::$ADMIN ? "admin" : "company", $this->getUserId());

        $jsonArr = array('status' => "ok");
        echo "<script>var l= new parent.ngs.UploadCompanyPriceAction(); l.afterAction('" . json_encode($jsonArr) . "'); </script>";
        $companyManager = new CompanyManager($this->config, $this->args);
        $company = $companyManager->selectByPK($companyId);
        $companyManager->updateCompanyRating($company);


        if (!isset($_REQUEST['silent_mode']) || $_REQUEST['silent_mode'] != 1) {
//$this->sendNewEmailUploadedToAllCompanyAccessedCustomers($company);		
            if ($this->getCmsVar('enable_upload_price_alert') == 1) {
                if ($company->getHidden() == 0) {
                    $this->addEventIntoEventsTableForOnlineCustomers($company);
                }
            }
            $this->sendSmsToAdminIfUploaderIsNotItself($company->getName());
            $this->sendSmsToSmsInterestedCompanies($company->getId(), $company->getName());
        }
        $this->updateCompanyPriceText($companyId, 0);

        return true;
    }

    public function addEventIntoEventsTableForOnlineCustomers($company) {
        $customerAlertsManager = CustomerAlertsManager::getInstance($this->config, $this->args);
        $companyDealersManager = CompanyDealersManager::getInstance($this->config, $this->args);
        $onlineUsersManager = OnlineUsersManager::getInstance($this->config, $this->args);
        $userManager = UserManager::getInstance($this->config, $this->args);
        $onlineRegisteredCustomers = $onlineUsersManager->getOnlineRegisteredCustomers();
        foreach ($onlineRegisteredCustomers as $onlineUsersDto) {
            $customerType = $userManager->getCustomerTypeByEmail($onlineUsersDto->getEmail());
            if ($customerType === UserGroups::$USER) {
                $userCustomer = $userManager->getUserByEmail($onlineUsersDto->getEmail());
                $dealerDto = $companyDealersManager->getByCompanyIdAndUserId($userCustomer->getId(), $company->getId());
                if (isset($dealerDto)) {
                    $customerAlertsManager->addPriceUploadCustomerAlert($onlineUsersDto->getEmail(), $company, $onlineUsersDto->getLanguageCode());
                }
            } elseif ($customerType === UserGroups::$COMPANY || $customerType === UserGroups::$ADMIN) {
                $customerAlertsManager->addPriceUploadCustomerAlert($onlineUsersDto->getEmail(), $company);
            }
        }
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

    public function sendSmsToSmsInterestedCompanies($companyId, $companyName) {
        $companyManager = CompanyManager::getInstance($this->config, $this->args);
        $companiesToReceiveSMS = $companyManager->getCompanyPriceInterestedForSmsCompanies($companyId);
        $sentSmsManager = SentSmsManager::getInstance($this->config, $this->args);
        foreach ($companiesToReceiveSMS as $key => $company) {
            $numberToReceiveSmsOnPriceUpload = $company->getPriceUploadSmsPhoneNumber();
            if (empty($numberToReceiveSmsOnPriceUpload)) {
                continue;
            }
            $weekdays = $company->getSmsReceiveWeekdays();
            $dayofweek = date('N');
            if ($weekdays[$dayofweek - 1] != '1') {
                continue;
            }
            $duration = $company->getSmsToDurationMinutes();
            if ($duration > 0) {
                $smsReceiveTimeStart = $company->getSmsReceiveTimeStart();
                $smsReceiveStartDateTime = strtotime(date('Y-m-d') . ' ' . $smsReceiveTimeStart);
                $smsReceiveEndDateTime = strtotime("+$duration minutes", $smsReceiveStartDateTime);
                $now = time();
                if ($now < $smsReceiveStartDateTime || $now > $smsReceiveEndDateTime) {
                    continue;
                }
            }
            $sentSmsManager->sendSmsToArmenia($numberToReceiveSmsOnPriceUpload, "'" . $companyName . "' just uploaded price on PcStore!    Best Regards www.pcstore.am");
        }
    }

    public function sendNewEmailUploadedToAllCompanyAccessedCustomers($company) {
        $this->sendNewPriceUploadedEmailToAllCompanies($company);
        $this->sendNewPriceUploadedEmailToCompanyDealers($company);
    }

    public function sendNewPriceUploadedEmailToAllCompanies($company) {
        $companyManager = new CompanyManager($this->config, $this->args);
        $allCompanies = $companyManager->getAllCompanies();
        $allCompaniesEmails = $this->getCompaniesEmailByCompaniesDtos($allCompanies);
        $emailSenderManager = new EmailSenderManager('gmail');
        $companyName = $company->getName();
        $subject = 'New Price form ' . $companyName . '!!!';
        $template = "new_price_uploaded";
        $params = array("company_name" => $companyName);
        $emailSenderManager->sendEmail('info', $allCompaniesEmails, $subject, $template, $params, '', '', true, true);
    }

    public function sendNewPriceUploadedEmailToCompanyDealers($company) {
        $companyDealersManager = CompanyDealersManager::getInstance($this->config, $this->args);
        $compayDealers = $companyDealersManager->getCompanyDealersUsersFullInfoHiddenIncluded($company->getId());
        $allUsersEmails = $this->getUsersEmailByUsersDtos($compayDealers);
        $emailSenderManager = new EmailSenderManager('gmail');
        $companyName = $company->getName();
        $subject = 'New Price form ' . $companyName . '!!!';
        $template = "new_price_uploaded";
        $params = array("company_name" => $companyName);
        $emailSenderManager->sendEmail('info', $allUsersEmails, $subject, $template, $params, '', '', true, true);
    }

    public function getCompaniesEmailByCompaniesDtos($dtos) {
        $userManager = new UserManager($this->config, $this->args);
        $ret = array();
        foreach ($dtos as $key => $dto) {
            $em = $dto->getEmail();
            if (filter_var($em, FILTER_VALIDATE_EMAIL)) {
                $ret[] = $em;
            }
        }
        return $ret;
    }

    public function getUsersEmailByUsersDtos($dtos) {
        $userManager = new UserManager($this->config, $this->args);
        $ret = array();
        foreach ($dtos as $key => $dto) {
            $email = $userManager->getRealEmailAddressByUserDto($dto);
            if (!empty($email)) {
                $ret[] = $email;
            }
        }
        return $ret;
    }

    public function getRequestGroup() {
        return RequestGroups::$companyRequest;
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

    private function updateCompanyPriceText($companyId, $priceIndex = 0) {
        if ($this->getCmsVar('dev_mode') == 'on') {
            exec('d:\xampp\php\php.exe ' . CLASSES_PATH . "/util/UpdateCompaniesPriceText.class.php $companyId $priceIndex");
        } else {
            exec('/usr/bin/php ' . CLASSES_PATH . "/util/UpdateCompaniesPriceText.class.php $companyId $priceIndex > /dev/null &");
        }
    }

    public function checkIfSamePriceAlreadyExists($companyId, $tmp_name) {
        $companiesPriceListManager = CompaniesPriceListManager::getInstance($this->config, $this->args);
        $companyLastPrices = $companiesPriceListManager->getCompanyLastPrices($companyId);
        $uploadedFileContentMd5 = md5_file($tmp_name);
        $duplicatedUpload = false;
        foreach ($companyLastPrices as $companyLastPrice) {
            $lastPriceContentMd5 = md5_file($companiesPriceListManager->getPricePath($companyLastPrice));
            if ($uploadedFileContentMd5 === $lastPriceContentMd5) {
                $duplicatedUpload = true;
                break;
            }
        }
        return $duplicatedUpload;
    }

}

?>