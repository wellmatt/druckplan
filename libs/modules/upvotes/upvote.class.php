<?php
/**
 *  Copyright (c) 2017 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2017
 *
 */
require_once 'libs/basic/model.php';

class UpVote extends Model{
    public $_table = 'upvotes';

    public $title = '';
    public $description = '';
    public $customer;
    public $crtuser;
    public $crtdate = 0;
    public $upvotes = 0;
    public $downvotes = 0;

    protected function bootClasses()
    {
        $this->customer = new BusinessContact($this->customer);
        $this->crtuser = new User($this->crtuser);
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
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return BusinessContact
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @param BusinessContact $customer
     */
    public function setCustomer($customer)
    {
        $this->customer = $customer;
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
     * @return int
     */
    public function getUpvotes()
    {
        return $this->upvotes;
    }

    /**
     * @param int $upvotes
     */
    public function setUpvotes($upvotes)
    {
        $this->upvotes = $upvotes;
    }

    /**
     * @return int
     */
    public function getDownvotes()
    {
        return $this->downvotes;
    }

    /**
     * @param int $downvotes
     */
    public function setDownvotes($downvotes)
    {
        $this->downvotes = $downvotes;
    }
}