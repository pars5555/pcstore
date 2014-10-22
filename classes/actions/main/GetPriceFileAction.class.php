<?php

require_once (CLASSES_PATH . "/actions/GuestAction.class.php");
require_once (CLASSES_PATH . "/managers/CompaniesPriceListManager.class.php");
require_once (CLASSES_PATH . "/managers/ServiceCompaniesPriceListManager.class.php");
require_once (CLASSES_PATH . "/managers/CompanyManager.class.php");
require_once (CLASSES_PATH . "/managers/ServiceCompanyManager.class.php");
require_once (CLASSES_PATH . "/managers/CompanyDealersManager.class.php");
require_once (CLASSES_PATH . "/managers/ServiceCompanyDealersManager.class.php");

/**
 * @author Vahagn Sookiasian
 * @site http://naghashyan.com
 * @mail vahagnsookaisyan@gmail.com
 * @year 2010-2012
 */
class GetPriceFileAction extends GuestAction {

    public $imgType;

    public function service() {

        //todo check if user have access to given company

        $customer = $this->sessionManager->getUser();
        $userLevel = $customer->getLevel();
        $userId = $customer->getId();


        $companyManager = CompanyManager::getInstance($this->config, $this->args);
        $serviceCompanyManager = ServiceCompanyManager::getInstance($this->config, $this->args);

        if ($this->args[0] == "last_price") {
            $companyId = $this->args[1];
            if ($userLevel == UserGroups::$USER) {
                $companyDealersManager = CompanyDealersManager::getInstance($this->config, $this->args);
                $dto = $companyDealersManager->getByCompanyIdAndUserId($userId, $companyId);
                if (!isset($dto)) {
                    return false;
                }
            }
            $company = $companyManager->selectByPK($companyId);
            assert($company);
            $companiesPriceListManager = CompaniesPriceListManager::getInstance($this->config, $this->args);
            $companyLastPrices = $companiesPriceListManager->getCompanyLastPrices($companyId);
            if (!empty($companyLastPrices)) {
                if (count($companyLastPrices) === 1) {
                    $pricePath = DATA_DIR . "/companies_prices/" . $companyId . '/' . $companyLastPrices[0]->getFileName() . '.' . $companyLastPrices[0]->getFileExt();
                    if (file_exists($pricePath)) {
                        $companiesPriceListManager->downloadFile($pricePath, $company->getShortName() . "_" . $companyLastPrices[0]->getFileName());
                    } else {
                        echo "file not exists!";
                    }
                } else {
                    $filesPaths = array();
                    foreach ($companyLastPrices as $key => $clp) {
                        $fileName = $clp->getFileName() . '.' . $clp->getFileExt();
                        $filesPaths[] = array(DATA_DIR . "/companies_prices/" . $company->getId() . '/' . $fileName, $company->getShortName() . '_' . ($key + 1) . '_' . $fileName);
                    }
                    $uid = uniqid();
                    $this->createZip($filesPaths, DATA_DIR . "/temp/" . $uid . '.zip');
                    $companiesPriceListManager->downloadFile(DATA_DIR . "/temp/" . $uid . '.zip', $company->getShortName() . '_price');
                    unlink(DATA_DIR . "/temp/" . $uid . '.zip');
                }
            } else {
                echo "file not exists!";
            }
        }
        if ($this->args[0] == "service_last_price") {
            $companyId = $this->args[1];
            if ($userLevel == UserGroups::$USER) {
                $serviceCompanyDealersManager = ServiceCompanyDealersManager::getInstance($this->config, $this->args);
                $dto = $serviceCompanyDealersManager->getByCompanyIdAndUserId($userId, $companyId);
                if (!isset($dto)) {
                    return false;
                }
            }
            $company = $serviceCompanyManager->selectByPK($companyId);
            assert($company);
            $companiesPriceListManager = ServiceCompaniesPriceListManager::getInstance($this->config, $this->args);
            $companyLastPrices = $companiesPriceListManager->getCompanyLastPrices($companyId);
            if (!empty($companyLastPrices)) {
                if (count($companyLastPrices) === 1) {
                    $pricePath = DATA_DIR . "/service_companies_prices/" . $companyId . '/' . $companyLastPrices[0]->getFileName() . '.' . $companyLastPrices[0]->getFileExt();
                    if (file_exists($pricePath)) {
                        $companiesPriceListManager->downloadFile($pricePath, $company->getShortName() . "_" . $companyLastPrices[0]->getFileName());
                    } else {
                        echo "file not exists!";
                    }
                } else {
                    $filesPaths = array();
                    foreach ($companyLastPrices as $key => $clp) {
                        $fileName = $clp->getFileName() . '.' . $clp->getFileExt();
                        $filesPaths[] = array(DATA_DIR . "/service_companies_prices/" . $company->getId() . '/' . $fileName, $company->getShortName() . '_' . ($key + 1) . '_' . $fileName);
                    }
                    $uid = uniqid();
                    $this->createZip($filesPaths, DATA_DIR . "/temp/" . $uid . '.zip');
                    $companiesPriceListManager->downloadFile(DATA_DIR . "/temp/" . $uid . '.zip', $company->getShortName() . '_price');
                    unlink(DATA_DIR . "/temp/" . $uid . '.zip');
                }
            } else {
                echo "file not exists!";
            }
        }
        if ($this->args[0] == "zipped_price") {
            $zippedPriceId = $this->args[1];
            $companiesPriceListManager = CompaniesPriceListManager::getInstance($this->config, $this->args);
            $price = $companiesPriceListManager->selectByPK($zippedPriceId);
            $company_id = $price->getCompanyId();
            if ($userLevel == UserGroups::$USER) {
                $companyDealersManager = CompanyDealersManager::getInstance($this->config, $this->args);
                $dto = $companyDealersManager->getByCompanyIdAndUserId($userId, $company_id);
                if (!isset($dto)) {
                    return false;
                }
            }
            $company = $companyManager->selectByPK($company_id);
            assert($company);
            if ($price) {
                $pricePath = DATA_DIR . "/companies_prices/" . $price->getCompanyId() . '/' . $price->getFileName() . '.' . $price->getFileExt();
                if (file_exists($pricePath)) {
                    $companiesPriceListManager->downloadFile($pricePath, $company->getShortName() . "_" . $price->getFileName());
                } else {
                    echo "file not exists!";
                }
            } else {
                echo "file not exists!";
            }
        }
        if ($this->args[0] == "zipped_price_unzipped") {
            $zippedPriceId = $this->args[1];
            $companiesPriceListManager = CompaniesPriceListManager::getInstance($this->config, $this->args);
            $price = $companiesPriceListManager->selectByPK($zippedPriceId);
            $company_id = $price->getCompanyId();
            if ($userLevel == UserGroups::$USER) {
                $companyDealersManager = CompanyDealersManager::getInstance($this->config, $this->args);
                $dto = $companyDealersManager->getByCompanyIdAndUserId($userId, $company_id);
                if (!isset($dto)) {
                    return false;
                }
            }
            $company = $companyManager->selectByPK($company_id);
            assert($company);
            if ($price) {
                $pricePath = DATA_DIR . "/companies_prices/" . $price->getCompanyId() . '/' . $price->getFileName() . '.' . $price->getFileExt();
                if (file_exists($pricePath)) {
                    $unzipPriceFile = $companiesPriceListManager->unzipFile($pricePath);
                    if (count($unzipPriceFile) === 1) {
                        $companiesPriceListManager->downloadFile($unzipPriceFile[0], $company->getShortName() . "_" . $price->getFileName());
                    } else {

                        $companiesPriceListManager->downloadFile($pricePath, $company->getShortName() . "_" . $price->getFileName());
                    }
                } else {
                    echo "file not exists!";
                }
            } else {
                echo "file not exists!";
            }
        }
        if ($this->args[0] == "service_zipped_price_unzipped") {
            $zippedPriceId = $this->args[1];
            $companiesPriceListManager = ServiceCompaniesPriceListManager::getInstance($this->config, $this->args);
            $price = $companiesPriceListManager->selectByPK($zippedPriceId);
            $company_id = $price->getServiceCompanyId();
            if ($userLevel == UserGroups::$USER) {
                $serviceCompanyDealersManager = ServiceCompanyDealersManager::getInstance($this->config, $this->args);
                $dto = $serviceCompanyDealersManager->getByCompanyIdAndUserId($userId, $company_id);
                if (!isset($dto)) {
                    return false;
                }
            }
            $company = $serviceCompanyManager->selectByPK($company_id);
            assert($company);
            if ($price) {
                $pricePath = DATA_DIR . "/service_companies_prices/" . $price->getServiceCompanyId() . '/' . $price->getFileName() . '.' . $price->getFileExt();
                if (file_exists($pricePath)) {
                    $unzipPriceFile = $companiesPriceListManager->unzipFile($pricePath);
                    if (count($unzipPriceFile) === 1) {
                        $companiesPriceListManager->downloadFile($unzipPriceFile[0], $company->getShortName() . "_" . $price->getFileName());
                    } else {
                        $companiesPriceListManager->downloadFile($pricePath, $company->getShortName() . "_" . $price->getFileName());
                    }
                } else {
                    echo "file not exists!";
                }
            } else {
                echo "file not exists!";
            }
        }
        if ($this->args[0] == "all_zipped_prices") {
            $companiesList = array();
            if ($userLevel == UserGroups::$COMPANY) {
                $companiesList = $companyManager->getAllCompaniesByPriceHours();
            }
            if ($userLevel == UserGroups::$USER) {
                $companiesList = $companyManager->getUserCompaniesJoindWithFullInfo($userId);
            }
            if ($userLevel == UserGroups::$ADMIN) {
                $companiesList = $companyManager->getAllCompaniesByPriceHours(0, "", true, true);
            }
            $filesPaths = array();
            $companiesPriceListManager = CompaniesPriceListManager::getInstance($this->config, $this->args);
            foreach ($companiesList as $company) {
                $companyLastPrices = $companiesPriceListManager->getCompanyLastPrices($company->getId());
                foreach ($companyLastPrices as $key => $companyLastPrice) {
                    $fileName = $companyLastPrice->getFileName() . '.' . $companyLastPrice->getFileExt();
                    $filesPaths[] = array(DATA_DIR . "/companies_prices/" . $company->getId() . '/' . $fileName, $company->getShortName() . '_' . $fileName . '_' . ($key + 1));
                }
            }
            $uid = uniqid();
            $this->createZip($filesPaths, DATA_DIR . "/temp/" . $uid . '.zip');
            $companiesPriceListManager->downloadFile(DATA_DIR . "/temp/" . $uid . '.zip', 'all');
            unlink(DATA_DIR . "/temp/" . $uid . '.zip');
        }
    }

    public function getRequestGroup() {
        return RequestGroups::$userCompanyRequest;
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

}

?>