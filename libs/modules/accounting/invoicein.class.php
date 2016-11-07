<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Christian Schroeer <cschroeer@ipactor.de>, 2016
 *
 */
require_once 'libs/basic/model.php';

class invoicein extends model
{
    public $_table = 'invoiceins';

    public $number = '';
    public $supplier = 0;
    public $status = 1;
    public $netvalue = 0.0;
    public $tax = 0;
    public $redate = 0;
    public $duedate = 0;
    public $payeddate = 0;
    /**
     * @var string
     */
    public $description = '';

    const state_deleted = 0;
    const state_open = 1;
    const state_payed = 2;


    protected function bootclasses()
    {
        $this->supplier = new BusinessContact($this->supplier);
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
    public function getRedate()
    {
        return $this->redate;
    }

    /**
     * @param int $redate
     */
    public function setRedate($redate)
    {
        $this->redate = $redate;
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
     * @return mixed
     */
    public function getnumber()
    {
        return $this->number;
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
     * @param mixed $number
     */
    public function setnumber($number)
    {
        $this->number = $number;
    }

    /**
     * @return int
     */
    public function getstatus()
    {
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setstatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return businesscontact
     */
    public function getsupplier()
    {
        return $this->supplier;
    }

    /**
     * @param businesscontact $supplier
     */
    public function setsupplier($supplier)
    {
        $this->supplier = $supplier;
    }

    /**
     * @return int
     */
    public function getTax()
    {
        return $this->tax;
    }

    /**
     * @param int $tax
     */
    public function setTax($tax)
    {
        $this->tax = $tax;
    }
}

