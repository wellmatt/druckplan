<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Christian Schroeer <cschroeer@ipactor.de>, 2016
 *
 */
require_once 'libs/basic/model.php';

class InvoiceIn extends model
{
    public $_table = 'invoiceins';

    public $number = '';
    public $supplier = 0;
    public $status = 1;
    public $netvalue = 0.0;
    public $tax = 19.00;
    public $redate = 0;
    public $duedate = 0;
    public $payeddate = 0;
    public $grossvalue = 0.0;

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
     * @return BusinessContact
     */
    public function getSupplier()
    {
        return $this->supplier;
    }

    /**
     * @param BusinessContact $supplier
     */
    public function setSupplier($supplier)
    {
        $this->supplier = $supplier;
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

}

