<?php

require_once (CLASSES_PATH . "/actions/company/CompanyAction.class.php");
require_once (CLASSES_PATH . "/managers/ServiceCompanyManager.class.php");
require_once (CLASSES_PATH . "/managers/UserManager.class.php");
require_once (CLASSES_PATH . "/util/ImageThumber.php");

/**
 * @author Vahagn Sookiasian
 */
class ChangeServiceCompanyProfileAction extends CompanyAction {

    private $userManager;
    private $supported_file_formats = array('jpg', 'png', 'gif');

    public function service() {

        $serviceCompanyManager = new ServiceCompanyManager($this->config, $this->args);
        $this->userManager = new UserManager($this->config, $this->args);

        $company_branch_id = $this->secure($_REQUEST["cp_branch_select"]);
        $company_name = $this->secure($_REQUEST["company_name"]);
        $accessKey = $this->secure($_REQUEST["dealers_access_key"]);
        $change_pass = $this->secure($_REQUEST["change_pass"]);
        $new_pass = $this->secure($_REQUEST["new_pass"]);
        $repeat_new_pass = $this->secure($_REQUEST["repeat_new_pass"]);
        $phone1 = $this->secure($_REQUEST["phone1"]);
        $phone2 = $this->secure($_REQUEST["phone2"]);
        $phone3 = $this->secure($_REQUEST["phone3"]);
        $address = $this->secure($_REQUEST["address"]);
        $region = $this->secure($_REQUEST["region"]);
        $lng = $this->secure($_REQUEST["longitute"]);
        $lat = $this->secure($_REQUEST["latitude"]);
        $working_days = $this->secure($_REQUEST["working_days"]);
        $working_hours = $this->secure($_REQUEST["working_hours"]);
        $zip = $this->secure($_REQUEST["zip"]);
        $url = $this->secure($_REQUEST["url"]);


        $validFields = $this->validateCompanyProfileFields($company_name, $change_pass, $new_pass, $repeat_new_pass, $phone1, $phone2, $phone3, $address, $zip, $region, $working_days, $working_hours, $url);
        $companyId = $this->getUserId();

        if ($validFields === 'ok') {
            if (isset($_FILES['service_company_logo'])) {

                ////////////////////////////
                $originalLogoFullName = null;

                $file_name = $_FILES['service_company_logo']['name'];
                $file_type = $_FILES['service_company_logo']['type'];
                $tmp_name = $_FILES['service_company_logo']['tmp_name'];
                $file_size = $_FILES['service_company_logo']['size'];

                $logoCheck = $this->checkInputFile('service_company_logo');

                //start to save new price file

                $logoExt = end(explode('.', $file_name));
                if ($logoCheck === 'ok' && (!in_array($logoExt, $this->supported_file_formats))) {
                    $logoCheck = "Not supported file format! ()";
                }
                if ($logoCheck === 'ok') {
                    $dir = DATA_DIR . "/images/";
                    if (!is_dir($dir)) {
                        mkdir($dir, 0777);
                    }
                    $dir = DATA_DIR . "/images/service_company_logo/";
                    if (!is_dir($dir)) {
                        mkdir($dir, 0777);
                    }

                    $logoName = 'service_company_' . $companyId . '_logo';
                    $originalLogoFullName = $dir . $logoName . '_original' . '.' . 'png';
                    $resizedLogoFullName_55_30 = $dir . $logoName . '_55_30' . '.' . 'png';
                    $resizedLogoFullName_120_75 = $dir . $logoName . '_120_75' . '.' . 'png';
                    move_uploaded_file($tmp_name, $originalLogoFullName);
                    $resret1 = resizeImageToGivenType($originalLogoFullName, $resizedLogoFullName_55_30, 55, 30, 'png');
                    $resret2 = resizeImageToGivenType($originalLogoFullName, $resizedLogoFullName_120_75, 120, 75, 'png');
                    //resize image
                    if ($logoCheck === 'ok' && $resret1 == false) {
                        $logoCheck = "Error resizing image!";
                    }
                    if (is_file($originalLogoFullName)) {
                        unlink($originalLogoFullName);
                    }
                }
            }

            $sms_phone_number = '';
            if (isset($_REQUEST['enable_sms_on_price_upload'])) {
                $sms_phone_number = $this->secure($_REQUEST['sms_phone_number']);
            }
            $sms_from_time = date('H:i:s', strtotime($this->secure($_REQUEST['sms_from_time'])));
            $sms_to_duration_minutes = 0;
            if (isset($_REQUEST['sms_time_control'])) {
                $sms_to_duration_minutes = $this->secure($_REQUEST['sms_to_duration_minutes']);
            }
            $sms_receiving_days = $this->secure($_REQUEST['sms_receiving_days']);
            $serviceCompanyManager->updateCompanyProfileFieldsById($companyId, $company_branch_id, $company_name, $change_pass, $new_pass, $accessKey, $phone1, $phone2, $phone3, $address, $zip, $region, $working_days, $working_hours, $url, $lng, $lat);
            $jsonArr = array('status' => "ok", "message" => $logoCheck);
            echo "<script>var l= new parent.ngs.ChangeServiceCompanyProfileAction(); l.afterAction('" . json_encode($jsonArr) . "'); </script>";
            return true;

            ////////////////////////////
        } else {

            $jsonArr = array('status' => "err", "errText" => $validFields);
            echo "<script>var l= new parent.ngs.ChangeServiceCompanyProfileAction(); l.afterAction('" . json_encode($jsonArr) . "'); </script>";
            return false;
        }
    }

    public function validateCompanyProfileFields($company_name, $change_pass, $new_pass, $repeat_new_pass, $phone1, $phone2, $phone3, $address, $working_days, $dealers_access_key, $url) {

        if ($change_pass) {
            if (!$this->userManager->checkPassword($new_pass))
                return 'Invalid password';
            if ($new_pass != $repeat_new_pass)
                return 'New passwords don\'t matched';
        }
        if ($phone1 != null && (!strpos(',', $phone1) === false)) {
            return 'Phone number can not contain comma charecter';
        }
        if ($phone2 != null && (!strpos(',', $phone2) === false)) {
            return 'Phone number can not contain comma charecter';
        }
        if ($phone3 != null && (!strpos(',', $phone3) === false)) {
            return 'Phone number can not contain comma charecter';
        }
        if ($dealers_access_key && strlen($dealers_access_key) < 6) {
            return 'Dealers Access Key must have at least 6 charecters';
        }

        return 'ok';
    }

}

?>