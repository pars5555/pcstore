<?php

require_once (CLASSES_PATH . "/loads/GuestLoad.class.php");

/**
 *
 * @author Vahagn Sookiasian
 *
 */
class FooterLinksContentLoad extends GuestLoad {

    public $pages = array("help" => "help", "register" => "register", "invite" => "invite_friends", "about" => "about_pcstore", "policy" => "privacy_policy");

    public function load() {
        $loadIndex = $this->args['selected_footer_link_load_index'];
        switch ($loadIndex) {
            case 'help':
                $this->addParam('translation_number', 398);
                $this->setDescriptionTagValue('pcstore help!');
                $this->setTitleTagValue('PcStore.am help');
                $this->setKeywordsTagValue('pcstore help');
                break;
            case 'register':
                $this->addParam('translation_number', 395);
                $this->setDescriptionTagValue('How to register on pcstore');
                $this->setTitleTagValue('How to register');
                $this->setKeywordsTagValue('Register on pcstore.am, pcstore.am registration');
                break;
            case 'policy':
                $this->addParam('translation_number', 394);
                $this->setDescriptionTagValue('Pcstore.am privacy/policy');
                $this->setTitleTagValue('Privacy/policy');
                $this->setKeywordsTagValue('Pcstore.am privacy/policy');
                break;
            case 'invite':
                $this->addParam('translation_number', 396);
                $this->setDescriptionTagValue('How to invite friend on pcstore.am and make money with them');
                $this->setTitleTagValue('Invite friends and Make money!');
                $this->setKeywordsTagValue('Make money on pcstore.am, Invite friends and make money');
                break;
            case 'about':
                $this->addParam('translation_number', 397);
                $this->setDescriptionTagValue('About Pcstore.am');
                $this->setTitleTagValue('About Pcstore.am');
                $this->setKeywordsTagValue('About pcstore.am');
                break;
            default:
                break;
        }
    }

    public function getDefaultLoads($args) {
        $loads = array();
        return $loads;
    }

    public function isValidLoad($namespace, $load) {
        return true;
    }

    public function getTemplate() {
        return TEMPLATES_DIR . "/static/footer_links_content.tpl";
    }

    public function getRequestGroup() {
        return RequestGroups::$guestRequest;
    }

}

?>