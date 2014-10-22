<?php

require_once (CLASSES_PATH . "/actions/GuestAction.class.php");
require_once (CLASSES_PATH . "/managers/CompanyManager.class.php");

/**
 * @author Vahagn Sookiasian
 */
class UploadAttachmentAction extends GuestAction {

    private $supported_file_formats = array('xls', 'xlsx', 'pdf', 'doc', 'docx', 'txt', 'csv', 'jpg', 'jpeg', 'png', 'zip', 'rar');

    public function service() {
        $companyId = $this->getUserId();
        $userLevel = $this->getUserLevel();
//getting parameters
        ini_set('upload_max_filesize', '7M');
        $name = $_FILES['attachment']['name'];
        $type = $_FILES['attachment']['type'];
        $tmp_name = $_FILES['attachment']['tmp_name'];
        $size = $_FILES['attachment']['size'];

        $response = $this->checkInputFile('attachment');

        if ($response !== 'ok') {
            $jsonArr = array('status' => "err", "message" => $response);
            echo "<script>var l= new parent.ngs.UploadAttachmentAction(); l.afterAction('" . json_encode($jsonArr) . "'); </script>";
            return false;
        }

        $fname = explode('.', $name);
        end($fname);
        $newFileExt = strtolower(current($fname));
        unset($fname[count($fname) - 1]);
        $uploadedFileOriginalNameOnly = implode('.', $fname);


        if (!in_array($newFileExt, $this->supported_file_formats)) {
            $jsonArr = array('status' => "err", "message" => "Not supported file format! " . $newFileExt);
            echo "<script>var l= new parent.ngs.UploadAttachmentAction(); l.afterAction('" . json_encode($jsonArr) . "'); </script>";
            return false;
        }

        $dir = HTDOCS_TMP_DIR_ATTACHMENTS . '/';
        if (!is_dir($dir)) {
            mkdir($dir, 0777);
        }
        if ($userLevel == UserGroups::$COMPANY) {
            $subDir = 'companies';
        } else {
            $subDir = 'service_companies';
        }

        $dir = HTDOCS_TMP_DIR_ATTACHMENTS . '/' . $subDir . '/';
        if (!is_dir($dir)) {
            mkdir($dir, 0777);
        }

        $dir = HTDOCS_TMP_DIR_ATTACHMENTS . '/' . $subDir . '/' . $companyId . '/';
        if (!is_dir($dir)) {
            mkdir($dir, 0777);
        }

        $newFileName = uniqid() . "_" . $uploadedFileOriginalNameOnly;
        $newFileFullName = $dir . $newFileName . '.' . $newFileExt;
        move_uploaded_file($tmp_name, $newFileFullName);

        $attachmentIconPathToShowInFrontend = "";
        switch ($newFileExt) {
            case 'jpg':
            case 'jpeg':
            case 'png':
                $attachmentIconPathToShowInFrontend = '/tmp/attachments/' . $subDir . '/' . $companyId . '/' . $newFileName . '.' . $newFileExt;
                break;
            default :
                $attachmentIconPathToShowInFrontend = '/img/file_types_icons/' . $newFileExt . "_icon.png";
                break;
        }

        $jsonArr = array('status' => "ok", "attachmentIconPathToShowInFrontend" => $attachmentIconPathToShowInFrontend
            , "file_name" => ($uploadedFileOriginalNameOnly . '.' . $newFileExt)
            , "file_real_name" => ($newFileName . '.' . $newFileExt));
        echo "<script>var l= new parent.ngs.UploadAttachmentAction(); l.afterAction('" . json_encode($jsonArr) . "'); </script>";
    }

    public function getRequestGroup() {
        return RequestGroups::$companyAndServiceCompanyRequest;
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

    function createZip($source, $destination) {
        $zip = new ZipArchive();
        if ($zip->open($destination, ZipArchive::CREATE) === TRUE) {
            $zip->addFile($source);
            $zip->close();
            return true;
        }
        return false;
    }

}

?>