<?php

require_once (CLASSES_PATH . "/managers/AbstractManager.class.php");
require_once (CLASSES_PATH . "/dal/mappers/ServiceCompaniesPriceListMapper.class.php");

/**
 * CategoryHierarchyManager class is responsible for creating,
 *
 * @author Vahagn Sookiasian
 * @package managers
 * @version 1.0
 */
class ServiceCompaniesPriceListManager extends AbstractManager {

    /**
     * @var app config
     */
    private $config;

    /**
     * @var passed arguemnts
     */
    private $args;

    /**
     * @var singleton instance of class
     */
    private static $instance = null;

    /**
     * Initializes DB mappers
     *
     * @param object $config
     * @param object $args
     * @return
     */
    function __construct($config, $args) {
        $this->mapper = ServiceCompaniesPriceListMapper::getInstance();
        $this->config = $config;
        $this->args = $args;
    }

    /**
     * Returns an singleton instance of this class
     *
     * @param object $config
     * @param object $args
     * @return
     */
    public static function getInstance($config, $args) {

        if (self::$instance == null) {
            self::$instance = new ServiceCompaniesPriceListManager($config, $args);
        }
        return self::$instance;
    }

    public function getCompanyLastPrices($company_id) {
        return $this->mapper->getCompanyLastPrices($company_id);
    }

    public function getCompanyPrevPrice($company_id) {
        $companyHistoryPricesOrderByDate = $this->getCompanyHistoryPricesOrderByDate($company_id, 0, 1);
        if (isset($companyHistoryPricesOrderByDate) && !empty($companyHistoryPricesOrderByDate)) {
            return $companyHistoryPricesOrderByDate[0];
        } else {
            return null;
        }
    }

    public function getCompanyHistoryPricesOrderByDate($company_id, $offset, $limit) {
        return $this->mapper->getCompanyHistoryPricesOrderByDate($company_id, $offset, $limit);
    }

    public function addCompanyPrice($serviceCompanyId, $fileName, $fileExt, $uploaderType, $uploaderId, $uploadedDateTime = null) {
        $dto = $this->mapper->createDto();
        $dto->setServiceCompanyId($serviceCompanyId);
        $dto->setFileName($fileName);
        $dto->setFileExt($fileExt);
        if (!isset($uploadedDateTime)) {
            $dto->setUploadDateTime(date('Y-m-d H:i:s'));
        } else {
            $dto->setUploadDateTime($uploadedDateTime);
        }
        $dto->setUploaderType($uploaderType);
        $dto->setUploaderId($uploaderId);
        return $this->mapper->insertDto($dto);
    }

    public function getCompanyTodayPriceUploadedTimes($companyId) {
        return $this->mapper->getCompanyTodayPriceUploadedTimes($companyId);
    }

    public function removeCompanyPrice($id, $removeFromTableToo = true) {
        $dto = $this->mapper->selectByPK($id);
        if ($dto) {
            $companyId = $dto->getServiceCompanyId();
            $fileName = $dto->getFileName();
            $fileExt = $dto->getFileExt();
            $dir = DATA_DIR . "/service_companies_prices/" . $companyId . '/';
            $fileFullPath = $dir . $fileName . '.' . $fileExt;
            if (is_file($fileFullPath)) {
                unlink($fileFullPath);
            }
            if ($removeFromTableToo === true) {
                return $this->mapper->deleteByPK($dto->getId());
            } else {
                return true;
            }
        } else {
            return false;
        }
    }

    public function getUploadedPriceCountByUploaderTypeIdAndDays($uploaderType, $companyId, $days) {
        return $this->mapper->getUploadedPriceCountByUploaderTypeIdAndDays($uploaderType, $companyId, $days);
    }

    public function getPriceCountByDays($companyId, $days) {
        return $this->mapper->getPriceCountByDays($companyId, $days);
    }

    public function getAllPricesAfterTime($datetime, $companiesIds) {
        return $this->mapper->getAllPricesAfterTime($datetime, $companiesIds);
    }

    public function getCompanyPriceHours($uploadDateTime) {
        if (isset($uploadDateTime)) {
            return (time() - strtotime($uploadDateTime)) / 60 / 60;
        }
        return -1;
    }

    public function getPricePath($dto) {
        if (isset($dto)) {
            return DATA_DIR . "/service_companies_prices/" . $dto->getServiceCompanyId() . '/' . $dto->getFileName() . '.' . $dto->getFileExt();
        }
        return false;
    }

    public function getCompanyLastPriceMinutes($serviceCompanyId) {
        $dtos = $this->getCompanyLastPrices($serviceCompanyId);
        if (!empty($dtos)) {
            $dto = end($dtos);
            $udt = $dto->getUploadDateTime();
            return intval((time() - strtotime($udt)) / 60);
        }
        return -1;
    }

    public function getCompanyPriceColor($uploadDateTime) {
        $hours = $this->getCompanyPriceHours($uploadDateTime);
        if ($hours >= 0) {
            return $this->gradientColors(0xAA0000, 0x999999, intval($hours), 20);
        }
        return 0x999999;
    }

    function gradientColors($theColorBegin, $theColorEnd, $pStep, $theNumSteps) {
        if ($pStep > $theNumSteps) {
            $pStep = $theNumSteps;
        }
        $theR0 = ($theColorBegin & 0xff0000) >> 16;
        $theG0 = ($theColorBegin & 0x00ff00) >> 8;
        $theB0 = ($theColorBegin & 0x0000ff) >> 0;

        $theR1 = ($theColorEnd & 0xff0000) >> 16;
        $theG1 = ($theColorEnd & 0x00ff00) >> 8;
        $theB1 = ($theColorEnd & 0x0000ff) >> 0;

        $theR = $this->interpolate($theR0, $theR1, $pStep, $theNumSteps);
        $theG = $this->interpolate($theG0, $theG1, $pStep, $theNumSteps);
        $theB = $this->interpolate($theB0, $theB1, $pStep, $theNumSteps);

        $theVal = ((($theR << 8) | $theG) << 8) | $theB;
        return sprintf('#%06X', $theVal);
    }

    // return the interpolated value between pBegin and pEnd
    function interpolate($pBegin, $pEnd, $pStep, $pMax) {
        if ($pBegin < $pEnd) {
            return (($pEnd - $pBegin) * ($pStep / $pMax)) + $pBegin;
        } else {
            return (($pBegin - $pEnd) * (1 - ($pStep / $pMax))) + $pEnd;
        }
    }

    /**
     * 
     * @param type $companyId
     * @return int , -1 if no last price exists,
     * -2 if no previouse price exists
     * 1 if ok
     */
    public function removeCompanyLastPrice($companyId) {
        $lastPrices = $this->getCompanyLastPrices($companyId);
        if (empty($lastPrices) || !$this->priceFilesExists($lastPrices)) {
            return -1;
        }
        $companyPrevPrice = $this->getCompanyPrevPrice($companyId);
        if (empty($companyPrevPrice) || !$this->priceFilesExists($companyPrevPrice)) {
            return -2;
        }
        foreach ($lastPrices as $lastPrice) {
            $this->removeCompanyPrice($lastPrice->getId());
        }
        $prevPricePath = $this->getPriceFileFullPath($companyPrevPrice);
        $unzipPrevPriceFilesPaths = $this->unzipFile($prevPricePath);
        $this->removeCompanyPrice($companyPrevPrice->getId());
        foreach ($unzipPrevPriceFilesPaths as $upp) {
            $fileNamePartsArray = explode('.', $upp);
            $prevPriceExt = end($fileNamePartsArray);
            $fileNamePartsArray = explode('/', $upp);
            $prevPriceFileNameAndExt = end($fileNamePartsArray);
            $prevPriceFileNameAndExtExplodedByDot = explode('.', $prevPriceFileNameAndExt);
            $prevPriceFileName = $prevPriceFileNameAndExtExplodedByDot[0];
            $prevPricePathToBeRename = DATA_DIR . "/service_companies_prices/" . $companyId . '/' . $prevPriceFileName . '.' . $prevPriceExt;
            rename($upp, $prevPricePathToBeRename);
            $this->addCompanyPrice($companyId, $prevPriceFileName, $prevPriceExt, $companyPrevPrice->getUploaderType(), $companyPrevPrice->getUploaderId(), $companyPrevPrice->getUploadDateTime());
        }
        return 1;
    }

    public function getPriceFileFullPath($price, $includeFileExt = true) {
        return DATA_DIR . "/service_companies_prices/" . $price->getServiceCompanyId() . '/' . $price->getFileName() . (($includeFileExt == true) ? '.' . $price->getFileExt() : '');
    }

    private function priceFilesExists($dtos) {
        if (!is_array($dtos)) {
            $dtos = array($dtos);
        }
        foreach ($dtos as $dto) {
            $fileFullPath = $this->getPriceFileFullPath($dto);
            if (!file_exists($fileFullPath)) {
                return false;
            }
        }
        return true;
    }

    public function unzipFile($file) {
        $zip = new ZipArchive();
        $res = $zip->open($file);
        $extracted_path = DATA_TEMP_DIR . '/' . $this->random_string();
        if ($res === true) {
            $zip->extractTo($extracted_path);
            $zip->close();
            return $this->findPriceFilesInDirectory($extracted_path);
        } else {
            echo 'failed, code:' . $res;
        }
    }

    private function findPriceFilesInDirectory($path) {
        if ($handle = opendir($path)) {
            $ret = array();
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != "..") {
                    if (is_dir($path . '/' . $entry)) {
                        closedir($handle);
                        $ret = $this->findPriceFilesInDirectory($path . '/' . $entry);
                        if ($ret !== false) {
                            return $ret;
                        }
                        return false;
                    } else {
                        $ret [] = $path . '/' . $entry;
                    }
                }
            }
        }
        return $ret;
    }

    function random_string($chars = 32) {
        $letters = 'abcefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        return substr(str_shuffle($letters), 0, $chars);
    }

    public function downloadFile($pricePath, $downloadFileName) {

        if (file_exists($pricePath)) {
            $ff = explode('.', $pricePath);
            end($ff);
            $extension = current($ff);
            header("Content-disposition: attachment; filename=" . $downloadFileName . "." . $extension);
            header('Content-Description: File Transfer');
            $finfo = new finfo(FILEINFO_MIME);
            $type = $finfo->file($pricePath);
            header('Content-Type: ' . $type);
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: ' . filesize($pricePath));
            $fh = fopen($pricePath, "rb");
            while (!feof($fh)) {
                print(fread($fh, 4094));
            }
            fclose($fh);
        }
    }

    public function getCompaniesZippedPricesByDaysNumber($companyIdsArray, $dayNumber = null) {
        if (!is_array($companyIdsArray)) {
            $companyIdsArray = array($companyIdsArray);
        }
        return $this->mapper->getCompaniesZippedPricesByDaysNumber($companyIdsArray, $dayNumber);
    }

}

?>