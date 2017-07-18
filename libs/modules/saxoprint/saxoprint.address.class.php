<?php
/**
 *  Copyright (c) 2017 Teuber Consult + IT GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <alexander.scherer@teuber-consult.de>, 2017
 *
 */


class SaxoprintAddress{
    public $Salutation;
    public $CompanyName;
    public $FirstName;
    public $LastName;
    public $Street;
    public $Zipcode;
    public $City;
    public $TelephoneNumber;
    public $CountryCodeISO;

    /**
     * SaxoprintAddress constructor.
     * @param $Salutation
     * @param $CompanyName
     * @param $FirstName
     * @param $LastName
     * @param $Street
     * @param $Zipcode
     * @param $City
     * @param $TelephoneNumber
     * @param $CountryCodeISO
     */
    public function __construct($Salutation, $CompanyName, $FirstName, $LastName, $Street, $Zipcode, $City, $TelephoneNumber, $CountryCodeISO)
    {
        $this->Salutation = $Salutation;
        $this->CompanyName = $CompanyName;
        $this->FirstName = $FirstName;
        $this->LastName = $LastName;
        $this->Street = $Street;
        $this->Zipcode = $Zipcode;
        $this->City = $City;
        $this->TelephoneNumber = $TelephoneNumber;
        $this->CountryCodeISO = $CountryCodeISO;
    }

    /**
     * @return mixed
     */
    public function getSalutation()
    {
        return $this->Salutation;
    }

    /**
     * @return mixed
     */
    public function getCompanyName()
    {
        return $this->CompanyName;
    }

    /**
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->FirstName;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->LastName;
    }

    /**
     * @return mixed
     */
    public function getStreet()
    {
        return $this->Street;
    }

    /**
     * @return mixed
     */
    public function getZipcode()
    {
        return $this->Zipcode;
    }

    /**
     * @return mixed
     */
    public function getCity()
    {
        return $this->City;
    }

    /**
     * @return mixed
     */
    public function getTelephoneNumber()
    {
        return $this->TelephoneNumber;
    }

    /**
     * @return mixed
     */
    public function getCountryCodeISO()
    {
        return $this->CountryCodeISO;
    }
}