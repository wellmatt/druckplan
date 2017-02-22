<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Christian Schroeer <cschroeer@ipactor.de>, 2016
 *
 */
require_once 'libs/basic/model.php';
require_once 'libs/modules/taxkeys/taxkey.class.php';

class InvoiceIn extends model
{
    public $_table = 'invoiceins';

    public $number = '';
    public $supplier = 0;
    public $status = 1;
    public $netvalue = 0.0;
    public $taxkey = 0;
    public $redate = 0;
    public $duedate = '';
    public $payeddate = 0;
    public $grossvalue = 0.0;

    /**
     * @var string
     */
    public $description = '';

    const state_deleted = 0;
    const state_open = 1;
    const state_payed = 2;


    protected function BootClasses()
    {
        $this->supplier = new BusinessContact($this->supplier);
        $this->taxkey = new TaxKey($this->taxkey);

        // -- Temporary measure to assure default taxkey if none is set! //
        if ($this->taxkey->getId() == 0){
            $defaulttaxkey = TaxKey::getDefaultTaxKey();
            $this->taxkey = $defaulttaxkey; // grabbing the default taxkey just to be sure that one is set
        }
        //
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
        return $this->taxkey->getValue();
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

    /**
     * @return TaxKey
     */
    public function getTaxkey()
    {
        return $this->taxkey;
    }

    /**
     * @param TaxKey $taxkey
     */
    public function setTaxkey($taxkey)
    {
        $this->taxkey = $taxkey;
    }
}

