<?php
/**
 *  Copyright (c) 2017 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2017
 *
 */
require_once 'libs/basic/model.php';
require_once 'libs/modules/collectiveinvoice/collectiveinvoice.class.php';
require_once 'libs/modules/accounting/revertposition.class.php';

class Revert extends Model{
    public $_table = 'reverts';

    public $number;
    public $colinv = 0;
    public $netvalue = 0.0;
    public $grossvalue = 0.0;
    public $crtdate = 0;
    public $duedate = 0;
    public $payeddate = 0;
    public $status = 1;
    public $doc = 0;

    const STATE_DELETED = 0;
    const STATE_OPEN = 1;
    const STATE_PAYED = 2;
    const STATE_STORNO = 3;


    protected function bootClasses()
    {
        $this->colinv = new CollectiveInvoice($this->colinv);
    }

    /**
     * @param CollectiveInvoice $colinv
     * @param array $positions
     * @param Letterhead $letterhead
     * @return Revert
     */
    public static function generate($colinv, $positions, $letterhead)
    {
        global $_USER;
        $netvalue = 0.0;
        $grossvalue = 0.0;
        $revertpositions = [];

        $tax = [];
        foreach ($positions as $opos => $amount) {
            $position = new Orderposition($opos);
            $taxkey = $position->getTaxkey()->getId();
            if ($position->getType() == 1){
                $netto = $position->getPrice() / $position->getAmount() * $amount;
                $postax = $position->getTax();
                $gross = $netto * (1+($position->getTax()/100));
                $tax[$postax][] = [$netto,$gross];
            } else {
                $netto = $position->getPrice() * $amount;
                $postax = $position->getTax();
                $gross = $netto * (1+($position->getTax()/100));
                $tax[$postax][] = [$netto,$gross];
            }
            // Generate RevertPosition
            $array = [
                'opos' => $opos,
                'amount' => $amount,
                'taxkey' => $taxkey,
                'price' => $netto
            ];
            $revertpositions[] = new RevertPosition(0,$array);
        }

        foreach ($tax as $mwst => $items) {
            foreach ($items as $item) {
                $netvalue += $item[0];
                $grossvalue += $item[1];
            }
        }

        // Generate Revert Number
//        $number = $_USER->getClient()->createOrderNumber(Client::NUMBER_REVERT);

        // Create the Revert
        $array = [
            'number' => "",
            'colinv' => $colinv->getId(),
            'netvalue' => $netvalue,
            'grossvalue' => $grossvalue,
            'crtdate' => time(),
            'duedate' => time(),
        ];
        $revert = new Revert(0,$array);
        $ret = $revert->save();
        if ($ret){
            // Generate RevertPositions
            foreach ($revertpositions as $revertposition) {
                $revertposition->setRevert($revert->getId());
                $revertposition->save();
            }

            // Generate Document
            $doc = new Document();
            $doc->setRequestId($colinv->getId());
            $doc->setRequestModule(Document::REQ_MODULE_COLLECTIVEORDER);
            $doc->setLetterhead((int)$letterhead);
            $doc->setType(Document::TYPE_REVERT);
            $hash = $doc->createDoc(Document::VERSION_EMAIL);
            $doc->createDoc(Document::VERSION_PRINT, $hash);
            $doc->save();
            $revert->setDoc($doc->getId());
            $revert->setNumber($doc->getName());
            $revert->save();
        }
        return $revert;
    }

    /**
     * @param CollectiveInvoice $collectinv
     * @return Revert
     */
    public static function getNewestForColinv(CollectiveInvoice $collectinv)
    {
        $retval = self::fetchSingle([
            [
                'column'=>'colinv',
                'value'=>$collectinv->getId()
            ],
            [
                'column'=>'status',
                'value'=>self::STATE_OPEN
            ],
            [
                'orderby'=>'id',
                'orderbydir'=>'desc'
            ]
        ]);
        return $retval;
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