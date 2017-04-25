<?php
/**
 *  Copyright (c) 2017 Teuber Consult + IT GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <alexander.scherer@teuber-consult.de>, 2017
 *
 */
require_once 'libs/basic/model.php';
require_once 'textblock.groups.class.php';

class TextBlock extends Model{
    public $_table = 'textblocks';

    public $name = "";
    public $text = "";
    public $crtdate = 0;
    public $crtuser = 0;
    public $uptdate = 0;
    public $uptuser = 0;

    public $mod_ticket = 0;
    public $mod_mail = 0;

    public $_groups = [];

    protected function bootClasses()
    {
        $this->_groups = TextBlockGroup::getAllForTextblock($this);
        $this->crtuser = new User($this->crtuser);
        $this->uptuser = new User($this->uptuser);
    }

    /**
     * @param array $filterarray
     * @param int $single
     * @return TextBlock[]
     */
    public static function fetch($filterarray = Array(), $single = 0)
    {
        return parent::fetch($filterarray, $single);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * @return TextBlockGroup[]
     */
    public function getGroups()
    {
        return $this->_groups;
    }

    /**
     * @param TextBlockGroup[] $groups
     */
    public function setGroups($groups)
    {
        $this->_groups = $groups;
    }

    /**
     * @return int
     */
    public function getCrtdate()
    {
        return $this->crtdate;
    }

    /**
     * @param int $crtdate
     */
    public function setCrtdate($crtdate)
    {
        $this->crtdate = $crtdate;
    }

    /**
     * @return User
     */
    public function getCrtuser()
    {
        return $this->crtuser;
    }

    /**
     * @param User $crtuser
     */
    public function setCrtuser($crtuser)
    {
        $this->crtuser = $crtuser;
    }

    /**
     * @return int
     */
    public function getUptdate()
    {
        return $this->uptdate;
    }

    /**
     * @param int $uptdate
     */
    public function setUptdate($uptdate)
    {
        $this->uptdate = $uptdate;
    }

    /**
     * @return User
     */
    public function getUptuser()
    {
        return $this->uptuser;
    }

    /**
     * @param User $uptuser
     */
    public function setUptuser($uptuser)
    {
        $this->uptuser = $uptuser;
    }

    /**
     * @return int
     */
    public function getModTicket()
    {
        return $this->mod_ticket;
    }

    /**
     * @param int $mod_ticket
     */
    public function setModTicket($mod_ticket)
    {
        $this->mod_ticket = $mod_ticket;
    }

    /**
     * @return int
     */
    public function getModMail()
    {
        return $this->mod_mail;
    }

    /**
     * @param int $mod_mail
     */
    public function setModMail($mod_mail)
    {
        $this->mod_mail = $mod_mail;
    }
}