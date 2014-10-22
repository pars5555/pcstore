<?php

require_once (CLASSES_PATH . "/managers/AbstractManager.class.php");
require_once (CLASSES_PATH . "/dal/mappers/ImportItemsTempMapper.class.php");
require_once (CLASSES_PATH . "/managers/ImportPriceManager.class.php");
require_once (CLASSES_PATH . "/managers/ItemManager.class.php");

/**
 * AdminManager class is responsible for creating,
 *
 * @author Vahagn Sookiasian
 * @package managers
 * @version 1.0
 */
class ImportItemsTempManager extends AbstractManager {

    /**
     * @var app config
     */
    private $config;
    public static $ALL_BRANS = array("Asus", "Mitsubishi", "Toshiba", "Dell", "Acer", "AOC", "Elixir", "Mercury", "Lenovo", "Apple", " I-Nix", "Gigabyte", "Dany",
        "Eton", "MyGica", "Chicony", "Sony", "HP", "Compaq", "Samsung", "Gateway", "MSI", "Fujitsu", "Point Of View", "POV", "Hyundai",
        "Viewsonic", "MID", "Seagate", "Western Digital", "WD", "Hitachi", "Maxtor", "Corsair", "OCZ", "Kingston", "Intel", "Patriot",
        "Plextor", "Super Talent", "NCP", "Apacer", "Goodram", "Twinmos", "Silicon Power", "RCM", "Hynix", "Gskill", "Adata", "AMD",
        "Zalman", "Invader", "Spire", "Deep Cool", "GlacialTech", "Coller Master", "Thermaltake", "cooler master", "xilence", "LG",
        "ViewSonic", "Philips", "Benq", "Xerox", "Envision", "Topview", "Pioneer", "Panasonic", "Lite-on", "Afox", "Palit", "Zotac",
        "XFX", "Power color", "Galaxy", "Macy", "Sapphire", "Sparkle", "Gainward", "Forca", "EVGA", "Creative", "Light Wave", "Elitegroup",
        "ECS", "Asrock", "Biostar", "Foxconn", "D-Tech", "Starmax", "Elvision", "Canyon", "Genius", "Logitech", "Manhattan", "I-NIX",
        "Intex", "Microsoft", "D-Tech", "A4tech", "Rocentech", "Targus", "Rapoo", "Premium", "FAST", "Hair", "Belkin", "JBL", "Camac",
        "Ucom", "Beats by dr", "techCom", "powertrain", "codegen", "FSP", "Antec", "corsair", "ARCHOS",
        "Enermax", "QoRi", "GF", "Super color", "Light Wave", "Analog", "Kworld", "Pinnacle", "Epson", "Xerox", "Lexmark", "Tp-Link",
        "Atlas", "Cnet", "D-Link", "Gembird", "Encore", "Transcend", "Imation", "Sandisk", "team", "Emtec", "e-Pro",
        "Tripplite", "APC", "Optoma", "Epson", "cusei", "Sungale", "Nikon", "Canon", "Fuji", "Pentax", "Prestigio",
        "inno3D", "ATI", "Crucial", "ZTE");
    public static $ALL_BRANDS_LOWER_CASE = array();

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
        $this->mapper = ImportItemsTempMapper::getInstance();
        $this->config = $config;
        $this->args = $args;
        foreach (self::$ALL_BRANS as $brand) {
            self::$ALL_BRANDS_LOWER_CASE[] = strtolower($brand);
        }
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

            self::$instance = new ImportItemsTempManager($config, $args);
        }
        return self::$instance;
    }

    public function getUserCurrentRows($customerLogin) {
        return $this->mapper->getUserCurrentRows($customerLogin);
    }

    public function getUserCurrentPriceNewRows($customerLogin) {
        return $this->mapper->getUserCurrentPriceNewRows($customerLogin);
    }

    public function getUserCurrentPriceChangedRows($customerLogin) {
        return $this->mapper->getUserCurrentPriceChangedRows($customerLogin);
    }

    public function setMatchedItemId($id, $itemId) {
        $this->mapper->updateNumericField($id, 'matched_item_id', $itemId);
    }

    public function addRow($customerLogin, $modelColumn, $nameColumn, $dealerPriceColumn, $dealerPriceAmdColumn, $vatPriceColumn, $vatPriceAmdColumn, $warrantyMonthColumn, $warrantyYearColumn, $brandColumn) {
        $itemManager = ItemManager::getInstance($this->config, $this->args);
        $modelColumn = trim(preg_replace('/\s+/', ' ', $modelColumn));
        $nameColumn = trim(preg_replace('/\s+/', ' ', $nameColumn));
        $warrantyMonthColumn = trim(preg_replace('/\s+/', ' ', $warrantyMonthColumn));
        $warrantyYearColumn = trim(preg_replace('/\s+/', ' ', $warrantyYearColumn));
        $brandColumn = trim(preg_replace('/\s+/', ' ', $brandColumn));

        $dto = $this->mapper->createDto();
        $dto->setLogin($customerLogin);
        $dto->setDisplayName($nameColumn);


        $dto->setOriginalDisplayName($nameColumn);
        if (isset($modelColumn)) {
            $dto->setModel($modelColumn);
            $dto->setOriginalModel($modelColumn);
        }
        if (isset($dealerPriceColumn)) {
            $dto->setDealerPrice(ImportPriceManager::convertCurrencyToDollar($dealerPriceColumn));
            $dto->setOriginalDealerPrice($dealerPriceColumn);
        }
        if (isset($dealerPriceAmdColumn)) {
            $AMDprice = ImportPriceManager::convertCurrencyToAmd($dealerPriceAmdColumn);
            $dto->setDealerPriceAmd($AMDprice);
            $dto->setOriginalDealerPriceAmd($dealerPriceAmdColumn);
            if (!isset($dealerPriceColumn)) {
                $dealerPriceUSD = $itemManager->exchangeFromAMDToUSD($AMDprice);
                $dto->setDealerPrice($dealerPriceUSD);
            }
        }

        if (isset($vatPriceColumn)) {
            $usdPrice = ImportPriceManager::convertCurrencyToDollar($vatPriceColumn);
            $dto->setVatPrice($usdPrice);
            $dto->setOriginalVatPrice($vatPriceColumn);
            if (!isset($dealerPriceColumn)) {
                $dto->setDealerPrice($usdPrice);
                $dto->setOriginalDealerPrice($vatPriceColumn);
            }
        }
        if (isset($vatPriceAmdColumn)) {
            $ADMprice = ImportPriceManager::convertCurrencyToAmd($vatPriceAmdColumn);
            $dto->setVatPriceAmd($ADMprice);
            $dto->setOriginalVatPriceAmd($vatPriceAmdColumn);
            if (!isset($dealerPriceAmdColumn)) {
                $dto->setDealerPriceAmd($ADMprice);
                $dto->setOriginalDealerPriceAmd($vatPriceAmdColumn);
            }
            if (!isset($vatPriceColumn) && !isset($dealerPriceColumn)) {
                $dealerPriceUSD = $itemManager->exchangeFromAMDToUSD($ADMprice);
                $dto->setDealerPrice($dealerPriceUSD);
                $dto->setVatPrice($dealerPriceUSD);
            }
        }



        if (!empty($brandColumn)) {
            $brand = $this->findBrandFromItemTitle($brandColumn);
            $dto->setBrand($brand);
            $dto->setOriginalBrand($brandColumn);
        } else {
            $brand = $this->findBrandFromItemTitle($nameColumn);
            $dto->setBrand($brand);
        }
        if (isset($warrantyMonthColumn)) {
            $dto->setWarrantyMonths(ImportPriceManager::convertWarrantyMonthsFieldToWarrantyMonths($warrantyMonthColumn));
            $dto->setOriginalWarranty($warrantyMonthColumn);
        }
        if (isset($warrantyYearColumn)) {
            $dto->setWarrantyMonths(ImportPriceManager::convertWarrantyYearsFieldToWarrantyMonths($warrantyYearColumn));
            $dto->setOriginalWarranty($warrantyYearColumn);
        }

        $this->mapper->insertDto($dto, false);
    }

    public function findBrandFromItemTitle($itemTitle) {
        $title = strtolower($itemTitle);
        foreach (self::$ALL_BRANDS_LOWER_CASE as $brand) {
            if (preg_match('/\b' . $brand . '\b/', $title))
                return ucfirst($brand);
        }
        return '';
    }

    public function findModelFromItemTitle($itemTitle) {
        $words = preg_split('/(?!\w)\b/', $itemTitle);
        foreach ($words as $word) {
            $word = trim($word);
            if (preg_match("/^(?=.*[a-zA-Z])(?=.*[0-9])/", $word)) {
                return $word;
            }
        }
        return null;
    }

    public function deleteCustomerRows($login) {
        return $this->mapper->deleteCustomerRows($login);
    }

    public function importToItemsTable($login, $company_id, $new_items_row_ids, $changed_rows_ids) {
        $allPriceRows = $this->getUserCurrentRows($login);
        $itemManager = ItemManager::getInstance($this->config, $this->args);
        $newItemsCount = 0;
        $updatedItemsCount = 0;
        $priceOrderIndex = 1;
        foreach ($allPriceRows as $priceRow) {
            $priceOrderIndex+=5;
            $catIds = null;
            $simillarItemId = intval($priceRow->getSimillarItemId());
            if ($simillarItemId > 0) {
                //item is new and is simillar to another item
                $simillarItemDto = $itemManager->selectByPK($simillarItemId);
                $priceRow->getShortSpec($simillarItemDto->getShortDescription());
                $priceRow->getFullSpec($simillarItemDto->getFullDescription());
                $catIdsStr = trim($simillarItemDto->getCategoriesIds(), ',');
                if (!empty($catIdsStr)) {
                    $catIds = explode(',', $catIdsStr);
                }
            }
            if ($priceRow->getMatchedItemId() > 0 && in_array($priceRow->getId(), $changed_rows_ids)) {
                // item is changes
                $editedItemId = intval($priceRow->getMatchedItemId());
                $itemId = $editedItemId;
                $itemManager->updateItem($editedItemId, $this->secure($priceRow->getDisplayName()), $priceRow->getShortSpec(), $priceRow->getFullSpec(), $priceRow->getWarrantyMonths(), $priceRow->getDealerPrice(), $priceRow->getVatPrice(), $priceRow->getDealerPriceAmd(), $priceRow->getVatPriceAmd(), $company_id, $priceRow->getModel(), $priceRow->getBrand(), $catIds, null, $priceOrderIndex, $login);
                $itemManager->setItemHidden($editedItemId, 0);
                $updatedItemsCount++;
            } elseif (in_array($priceRow->getId(), $new_items_row_ids)) {
                //item is new
                $rootCategoryId = $priceRow->getRootCategoryId();
                $subCategoriesIds = trim($priceRow->getSubCategoriesIds());
                $subCategoriesIdsArray = array();
                if (!empty($subCategoriesIds)) {
                    $subCategoriesIdsArray = explode(',', $subCategoriesIds);
                }               
                $catIds = array_merge(array($rootCategoryId), $subCategoriesIdsArray);
                $itemId = $itemManager->addItem($this->secure($priceRow->getDisplayName()), $priceRow->getShortSpec(), $priceRow->getFullSpec(), $priceRow->getWarrantyMonths(), $priceRow->getDealerPrice(), $priceRow->getVatPrice(), $priceRow->getDealerPriceAmd(), $priceRow->getVatPriceAmd(), $company_id, $priceRow->getModel(), $priceRow->getBrand(), $catIds, date('Y-m-d'), $priceOrderIndex, $login);
                $newItemsCount++;
            }
            if ($simillarItemId > 0) {
                $itemDto = $itemManager->selectByPK($itemId);
                $itemManager->copyItemPictures($simillarItemDto, $itemDto);
            }
            $itemPictureName = "";
            $itemPicturePath = HTDOCS_TMP_DIR . '/import';
            if (file_exists(HTDOCS_TMP_DIR . '/import/' . $priceRow->getId() . '.png')) {
                $itemPictureName = $priceRow->getId() . '.png';
            }
            if (file_exists(HTDOCS_TMP_DIR . '/import/' . $priceRow->getId() . '.jpg')) {
                $itemPictureName = $priceRow->getId() . '.jpg';
            }
            if (!empty($itemPictureName)) {
                $itemDto = $itemManager->selectByPK($itemId);
                $itemManager->addPicture($itemDto, null, $itemPicturePath . '/' . $itemPictureName);
            }
        }
        return array($newItemsCount, $updatedItemsCount);
    }

}

?>