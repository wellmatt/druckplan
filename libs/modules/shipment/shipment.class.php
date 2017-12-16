<?php
/**
 *  Copyright (c) 2017 Teuber Consult + IT GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <alexander.scherer@teuber-consult.de>, 2017
 *
 */

require_once 'saas.shipping.class.php';

class Shipment extends Model{
    public $_table = 'shipments';

    const SERVICE_DPD = "dpd";
    const SERVICE_DPD_TEST = "dpd_test";

    const LABELSIZE_A4 = "PDF_A4";
    const LABELSIZE_A6 = "PDF_A6";

    public $colinv;

    public $shippingService = '';
    public $labelSize = '';
    public $shipDate = 0;
    public $weight = 0.0;
    public $width = 0;
    public $length = 0;
    public $height = 0;
    public $content = '';
    public $reference = '';

    public $packageLabel = '';
    public $parcelNumber = '';
    public $message = '';

    protected function bootClasses()
    {
        $this->colinv = new CollectiveInvoice((int)$this->colinv);
    }

    /**
     * @param CollectiveInvoice $colinv
     * @return Shipment[]
     */
    public static function getAllForColinv(CollectiveInvoice $colinv)
    {
        $retval = self::fetch([
            [
                'column'=>'colinv',
                'value'=>$colinv->getId()
            ],
            [
                'column'=>'parcelNumber',
                'operator'=>'!=',
                'value'=>''
            ]
        ]);
        return $retval;
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
     * @return string
     */
    public function getShippingService()
    {
        return $this->shippingService;
    }

    /**
     * @param string $shippingService
     */
    public function setShippingService($shippingService)
    {
        $this->shippingService = $shippingService;
    }

    /**
     * @return string
     */
    public function getLabelSize()
    {
        return $this->labelSize;
    }

    /**
     * @param string $labelSize
     */
    public function setLabelSize($labelSize)
    {
        $this->labelSize = $labelSize;
    }

    /**
     * @return int
     */
    public function getShipDate()
    {
        return $this->shipDate;
    }

    /**
     * @param int $shipDate
     */
    public function setShipDate($shipDate)
    {
        $this->shipDate = $shipDate;
    }

    /**
     * @return float
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * @param float $weight
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;
    }

    /**
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param int $width
     */
    public function setWidth($width)
    {
        $this->width = $width;
    }

    /**
     * @return int
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * @param int $length
     */
    public function setLength($length)
    {
        $this->length = $length;
    }

    /**
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @param int $height
     */
    public function setHeight($height)
    {
        $this->height = $height;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * @param string $reference
     */
    public function setReference($reference)
    {
        $this->reference = $reference;
    }

    /**
     * @return string
     */
    public function getPackageLabel()
    {
        return $this->packageLabel;
    }

    /**
     * @param string $packageLabel
     */
    public function setPackageLabel($packageLabel)
    {
        $this->packageLabel = $packageLabel;
    }

    /**
     * @return string
     */
    public function getParcelNumber()
    {
        return $this->parcelNumber;
    }

    /**
     * @param string $parcelNumber
     */
    public function setParcelNumber($parcelNumber)
    {
        $this->parcelNumber = $parcelNumber;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }
}