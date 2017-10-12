<?php
/**
 *  Copyright (c) 2017 Teuber Consult + IT GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <alexander.scherer@teuber-consult.de>, 2017
 *
 */
require_once 'libs/basic/model.php';

class Filestorage extends Model{
    public $_table = 'filestorage';
    public $_caching = false;

    public $module = '';
    public $name = '';
    public $type = '';
    public $size = 0;
    public $content = '';
    public $date = 0;
    public $user;

    protected function bootClasses()
    {
        $this->user = new User($this->user);
    }

    public static function checkExists($module, $name, $type, $size)
    {
        return self::fetchSingle([
            [
                "column" => "module",
                "value" => $module
            ],
            [
                "column" => "name",
                "value" => $name
            ],
            [
                "column" => "type",
                "value" => $type
            ],
            [
                "column" => "size",
                "value" => $size
            ]
        ]);
    }

    public function getDlLink()
    {
        return 'libs/modules/filestorage/filestorage.get.php?id='.$this->getId();
    }

    /**
     * @return string
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * @param string $module
     */
    public function setModule($module)
    {
        $this->module = $module;
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
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param int $size
     */
    public function setSize($size)
    {
        $this->size = $size;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @return int
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param int $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }
}