<?php

require_once (CLASSES_PATH . "/managers/AbstractManager.class.php");
require_once (CLASSES_PATH . "/dal/mappers/CategoriesDefaultPicturesMapper.class.php");

/**
 * OrdersManager class is responsible for creating,
 *
 * @author Vahagn Sookiasian
 * @package managers
 * @version 1.0
 */
class CategoriesDefaultPicturesManager extends AbstractManager {

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
    private $categoryIdPictureNameMap;

    /**
     * Initializes DB mappers
     *
     * @param object $config
     * @param object $args
     * @return
     */
    function __construct($config, $args) {
        $this->mapper = CategoriesDefaultPicturesMapper::getInstance();
        $this->config = $config;
        $this->args = $args;
        $this->cacheTable();
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

            self::$instance = new CategoriesDefaultPicturesManager($config, $args);
        }
        return self::$instance;
    }

    public function getItemDefaultPictureByCategoriesIds($categoriesIds) {
        if (!is_array($categoriesIds)) {
            $categoriesIds = trim($categoriesIds, ',');
            $categoriesIds = explode(',', $categoriesIds);
        }
        array_reverse($categoriesIds);
        foreach ($categoriesIds as $categoryId) {
            if (array_key_exists(intval($categoryId), $this->categoryIdPictureNameMap)) {
                return $this->categoryIdPictureNameMap[intval($categoryId)];
            }
        }
        return null;
    }

    public function cacheTable() {
        $this->categoryIdPictureNameMap = array();
        $allDtos = $this->selectAll();
        foreach ($allDtos as $dto) {
            $this->categoryIdPictureNameMap[intval($dto->getCategoryId())] = $dto->getPictureFileName();
        }
    }

}

?>