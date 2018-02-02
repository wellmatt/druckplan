<?php
/**
 *  Copyright (c) 2017 Teuber Consult + IT GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <alexander.scherer@teuber-consult.de>, 2017
 *
 */


class SaxoprintOrder{
    public $OrderNumber;
    public $ReferenceNumber;
    public $PortalId;
    public $CompletionDate;
    public $DeliveryAddresses;
    public $SenderAddress;
    public $ProductDetails;
    public $WorkingStates;

    /**
     * SaxoprintOrder constructor.
     * @param integer $OrderNumber
     * @param string $ReferenceNumber
     * @param integer $PortalId
     * @param $CompletionDate
     * @param SaxoprintAddress[] $DeliveryAddresses
     * @param SaxoprintAddress $SenderAddress
     * @param SaxoprintProductDetails $ProductDetails
     * @param SaxoprintWorkingState[] $WorkingStates
     */
    public function __construct($OrderNumber, $ReferenceNumber, $PortalId, $CompletionDate, $DeliveryAddresses, $SenderAddress, $ProductDetails, $WorkingStates)
    {
        $this->OrderNumber = $OrderNumber;
        $this->ReferenceNumber = $ReferenceNumber;
        $this->PortalId = $PortalId;
        $this->CompletionDate = $CompletionDate;
        $this->DeliveryAddresses = $DeliveryAddresses;
        $this->SenderAddress = $SenderAddress;
        $this->ProductDetails = $ProductDetails;
        $this->WorkingStates = $WorkingStates;
    }

    public function formatDetails()
    {
        $ret = '';
        $details = $this->getProductDetails();
        foreach ($details->getProductCharacteristics() as $productCharacteristic) {
            $ret .= $productCharacteristic->getPropertyName(). ': '. $productCharacteristic->getPropertyValueName(). '</br>';
        }
        foreach ($details->getSpecialColors() as $specialColor) {
            $ret .= $specialColor->getChromaticity(). ': '. $specialColor->getSpecialColor(). '</br>';
        }
        foreach ($details->getFreeFormats() as $freeFormat) {
            $ret .= $freeFormat->getPropertyName(). ': '. $freeFormat->getValue(). '</br>';
        }
        return $ret;
    }

    /**
     * @return bool|CollectiveInvoice
     */
    public function createColinv()
    {
        $perf = new Perferences();

        $saxobc = new BusinessContact($perf->getSaxobc());
        $saxocp = new ContactPerson($perf->getSaxocp());

        $comment = '';
        foreach ($this->DeliveryAddresses as $deliveryAddress) {
            $comment .= 'Lieferadresse:
"CompanyName": '.$deliveryAddress->CompanyName.',
"FirstName": '.$deliveryAddress->FirstName.',
"LastName": '.$deliveryAddress->LastName.',
"Street": '.$deliveryAddress->Street.',
"Zipcode": '.$deliveryAddress->Zipcode.',
"City": '.$deliveryAddress->City.',
"TelephoneNumber": '.$deliveryAddress->TelephoneNumber.',
"CountryCodeISO": '.$deliveryAddress->CountryCodeISO.'';
        }

        $comment2 = '';
        $comment2 .= 'Absenderadresse:
"CompanyName": '.$this->SenderAddress->CompanyName.',
"FirstName": '.$this->SenderAddress->FirstName.',
"LastName": '.$this->SenderAddress->LastName.',
"Street": '.$this->SenderAddress->Street.',
"Zipcode": '.$this->SenderAddress->Zipcode.',
"City": '.$this->SenderAddress->City.',
"TelephoneNumber": '.$this->SenderAddress->TelephoneNumber.',
"CountryCodeISO": '.$this->SenderAddress->CountryCodeISO.'';

        $prodgrp = '';
        $material = '';
        $format = '';
        $amount = $this->getProductDetails()->getCirculation();
        $chroma = '';
        $logistic = '';
        $form = '';

        foreach ($this->getProductDetails()->ProductCharacteristics as $productCharacteristic) {
            if ($productCharacteristic->getPropertyId() == 6)
                $prodgrp = $productCharacteristic->getPropertyValueName();
            if ($productCharacteristic->getPropertyId() == 8)
                $material = $productCharacteristic->getPropertyValueName();
            if ($productCharacteristic->getPropertyId() == 9)
                $format = $productCharacteristic->getPropertyValueName();
            if ($productCharacteristic->getPropertyId() == 7)
                $chroma = $productCharacteristic->getPropertyValueName();
            if ($productCharacteristic->getPropertyId() == 13)
                $logistic = $productCharacteristic->getPropertyValueName();
            if ($productCharacteristic->getPropertyId() == 75)
                $form = $productCharacteristic->getPropertyValueName();
        }

        $col_inv = new CollectiveInvoice();
        $col_inv->setBusinesscontact($saxobc);
        $col_inv->setCustContactperson($saxocp);
        $col_inv->setTitle("Saxoprint #".$this->getOrderNumber()." / ".$prodgrp." / ".$material);
        $col_inv->setPaymentterm($saxobc->getPaymentTerms());
        $col_inv->setClient(new Client(1));
        $col_inv->setType(3);
        $col_inv->setComment($comment);
        $col_inv->setExt_comment($comment2);
        $col_inv->setSaxoid($this->getOrderNumber());
        $col_inv->setSaxoportalid($this->getPortalId());
        $col_inv->setSaxomaterial($material);
        $col_inv->setSaxoprodgrp($prodgrp);
        $col_inv->setStatus(3);
        $col_inv->setDeliverydate($this->CompletionDate);
        $col_inv->setNeeds_planning(1);

        $res = $col_inv->save();
        if ($res) {
            $pos = new Orderposition();
            $pos->setSequence(1);
            $pos->setPrice(0.0);
            $pos->setStatus(1);
            $pos->setObjectid(0);
            $pos->setCollectiveinvoice($col_inv->getId());
            $pos->setQuantity(1);
            $pos->setTaxkey(TaxKey::getDefaultTaxKey());
            $pos->setType(Orderposition::TYPE_MANUELL);
            $pos->setComment($this->formatDetails());
            Orderposition::saveMultipleOrderpositions([$pos]);
            SaxoprintCollectiveinvoiceInfo::createInfo($col_inv->getId(),$col_inv->getNumber(),'',$this->ReferenceNumber,$this->CompletionDate,$material,$format,$amount,$chroma,0,$form,0,$logistic,$prodgrp);
            return $col_inv;
        } else
            return false;
    }

    /**
     * @return integer
     */
    public function getOrderNumber()
    {
        return $this->OrderNumber;
    }

    /**
     * @return integer
     */
    public function getPortalId()
    {
        return $this->PortalId;
    }

    /**
     * @return mixed
     */
    public function getCompletionDate()
    {
        return $this->CompletionDate;
    }

    /**
     * @return SaxoprintAddress[]
     */
    public function getDeliveryAddresses()
    {
        return $this->DeliveryAddresses;
    }

    /**
     * @return SaxoprintAddress
     */
    public function getSenderAddress()
    {
        return $this->SenderAddress;
    }

    /**
     * @return SaxoprintProductDetails
     */
    public function getProductDetails()
    {
        return $this->ProductDetails;
    }

    /**
     * @return SaxoprintWorkingState[]
     */
    public function getWorkingStates()
    {
        return $this->WorkingStates;
    }
}