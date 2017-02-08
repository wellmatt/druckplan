<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Christian Schroeer <cschroeer@ipactor.de>, 2016
 *
 */
require_once 'libs/basic/model.php';
require_once 'libs/modules/collectiveinvoice/collectiveinvoice.class.php';

class InvoiceOut extends Model{
    public $_table = 'invoiceouts';

    public $number;
    public $colinv = 0;
    public $netvalue = 0.0;
    public $grossvalue = 0.0;
    /**
     * @var float
     */
    public $cost = 0.0;
    public $crtdate = 0;
    public $duedate = 0;
    public $payeddate = 0;
    public $status = 1;
    public $doc;

    const STATE_DELETED = 0;
    const STATE_OPEN = 1;
    const STATE_PAYED = 2;
    const STATE_STORNO = 3;


    protected function bootClasses()
    {
        $this->colinv = new CollectiveInvoice($this->colinv);
    }

    /**
     * @param $number
     * @param CollectiveInvoice $colinv
     * @param PaymentTerms $payterm
     * @param int $doc
     * @return InvoiceOut
     */
    public static function generate($number, $colinv, $payterm, $doc)
    {
        $nettodays = $payterm->getNettodays();
        $now = time();
        $aday = 86400;
        $netvalue = 0.0;
        $grossvalue = 0.0;
        $cost = 0.0;


        $duedate = ($now + ($nettodays * $aday));

        $tax = [];
        $positions = Orderposition::getAllOrderposition($colinv->getId());
        foreach ($positions as $position) {
            if ($position->getStatus() == 1 && $position->getInvrel() == 1){
                if ($position->getType() == 1){
                    $netto = $position->getPrice();
                    $postax = $position->getTax();
                    $gross = $netto * (1+($position->getTax()/100));
                    $cost = $position->getCost();
                    $tax[$postax][] = [$netto,$gross,$cost];
                } else {
                    $netto = $position->getPrice() * $position->getAmount();
                    $postax = $position->getTax();
                    $gross = $netto * (1+($position->getTax()/100));
                    $cost = $position->getCost();
                    $tax[$postax][] = [$netto,$gross,$cost];
                }
            }
        }

        foreach ($tax as $mwst => $items) {
            foreach ($items as $item) {
                $netvalue += $item[0];
                $grossvalue += $item[1];
                $cost += $item[2];
            }
        }

        $array = [
            'number' => $number,
            'colinv' => $colinv->getId(),
            'netvalue' => $netvalue,
            'grossvalue' => $grossvalue,
            'cost' => $cost,
            'crtdate' => time(),
            'duedate' => $duedate,
            'doc' => $doc,
        ];
//        prettyPrint($array);

        $invout = new InvoiceOut(0,$array);
        $ret = $invout->save();
        if ($ret){
            $colinv->setLocked(1);
            $colinv->save();
            $commissionpartners = CommissionLink::getAllForBC($colinv->getBusinesscontact());
            if (count($commissionpartners)>0)
                Commission::generateCommission($colinv, $netvalue);
        }
        return $invout;
    }

    /**
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @param string $number
     */
    public function setNumber($number)
    {
        $this->number = $number;
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
    public function getNetvalue()
    {
        return $this->netvalue;
    }

    /**
     * @param float $netvalue
     */
    public function setNetvalue($netvalue)
    {
        $this->netvalue = $netvalue;
    }

    /**
     * @return float
     */
    public function getGrossvalue()
    {
        return $this->grossvalue;
    }

    /**
     * @param float $grossvalue
     */
    public function setGrossvalue($grossvalue)
    {
        $this->grossvalue = $grossvalue;
    }

    /**
     * @return int
     */
    public function getDuedate()
    {
        return $this->duedate;
    }

    /**
     * @param int $duedate
     */
    public function setDuedate($duedate)
    {
        $this->duedate = $duedate;
    }

    /**
     * @return int
     */
    public function getPayeddate()
    {
        return $this->payeddate;
    }

    /**
     * @param int $payeddate
     */
    public function setPayeddate($payeddate)
    {
        $this->payeddate = $payeddate;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
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
     * @return mixed
     */
    public function getDoc()
    {
        return $this->doc;
    }

    /**
     * @param mixed $doc
     */
    public function setDoc($doc)
    {
        $this->doc = $doc;
    }


}