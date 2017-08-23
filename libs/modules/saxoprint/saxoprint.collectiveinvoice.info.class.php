<?php
/**
 *  Copyright (c) 2017 Teuber Consult + IT GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <alexander.scherer@teuber-consult.de>, 2017
 *
 */
require_once 'libs/basic/model.php';

class SaxoprintCollectiveinvoiceInfo extends Model{
    public $_table = 'collectiveinvoice_saxoinfo';

    public $colinvoice;
    public $colinvoicenr;
    public $contractid;
    public $referenceid;
    public $compldate;
    public $material;
    public $format;
    public $amount;
    public $chroma;
    public $stamp;
    public $form;
    public $bookstamp;
    public $logistic;
    public $prodgrp;

    /**
     * SaxoprintCollectiveinvoiceInfo constructor.
     * @param $colinvoice
     * @param $colinvoicenr
     * @param $contractid
     * @param $referenceid
     * @param $compldate
     * @param $material
     * @param $format
     * @param $amount
     * @param $chroma
     * @param $stamp
     * @param $form
     * @param $bookstamp
     * @param $logistic
     * @param $prodgrp
     */
    public static function createInfo($colinvoice, $colinvoicenr, $contractid, $referenceid, $compldate, $material, $format, $amount, $chroma, $stamp, $form, $bookstamp, $logistic, $prodgrp)
    {
        $info = new SaxoprintCollectiveinvoiceInfo();
        $info->colinvoice = $colinvoice;
        $info->colinvoicenr = $colinvoicenr;
        $info->contractid = $contractid;
        $info->referenceid = $referenceid;
        $info->compldate = $compldate;
        $info->material = $material;
        $info->format = $format;
        $info->amount = $amount;
        $info->chroma = $chroma;
        $info->stamp = $stamp;
        $info->form = $form;
        $info->bookstamp = $bookstamp;
        $info->logistic = $logistic;
        $info->prodgrp = $prodgrp;
        $info->save();
    }


    /**
     * @param CollectiveInvoice $colinv
     * @return SaxoprintCollectiveinvoiceInfo
     */
    public static function getForColinv(CollectiveInvoice $colinv)
    {
        $retval = self::fetchSingle([
            [
                'column'=>'colinvoice',
                'value'=>$colinv->getId()
            ]
        ]);
        return $retval;
    }

    /**
     * @return mixed
     */
    public function getColinvoice()
    {
        return $this->colinvoice;
    }

    /**
     * @param mixed $colinvoice
     */
    public function setColinvoice($colinvoice)
    {
        $this->colinvoice = $colinvoice;
    }

    /**
     * @return mixed
     */
    public function getContractid()
    {
        return $this->contractid;
    }

    /**
     * @param mixed $contractid
     */
    public function setContractid($contractid)
    {
        $this->contractid = $contractid;
    }

    /**
     * @return mixed
     */
    public function getReferenceid()
    {
        return $this->referenceid;
    }

    /**
     * @param mixed $referenceid
     */
    public function setReferenceid($referenceid)
    {
        $this->referenceid = $referenceid;
    }

    /**
     * @return mixed
     */
    public function getCompldate()
    {
        return $this->compldate;
    }

    /**
     * @param mixed $compldate
     */
    public function setCompldate($compldate)
    {
        $this->compldate = $compldate;
    }

    /**
     * @return mixed
     */
    public function getMaterial()
    {
        return $this->material;
    }

    /**
     * @param mixed $material
     */
    public function setMaterial($material)
    {
        $this->material = $material;
    }

    /**
     * @return mixed
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * @param mixed $format
     */
    public function setFormat($format)
    {
        $this->format = $format;
    }

    /**
     * @return mixed
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param mixed $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * @return mixed
     */
    public function getChroma()
    {
        return $this->chroma;
    }

    /**
     * @param mixed $chroma
     */
    public function setChroma($chroma)
    {
        $this->chroma = $chroma;
    }

    /**
     * @return mixed
     */
    public function getStamp()
    {
        return $this->stamp;
    }

    /**
     * @param mixed $stamp
     */
    public function setStamp($stamp)
    {
        $this->stamp = $stamp;
    }

    /**
     * @return mixed
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @param mixed $form
     */
    public function setForm($form)
    {
        $this->form = $form;
    }

    /**
     * @return mixed
     */
    public function getBookstamp()
    {
        return $this->bookstamp;
    }

    /**
     * @param mixed $bookstamp
     */
    public function setBookstamp($bookstamp)
    {
        $this->bookstamp = $bookstamp;
    }

    /**
     * @return mixed
     */
    public function getLogistic()
    {
        return $this->logistic;
    }

    /**
     * @param mixed $logistic
     */
    public function setLogistic($logistic)
    {
        $this->logistic = $logistic;
    }

    /**
     * @return mixed
     */
    public function getProdgrp()
    {
        return $this->prodgrp;
    }

    /**
     * @param mixed $prodgrp
     */
    public function setProdgrp($prodgrp)
    {
        $this->prodgrp = $prodgrp;
    }

    /**
     * @return mixed
     */
    public function getColinvoicenr()
    {
        return $this->colinvoicenr;
    }

    /**
     * @param mixed $colinvoicenr
     */
    public function setColinvoicenr($colinvoicenr)
    {
        $this->colinvoicenr = $colinvoicenr;
    }
}