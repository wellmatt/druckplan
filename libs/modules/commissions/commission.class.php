<?php
/**
 *  Copyright (c) 2017 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2017
 *
 */

require_once 'libs/basic/model.php';
require_once 'commissionlink.class.php';

class Commission extends Model{
    public $_table = 'commissions';
    public $partner = 0;
    public $businesscontact = 0;
    public $colinv = 0;
    public $percentage = 0.0;
    public $value = 0.0;
    public $crtdate = 0;
    public $creditdate = 0;
    public $creditcolinv = 0;

    protected function bootClasses()
    {
        $this->partner = new BusinessContact($this->partner);
        $this->businesscontact = new BusinessContact($this->businesscontact);
        $this->colinv = new CollectiveInvoice($this->colinv);
        $this->creditcolinv = new CollectiveInvoice($this->creditcolinv);
    }

    /**
     * @param CollectiveInvoice $colinv
     * @param float $netvalue
     */
    public static function generateCommission($colinv, $netvalue)
    {
        if ($colinv->getId() > 0 && $netvalue > 0){

            $comlinks = CommissionLink::getAllForBC($colinv->getBusinesscontact());

            foreach ($comlinks as $comlink) {
                $thisvalue = $netvalue / 100 * $comlink->getPercentage();

                $array = [
                    "partner" => $comlink->getPartner()->getId(),
                    "businesscontact" => $colinv->getBusinesscontact()->getId(),
                    "colinv" => $colinv->getId(),
                    "percentage" => $comlink->getPercentage(),
                    "value" => $thisvalue,
                    "crtdate" => time(),
                    "creditdate" => 0,
                    "creditcolinv" => 0,
                ];
                $commission = new Commission(0, $array);
                $ret = $commission->save();
            }
        }
    }

    public function generateColinv()
    {
        global $_USER;

        $colinv = new CollectiveInvoice();
        $colinv->setClient($_USER->getClient());
        $colinv->setBusinesscontact($this->partner);
        $cp = ContactPerson::getMainContact($this->getPartner());
        $colinv->setCustContactperson($cp);
        $colinv->setTitle('Provision zu '.$this->getColinv()->getNumber());
        $colinv->setCrtuser($_USER);
        $colinv->setCrtdate(time());
        $colinv->setStatus(3);

        $ret = $colinv->save();

        if ($ret){
            $commissionpos = new Orderposition();
            $commissionpos->setComment('Provision für '.$this->getColinv()->getNumber());
            $commissionpos->setInvrel(1);
            $commissionpos->setCollectiveinvoice($colinv->getId());
            $commissionpos->setPrice($this->getValue());
            $commissionpos->setQuantity(1);
            $commissionpos->setRevrel(1);
            $commissionpos->setStatus(1);
            $commissionpos->setTax(19.0);
            $commissionpos->setType(0);
            $commissionpos->setObjectid(0);
            $ret = Orderposition::saveMultipleOrderpositions([$commissionpos]);

            if ($ret){
                $this->setCreditcolinv($colinv);
                $this->setCreditdate(time());
                $this->save();
            }
        }
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
     * @return CollectiveInvoice
     */
    public function getColinv()
    {
        return $this->colinv;
    }

    /**
     * @param CollectiveInvoice $colinv
     */
    public function setColinv($colinv)
    {
        $this->colinv = $colinv;
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

    /**
     * @return float
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param float $value
     */
    public function setValue($value)
    {
        $this->value = $value;
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
    public function getCreditdate()
    {
        return $this->creditdate;
    }

    /**
     * @param int $creditdate
     */
    public function setCreditdate($creditdate)
    {
        $this->creditdate = $creditdate;
    }

    /**
     * @return CollectiveInvoice
     */
    public function getCreditcolinv()
    {
        return $this->creditcolinv;
    }

    /**
     * @param CollectiveInvoice $creditcolinv
     */
    public function setCreditcolinv($creditcolinv)
    {
        $this->creditcolinv = $creditcolinv;
    }
}