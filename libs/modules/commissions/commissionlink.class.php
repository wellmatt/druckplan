<?php
/**
 *  Copyright (c) 2017 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2017
 *
 */

require_once 'libs/basic/model.php';
require_once 'commission.class.php';

class CommissionLink extends Model{
    public $_table = 'commissionlinks';
    public $partner = 0;
    public $businesscontact = 0;
    public $percentage = 0.0;


    protected function bootClasses()
    {
        $this->partner = new BusinessContact($this->partner);
        $this->businesscontact = new BusinessContact($this->businesscontact);
    }

    /**
     * @param BusinessContact $businesscontact
     * @return CommissionLink[]
     */
    public static function getAllForBC($businesscontact)
    {
        $retval = self::fetch([
            [
                'column'=>'businesscontact',
                'value'=>$businesscontact->getId()
            ]
        ]);
        return $retval;
    }

    /**
     * @return BusinessContact
     */
    public function getPartner()
    {
        return $this->partner;
    }

    /**
     * @param BusinessContact $partner
     */
    public function setPartner($partner)
    {
        $this->partner = $partner;
    }

    /**
     * @return BusinessContact
     */
    public function getBusinesscontact()
    {
        return $this->businesscontact;
    }

    /**
     * @param BusinessContact $businesscontact
     */
    public function setBusinesscontact($businesscontact)
    {
        $this->businesscontact = $businesscontact;
    }

    /**
     * @return float
     */
    public function getPercentage()
    {
        return $this->percentage;
    }

    /**
     * @param float $percentage
     */
    public function setPercentage($percentage)
    {
        $this->percentage = $percentage;
    }
}