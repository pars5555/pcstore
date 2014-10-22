<?php

require_once (CLASSES_PATH . "/managers/AbstractManager.class.php");
require_once (CLASSES_PATH . "/dal/mappers/UserMapper.class.php");
require_once (CLASSES_PATH . "/dal/mappers/CompanyMapper.class.php");
require_once (CLASSES_PATH . "/dal/mappers/ServiceCompanyMapper.class.php");
require_once (CLASSES_PATH . "/dal/mappers/AdminMapper.class.php");
require_once (CLASSES_PATH . "/users/UserGroups.class.php");
require_once (CLASSES_PATH . "/managers/UserPendingSubUsersManager.class.php");
require_once (CLASSES_PATH . "/managers/UserSubUsersManager.class.php");
require_once (CLASSES_PATH . "/managers/BonusHistoryManager.class.php");
require_once (CLASSES_PATH . "/managers/ServiceCompanyManager.class.php");

/**
 * UserManager class is responsible for creating,
 *
 * @author Vahagn Sookiasian
 * @package managers
 * @version 1.0
 */
class UserManager extends AbstractManager {

    /**
     * @var app config
     */
    private $config;
    private $companyMapper;

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
        $this->mapper = UserMapper::getInstance();
        $this->companyMapper = CompanyMapper::getInstance();
        $this->serviceCompanyMapper = ServiceCompanyMapper::getInstance();
        $this->serviceCompanyManager = ServiceCompanyManager::getInstance($config, $args);
        $this->adminMapper = AdminMapper::getInstance();
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

            self::$instance = new UserManager($config, $args);
        }
        return self::$instance;
    }

    public function isUserEmailInvitedByUser($userId, $invitedUserEmail) {
        return in_array($invitedUserEmail, $this->getUserPendingSubUsersEmailsArray($userId));
    }

    public function isSubUser($userId, $subUserId) {
        return in_array($subUserId, $this->getUserSubUsersIdsArray($userId));
    }

    public function getUserByEmailAndPassword($email, $password) {
        return $this->mapper->getUser($email, $password);
    }

    public function getUserByActivationCode($activationCode) {
        $dtos = $this->selectByField('activation_code', $activationCode);
        if (count($dtos) === 1) {
            return $dtos[0];
        }
        return null;
    }

    public function getUserByEmail($email) {
        $dtos = $this->selectByField('email', $email);
        if (count($dtos) === 1) {
            return $dtos[0];
        }
        return null;
    }

    public function getUserByInvitationCode($invitationCode) {
        $dtos = $this->selectByField('sub_users_registration_code', $invitationCode);
        if (count($dtos) === 1) {
            return $dtos[0];
        }
        return null;
    }

    public function setSubUser($invitation_code, $subUserId, $pendingEmail = null) {
        if (!empty($invitation_code)) {
            $userPendingSubUsersManager = new UserPendingSubUsersManager($this->config, $this->args);
            $inviterUserDto = $this->getUserByInvitationCode($invitation_code);
            if ($inviterUserDto) {
                if (isset($pendingEmail)) {
                    $invitedDto = $userPendingSubUsersManager->getByUserIdAndPendingSubUserEmail($inviterUserDto->getId(), $pendingEmail);
                    if ($invitedDto) {
                        $userPendingSubUsersManager->removePendingSubUserFromUser($inviterUserDto->getId(), $pendingEmail);
                    }
                }
                $userSubUsersManager = UserSubUsersManager::getInstance($this->config, $this->args);
                $userSubUsersManager->addSubUserToUser($subUserId, $inviterUserDto->getId());
                return $inviterUserDto->getId();
            }
        }
        return 0;
    }

    public function getCustomerByEmailAndPassword($email, $password) {
        $user = $this->getUserByEmailAndPassword($email, $password);
        if ($user == null) {
            $user = $this->companyMapper->getCompany($email, $password);
        }
        if ($user == null) {
            $user = $this->serviceCompanyManager->getServiceCompanyByEmailAndPassword($email, $password);
        }
        if ($user == null) {
            $user = $this->adminMapper->getAdmin($email, $password);
        }
        return $user;
    }

    public function getCustomerByEmail($email) {
        $user = $this->getUserByEmail($email);
        if ($user == null) {
            $user = $this->companyMapper->getCompanyByEmail($email);
        }
        if ($user == null) {
            $user = $this->serviceCompanyManager->getServiceCompanyByEmail($email);
        }
        if ($user == null) {
            $user = $this->adminMapper->getAdminByEmail($email);
        }
        return $user;
    }

    public function getAllUsersEmails() {
        $dtos = $this->mapper->getAllUsersEmails();
        $ret = array();
        foreach ($dtos as $key => $dto) {
            $ret[] = $dto->getEmail();
        }
        return $ret;
    }

    public function enableSound($userId, $value) {
        $this->mapper->updateNumericField($userId, 'sound_on', $value);
    }

    /**
     * this function is for admin only
     * return all registered users with online status
     * @return type
     */
    public function getAllUsersFull() {
        return $this->mapper->getAllUsersFull();
    }

    public function getRealEmailAddress($userId) {
        $userDto = $this->mapper->selectByPK($userId);
        if ($userDto) {
            return $this->getRealEmailAddressByUserDto($userDto);
        }
        return '';
    }

    public function getRealEmailAddressByUserDto($userDto) {
        if ($userDto) {
            if ($userDto->getLoginType() !== 'pcstore') {
                $socialProfile = $userDto->getSocialProfile();
                $socialProfile = json_decode($socialProfile);
                if (!empty($socialProfile)) {
                    $email = $socialProfile->email;
                }
            } else {
                $email = $userDto->getEmail();
            }
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return $email;
            }
        }
        return null;
    }

    /**
     * Returns user's phones array
     */
    public function getUserPhonesArray($user_id) {
        $user = $this->mapper->selectByPK($user_id);
        $userPhones = $user->getPhones();
        $userPhonesArray = array();
        if (strlen($userPhones) > 0) {
            $userPhonesArray = explode(",", $userPhones);
        }
        return $userPhonesArray;
    }

    public function setSubUsersRegistrationCode($userId, $value) {
        $this->mapper->setSubUsersRegistrationCode($userId, $value);
    }

    public function updateUserProfileFieldsById($id, $name, $lname, $change_pass, $new_pass, $phone1, $phone2, $phone3, $address, $region, $enable_vip = 0) {
        $userDto = $this->selectByPK($id);
        $userDto->setName($name);
        $userDto->setLastName($lname);
        if ($change_pass) {
            $userDto->setPassword($new_pass);
        }
        $phones = $phone1 ? $phone1 : "";
        if ($phone2) {
            if (strlen($phones) > 0) {
                $phones .= "," . $phone2;
            } else {
                $phones = $phone2;
            }
        }
        if ($phone3) {
            if (strlen($phones) > 0) {
                $phones .= "," . $phone3;
            } else {
                $phones = $phone3;
            }
        }

        $userDto->setPhones($phones);
        $userDto->setAddress($address);
        $userDto->setRegion($region);
        $userDto->setEnableVip($enable_vip == 1 ? 1 : 0);
        $this->mapper->updateByPK($userDto);
    }

    /**
     * 
     * @param type $customerDto (adminDto userDto or CompanyDto)
     */
    public function getCustomerTypeStringFromCustomerDto($customerDto) {
        if ($customerDto instanceof UserDto) {
            return 'user';
        } elseif ($customerDto instanceof CompanyDto) {
            return 'company';
        } elseif ($customerDto instanceof ServiceCompanyDto) {
            return 'service_company';
        } elseif ($customerDto instanceof AdminDto) {
            return 'admin';
        }
        return 'unknown';
    }

    /**
     * 
     * @param type $customerDto (adminDto userDto or CompanyDto)
     */
    public static function getCustomerTypeFromDto($customerDto) {
        if ($customerDto instanceof UserDto) {
            return UserGroups::$USER;
        } elseif ($customerDto instanceof CompanyDto) {
            return UserGroups::$COMPANY;
        } elseif ($customerDto instanceof ServiceCompanyDto) {
            return UserGroups::$SERVICE_COMPANY;
        } elseif ($customerDto instanceof AdminDto) {
            return UserGroups::$ADMIN;
        }
        return null;
    }

    public function getCustomerType($email, $pass) {
        if (!isset($email) && empty($email)) {
            return UserGroups::$GUEST;
        }
        if ($this->mapper->getUser($email, $pass)) {
            return UserGroups::$USER;
        }
        if ($this->companyMapper->getCompany($email, $pass)) {
            return UserGroups::$COMPANY;
        }
        if ($this->serviceCompanyManager->getServiceCompanyByEmailAndPassword($email, $pass)) {
            return UserGroups::$SERVICE_COMPANY;
        }
        if ($this->adminMapper->getAdmin($email, $pass)) {
            return UserGroups::$ADMIN;
        }
        return UserGroups::$GUEST;
    }

    public function getCustomerTypeByEmail($email = null) {
        if (!isset($email) && empty($email)) {
            return UserGroups::$GUEST;
        }
        if ($this->getUserByEmail($email)) {
            return UserGroups::$USER;
        }
        if ($this->companyMapper->getCompanyByEmail($email)) {
            return UserGroups::$COMPANY;
        }
        if ($this->serviceCompanyManager->getServiceCompanyByEmail($email)) {
            return UserGroups::$SERVICE_COMPANY;
        }
        if ($this->adminMapper->getAdminByEmail($email)) {
            return UserGroups::$ADMIN;
        }
        return UserGroups::$GUEST;
    }

    public function checkPassword($pass) {

        return preg_match($this->getCmsVar("password_regexp"), $pass);
    }

    public function updateUserHash($uId) {
        $hash = $this->generateHash($uId);
        $userDto = $this->mapper->createDto();
        $userDto->setId($uId);
        $userDto->setHash($hash);
        $this->mapper->updateByPK($userDto);
        return $hash;
    }

    public function setUserSocialProfile($userId, $profile) {
        $userDto = $this->mapper->selectByPK($userId);
        if ($userDto) {
            $userDto->setSocialProfile($profile);
            $this->mapper->updateByPK($userDto);
            return true;
        }
        return false;
    }

    public function setActive($userId) {
        $userDto = $this->mapper->selectByPK($userId);
        if ($userDto) {
            $userDto->setActive(1);
            $this->mapper->updateByPK($userDto);
            return true;
        }
        return false;
    }

    public function createUser($email, $pass, $name = '', $phone = '', $lname = '', $loginType = 'pcstore') {

        $userDto = $this->mapper->createDto();
        $userDto->setEmail($email);
        $userDto->setPassword($pass);
        $userDto->setName($name);
        $userDto->setLastName($lname);
        $userDto->setPhones($phone);
        $userDto->setLoginType($loginType);
        $userDto->setSubUsersRegistrationCode(uniqid());
        $userDto->setHash($this->generateHash(uniqid()));
        $userDto->setRegisteredDate($today = date("Ymd"));
        $userDto->setActivationCode(uniqid() . uniqid());
        return $this->mapper->insertDto($userDto);
    }

    public function generateHash($id) {
        return md5($id * time() * 19);
    }

    public function validate($id, $hash) {
        return $this->mapper->validate($id, $hash);
    }

    public function setLastSmsValidationCode($userId, $code) {
        $userDto = $this->mapper->selectByPK($userId);
        $userDto->setLastSmsValidationCode($code);
        return $this->mapper->updateByPK($userDto);
    }

    public function addUserPoints($userId, $points, $description = '') {
        $userDto = $this->mapper->selectByPK($userId);
        if (!$userDto || intval($points) <= 0) {
            return false;
        }
        $bonusHistoryManager = BonusHistoryManager::getInstance($this->config, $this->args);
        $bonusHistoryManager->addRow($userId, $points, $description);
        $points += intval($userDto->getPoints());
        $this->mapper->updateNumericField($userId, 'points', $points);
    }

    public function subtractUserPoints($userId, $points, $description = '') {
        $userDto = $this->mapper->selectByPK($userId);
        if (!$userDto || intval($points) <= 0) {
            return false;
        }
        $bonusHistoryManager = BonusHistoryManager::getInstance($this->config, $this->args);
        $bonusHistoryManager->addRow($userId, -$points, $description);
        $points = intval($userDto->getPoints()) - $points;
        $this->mapper->updateNumericField($userId, 'points', $points);
    }

    public function setLanguageCode($id, $lc) {
        $this->mapper->updateTextField($id, 'language_code', $lc);
    }

    public function isVipAndVipEnabled($userDto) {
        return $userDto->getVip() == 1 && $userDto->getEnableVip() == 1;
    }

    public function isVip($userId) {
        $user = $this->selectByPK($userId);
        if (isset($user)) {
            return $user->getVip() == 1;
        }
        return false;
    }

    public function setLastPingToNow($id) {
        $this->mapper->updateTextField($id, 'last_ping', date('Y-m-d H:i:s'));
    }

    public function deleteUserAndDependencies($id) {
        $userDto = $this->selectByPK($id);
        if ($userDto) {
            $email = $userDto->getEmail();
            $this->deleteByPK($id);
            $userSubUsersManager = UserSubUsersManager::getInstance($this->config, $this->args);
            $userSubUsersManager->deleteByField('user_id', $id);
            $userPendingSubUsersManager = UserPendingSubUsersManager::getInstance($this->config, $this->args);
            $userPendingSubUsersManager->deleteByField('user_id', $id);
            require_once (CLASSES_PATH . "/managers/CustomerCartManager.class.php");
            $customerCartManager = CustomerCartManager::getInstance($this->config, $this->args);
            $customerCartManager->deleteByField('email', $email);
            require_once (CLASSES_PATH . "/managers/CustomerLocalEmailsManager.class.php");
            $customerLocalEmailsManager = CustomerLocalEmailsManager::getInstance($this->config, $this->args);
            $customerLocalEmailsManager->deleteByField('customer_email', $email);
            require_once (CLASSES_PATH . "/managers/CompanyDealersManager.class.php");
            $companyDealersManager = CompanyDealersManager::getInstance($this->config, $this->args);
            $companyDealersManager->deleteByField('user_id', $id);
        }
    }

}

?>