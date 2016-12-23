<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */
require_once 'libs/basic/model.php';

class Link extends Model{
    public $_table = 'links';

    public $title = '';
    public $url = '';
    public $user = NULL;
    public $private = 1;
    public $username = '';
    public $password = '';

    protected function bootClasses()
    {
        $this->user = new User($this->user);
    }

    /**
     * @return Link[]
     */
    public static function getAllPublic()
    {
        $retval = self::fetch([
            [
                'column'=>'private',
                'value'=>0
            ],
            [
                'orderby'=>'title',
                'orderbydir'=>'asc'
            ]
        ]);
        return $retval;
    }

    /**
     * @return Link[]
     */
    public static function getAllPrivate()
    {
        global $_USER;
        $retval = self::fetch([
            [
                'column'=>'private',
                'value'=>1
            ],
            [
                'column'=>'user',
                'value'=>$_USER->getId()
            ],
            [
                'orderby'=>'title',
                'orderbydir'=>'asc'
            ]
        ]);
        return $retval;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return int
     */
    public function getPrivate()
    {
        return $this->private;
    }

    /**
     * @param int $private
     */
    public function setPrivate($private)
    {
        $this->private = $private;
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

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }
}