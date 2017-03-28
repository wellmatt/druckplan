<?php
/**
 *  Copyright (c) 2017 Teuber Consult + IT GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <alexander.scherer@teuber-consult.de>, 2017
 *
 */
require_once 'libs/basic/model.php';


class ReceiptTaxPosition extends Model{
    public $_table = 'receipts_taxpositions';

    public $receipt = 0;            // Zugeordneter Beleg

    public $key = 'D0';
    public $amount = 0.0;
    public $percent = 19.0;

    /**
     * @param Receipt $receipt
     * @return ReceiptTaxPosition[]
     */
    public static function getAllForReceipt(Receipt $receipt)
    {
        $retval = self::fetch([
            [
                'column'=>'receipt',
                'value'=>$receipt->getId()
            ]
        ]);
        return $retval;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param string $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * @return float
     */
    public function getPercent()
    {
        return $this->percent;
    }

    /**
     * @param float $percent
     */
    public function setPercent($percent)
    {
        $this->percent = $percent;
    }
}