<?php

require_once (CLASSES_PATH . "/managers/AbstractManager.class.php");
require_once (CLASSES_PATH . "/dal/mappers/UserSubUsersMapper.class.php");

/**
 * UserSubUsersManager class is responsible for creating,
 *
 * @author Vahagn Sookiasian
 * @package managers
 * @version 1.0
 */
class UserSubUsersManager extends AbstractManager {

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
        $this->mapper = UserSubUsersMapper::getInstance();
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

            self::$instance = new UserSubUsersManager($config, $args);
        }
        return self::$instance;
    }

    public function getUserSubUsers($userId) {
        return $this->mapper->getUserSubUsers($userId);
    }

    public function removeSubUserFromUser($subUserId, $userId) {
        $dto = $this->getByUserIdAndSubUserId($userId, $subUserId);
        if ($dto) {
            $this->mapper->deleteByPK($dto->getId());
            return true;
        }
        return false;
    }

    public function addSubUserToUser($subUserId, $userId) {
        $dto = $this->getByUserIdAndSubUserId($userId, $subUserId);
        if (!$dto) {
            $dto = $this->mapper->createDto();
            $dto->setUserId($userId);
            $dto->setSubUserId($subUserId);
            $dto->setTimestamp(date('Y-m-d H:i:s'));
            $this->mapper->insertDto($dto);
            return true;
        }
        return false;
    }

    public function getByUserIdAndSubUserId($userId, $subUserId) {

        return $this->mapper->getByUserIdAndSubUserId($userId, $subUserId);
    }

    public function getUserParentId($subUserId) {
        $dto = $this->mapper->getUserParentId($subUserId);
        if ($dto) {
            return $dto->getUserId();
        } else {
            return 0;
        }
    }

    public function getRowsAddedAfterGivenDatetime($userId, $datetime) {
        return $this->mapper->getRowsAddedAfterGivenDatetime($userId, $datetime);
    }

}

?>