<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */
require_once 'libs/basic/model.php';
require_once 'libs/modules/suporder/suporder.position.class.php';
require_once 'libs/modules/businesscontact/address.class.php';
require_once 'libs/modules/businesscontact/contactperson.class.php';
require_once 'libs/modules/paymentterms/paymentterms.class.php';

class SupOrder extends Model {
    public $_table = 'suporders';

    public $number = '';
    public $supplier = 0;
    public $title = '';
    public $eta = 0;
    public $paymentterm = 0;
    public $status = 1;
    public $invoiceaddress = 0;
    public $deliveryaddress = 0;
    public $crtdate = 0;
    public $crtuser = 0;
    public $cpinternal = 0;
    public $cpexternal = 0;

    protected function bootClasses()
    {
        $this->supplier = new BusinessContact($this->supplier);
        $this->invoiceaddress = new Address($this->invoiceaddress);
        $this->deliveryaddress = new Address($this->deliveryaddress);
        $this->crtuser = new User($this->crtuser);
        $this->cpinternal = new User($this->cpinternal);
        $this->cpexternal = new ContactPerson($this->cpexternal);
        $this->paymentterm = new PaymentTerms($this->paymentterm);
    }

    /**
     * @return SupOrder[]
     */
    public static function getAllOrdered()
    {
        $retval = self::fetch([
            [
                'column'=>'status',
                'value'=>2
            ]
        ]);
        return $retval;
    }

    /**
     * Gibt den String zu einem int Status zurueck
     * @param int $status
     * @return string
     */
    public static function getStatusLabel($status)
    {
        switch ($status){
            case 1:
                $status = 'Offen';
                break;
            case 2:
                $status = 'Bestellt';
                break;
            case 3:
                $status = 'Ware Eingegangen';
                break;
            case 4:
                $status = 'Bezahlt';
                break;
            case 5:
                $status = 'Erledigt';
                break;
            default:
                $status = 'Unbekannt';
                break;
        }
        return $status;
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
     * @return int
     */
    public function getEta()
    {
        return $this->eta;
    }

    /**
     * @param int $eta
     */
    public function setEta($eta)
    {
        $this->eta = $eta;
    }

    /**
     * @return PaymentTerms
     */
    public function getPaymentterm()
    {
        return $this->paymentterm;
    }

    /**
     * @param PaymentTerms $paymentterm
     */
    public function setPaymentterm($paymentterm)
    {
        $this->paymentterm = $paymentterm;
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
     * @return Address
     */
    public function getInvoiceaddress()
    {
        return $this->invoiceaddress;
    }

    /**
     * @param Address $invoiceaddress
     */
    public function setInvoiceaddress($invoiceaddress)
    {
        $this->invoiceaddress = $invoiceaddress;
    }

    /**
     * @return Address
     */
    public function getDeliveryaddress()
    {
        return $this->deliveryaddress;
    }

    /**
     * @param Address $deliveryaddress
     */
    public function setDeliveryaddress($deliveryaddress)
    {
        $this->deliveryaddress = $deliveryaddress;
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
     * @return User
     */
    public function getCpinternal()
    {
        return $this->cpinternal;
    }

    /**
     * @param User $cpinternal
     */
    public function setCpinternal($cpinternal)
    {
        $this->cpinternal = $cpinternal;
    }

    /**
     * @return ContactPerson
     */
    public function getCpexternal()
    {
        return $this->cpexternal;
    }

    /**
     * @param ContactPerson $cpexternal
     */
    public function setCpexternal($cpexternal)
    {
        $this->cpexternal = $cpexternal;
    }
}