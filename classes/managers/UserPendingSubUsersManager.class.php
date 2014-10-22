<?php

require_once (CLASSES_PATH . "/managers/AbstractManager.class.php");
require_once (CLASSES_PATH . "/dal/mappers/UserPendingSubUsersMapper.class.php");

/**
 * UserPendingSubUsersManager class is responsible for creating,
 *
 * @author Vahagn Sookiasian
 * @package managers
 * @version 1.0
 */
class UserPendingSubUsersManager extends AbstractManager {

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
        $this->mapper = UserPendingSubUsersMapper::getInstance();
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

            self::$instance = new UserPendingSubUsersManager($config, $args);
        }
        return self::$instance;
    }

    public function getByUserIdOrderByDate($userId) {
        return $this->mapper->getByUserIdOrderByDate($userId);
    }

    public function getByUserIdAndPendingSubUserEmail($userId, $pendingUserEmail) {
        return $this->mapper->getByUserIdAndPendingSubUserEmail($userId, $pendingUserEmail);
    }

    public function removePendingSubUserFromUser($userId, $pendingUserEmail) {
        $dto = $this->getByUserIdAndPendingSubUserEmail($userId, $pendingUserEmail);
        if ($dto) {
            $this->mapper->deleteByPK($dto->getId());
            return true;
        }
        return false;
    }

    public function addPendingSubUserEmailToUser($pendingUserEmail, $userId) {
        $dto = $this->getByUserIdAndPendingSubUserEmail($userId, $pendingUserEmail);
        if (!$dto) {
            $dto = $this->mapper->createDto();
            $dto->setUserId($userId);
            $dto->setPendingSubUserEmail($pendingUserEmail);
            $dto->setLastSent(date('Y-m-d H:i:s'));
            $dto->setTimestamp(date('Y-m-d H:i:s'));
            $this->mapper->insertDto($dto);
            return true;
        }
        return false;
    }

}

?>