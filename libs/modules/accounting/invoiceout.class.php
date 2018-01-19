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
    public $businesscontact = 0;
    public $netvalue = 0.0;
    public $grossvalue = 0.0;
    /**
     * @var float
     */
    public $cost = 0.0;
    public $crtdate = 0;
    public $duedate = 0;
    public $duedatesk1 = 0;
    public $sk1_percent = 0.0;
    public $duedatesk2 = 0;
    public $sk2_percent = 0.0;
    public $payeddate = 0;
    public $payedskonto = 0.0;
    public $status = 1;
    public $doc;
    public $bank = '';

    const STATE_DELETED = 0;
    const STATE_OPEN = 1;
    const STATE_PAYED = 2;
    const STATE_STORNO = 3;


    protected function bootClasses()
    {
        $this->colinv = new CollectiveInvoice($this->colinv);
        $this->businesscontact = new BusinessContact($this->businesscontact);
    }

    /**
     * @param BusinessContact $businesscontact
     * @param int $status
     * @return InvoiceOut[]
     */
    public static function getAllForBC(BusinessContact $businesscontact, $status = -1)
    {
        $filter = [
            [
                'column'=>'businesscontact',
                'value'=>$businesscontact->getId()
            ]
        ];
        if ($status != -1){
            $filter[] = [ 'column'=>'status', 'value'=>$status ];
        }
        $retval = self::fetch($filter);
        return $retval;
    }

    /**
     * @param BusinessContact $businesscontact
     * @return float
     */
    public static function getTotalOpenForBC(BusinessContact $businesscontact)
    {
        $sum = 0;
        $invoices = self::getAllForBC($businesscontact, self::STATE_OPEN);
        foreach ($invoices as $invoice) {
            $sum += $invoice->getGrossvalue();
        }
        return $sum;
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

        $duedate = ($now + ($nettodays * $aday));

        $pricetable = $colinv->getPriceTable();
        $netvalue = $pricetable['total_net'];
        $grossvalue = $pricetable['total_gross'];
        $costvalue = $pricetable['total_cost'];

        $sk1days = $payterm->getSkontodays1();
        $sk1daysduedate = ($now + ($sk1days * $aday));

        $sk2days = $payterm->getSkontodays2();
        $sk2daysduedate = ($now + ($sk2days * $aday));

        $sk1_percent = $payterm->getSkonto1();
        $sk2_percent = $payterm->getSkonto2();

        $array = [
            'number' => $number,
            'colinv' => $colinv->getId(),
            'businesscontact' => $colinv->getBusinesscontact()->getId(),
            'netvalue' => roundPrice($netvalue),
            'grossvalue' => roundPrice($grossvalue),
            'cost' => roundPrice($costvalue),
            'crtdate' => time(),
            'duedate' => $duedate,
            'doc' => $doc,
            'bank' => "",
            'duedatesk1' => $sk1daysduedate,
            'sk1_percent' => $sk1_percent,
            'duedatesk2' => $sk2daysduedate,
            'sk2_percent' => $sk2_percent,
        ];

        $invout = new InvoiceOut(0,$array);
        $ret = $invout->save();
        if ($ret){
            Receipt::generateReceipt($invout);

            $colinv->setLocked(1);
            $colinv->save();
            $commissionpartners = CommissionLink::getAllForBC($colinv->getBusinesscontact());
            if (count($commissionpartners)>0)
                Commission::generateCommission($colinv, $netvalue, $doc);
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

    /**
     * @return int
     */
    public function getDuedatesk1()
    {
        return $this->duedatesk1;
    }

    /**
     * @param int $duedatesk1
     */
    public function setDuedatesk1($duedatesk1)
    {
        $this->duedatesk1 = $duedatesk1;
    }

    /**
     * @return float
     */
    public function getSk1Percent()
    {
        return $this->sk1_percent;
    }

    /**
     * @param float $sk1_percent
     */
    public function setSk1Percent($sk1_percent)
    {
        $this->sk1_percent = $sk1_percent;
    }

    /**
     * @return int
     */
    public function getDuedatesk2()
    {
        return $this->duedatesk2;
    }

    /**
     * @param int $duedatesk2
     */
    public function setDuedatesk2($duedatesk2)
    {
        $this->duedatesk2 = $duedatesk2;
    }

    /**
     * @return float
     */
    public function getSk2Percent()
    {
        return $this->sk2_percent;
    }

    /**
     * @param float $sk2_percent
     */
    public function setSk2Percent($sk2_percent)
    {
        $this->sk2_percent = $sk2_percent;
    }

    /**
     * @return float
     */
    public function getPayedskonto()
    {
        return $this->payedskonto;
    }

    /**
     * @param float $payedskonto
     */
    public function setPayedskonto($payedskonto)
    {
        $this->payedskonto = $payedskonto;
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
     * @return string
     */
    public function getBank()
    {
        return $this->bank;
    }

    /**
     * @param string $bank
     */
    public function setBank($bank)
    {
        $this->bank = $bank;
    }
}