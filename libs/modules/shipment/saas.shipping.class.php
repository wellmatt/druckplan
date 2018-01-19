<?php
/**
 *  Copyright (c) 2017 Teuber Consult + IT GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <alexander.scherer@teuber-consult.de>, 2017
 *
 */
require_once 'shipment.class.php';

class SaasShipping{
    public $customerId; // integer
    public $shippingService;
    public $labelSize;
    public $company;
    public $salutation;
    public $firstname;
    public $lastname;
    public $street;
    public $houseNumber;
    public $country;
    public $zipCode;
    public $city;
    public $phone;
    public $mail;
    public $shipDate; // yyyy-mm-dd
    public $weight; // float
    public $width; // integer
    public $length; // integer
    public $height; // integer
    public $content;
    public $reference;

    /**
     * SaasShipping constructor.
     * @param Shipment $shipment
     */
    public function __construct(Shipment $shipment)
    {
        $perf = new Perferences();
        $this->customerId = $perf->getSaasdoCustomerId();

        $this->shippingService = $shipment->getShippingService();
        $this->labelSize = $shipment->getLabelSize();

        $colinv = $shipment->getColinv();
        $busicon = $colinv->getBusinesscontact();
        $contactperson = $colinv->getCustContactperson();

        $this->company = $busicon->getNameAsLine();
        $this->salutation = $contactperson->getTitle();
        $this->firstname = $contactperson->getName2();
        $this->lastname = $contactperson->getName1();

        if ($colinv->getDeliveryaddress()->getId() > 0){
            $this->street = $colinv->getDeliveryaddress()->getStreet();
            $this->houseNumber = $colinv->getDeliveryaddress()->getHouseno();

//        $this->country = $colinv->getDeliveryaddress()->getCountry()->getCode();
            $this->country = "DEU";

            $this->zipCode = $colinv->getDeliveryaddress()->getZip();
            $this->city = $colinv->getDeliveryaddress()->getCity();
        } else {
            $this->street = $busicon->getStreet();
            $this->houseNumber = $busicon->getHouseno();

//        $this->country = $busicon->getCountry()->getCode();
            $this->country = "DEU";

            $this->zipCode = $busicon->getZip();
            $this->city = $busicon->getCity();
        }
//        $this->phone = $busicon->getPhone();
//        $this->mail = $busicon->getEmail();

        $this->shipDate = date('Y-m-d',$shipment->getShipDate());

        $this->weight = tofloat($shipment->getWeight());
        $this->width = (int)$shipment->getWidth();
        $this->length = (int)$shipment->getLength();
        $this->height = (int)$shipment->getHeight();

        $this->content = $shipment->getContent();
        $this->reference = $shipment->getReference();
    }

    public function send()
    {
        $url = "https://teuber-shipping.saas.do/api/v1/shipping/default/";
        $postData = json_encode($this);
//        dd($postData);
        $ch = curl_init();

        $auth = "Authorization: Bearer ML7rMmZvcJO3I0c09OLwb1mkaMFvviwG5nDLVaNy";

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt($ch, CURLOPT_POST, 1 );
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData );
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', $auth, 'Accept: application/json'));
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        $output=curl_exec($ch);

        curl_close($ch);
        return $output;
    }
}