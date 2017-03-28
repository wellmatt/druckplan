<?php
/**
 *  Copyright (c) 2017 Teuber Consult + IT GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <alexander.scherer@teuber-consult.de>, 2017
 *
 */
require_once 'libs/basic/model.php';


class ReceiptPosition extends Model{
    public $_table = 'receipts_positions';

    public $receipt = 0;            // Zugeordneter Beleg

    CONST TYPE_CREDIT = 1;
    CONST TYPE_DEBIT = 2;

    public $type = 1;

    public $postingkey = 0;         // Buchungsschlüssel (Credit: 210 RE / 250 GS // Debit: 150 RE / 110 GS)
    public $accountnumber = 0;      // Kontonummer (Credit: Kundennummer // Debit: Erlöskonto)
    public $amount = 0.0;           // Betrag (Brutto)
    public $tax_key = '';           // Steuerschlüssel (WERT) // nur bei Debit
    public $tax_amount = 0.0;       // Steuerbetrag // nur bei Debit
    public $revenueaccount = '';    // Kostenträger // nur bei Debit

    /**
     * @param Receipt $receipt
     * @return ReceiptPosition[]
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
     * @return int
     */
    public function getReceipt()
    {
        return $this->receipt;
    }

    /**
     * @param int $receipt
     */
    public function setReceipt($receipt)
    {
        $this->receipt = $receipt;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param int $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return int
     */
    public function getPostingkey()
    {
        return $this->postingkey;
    }

    /**
     * @param int $postingkey
     */
    public function setPostingkey($postingkey)
    {
        $this->postingkey = $postingkey;
    }

    /**
     * @return int
     */
    public function getAccountnumber()
    {
        return $this->accountnumber;
    }

    /**
     * @param int $accountnumber
     */
    public function setAccountnumber($accountnumber)
    {
        $this->accountnumber = $accountnumber;
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
     * @return string
     */
    public function getTaxKey()
    {
        return $this->tax_key;
    }

    /**
     * @param string $tax_key
     */
    public function setTaxKey($tax_key)
    {
        $this->tax_key = $tax_key;
    }

    /**
     * @return float
     */
    public function getTaxAmount()
    {
        return $this->tax_amount;
    }

    /**
     * @param float $tax_amount
     */
    public function setTaxAmount($tax_amount)
    {
        $this->tax_amount = $tax_amount;
    }

    /**
     * @return string
     */
    public function getRevenueaccount()
    {
        return $this->revenueaccount;
    }

    /**
     * @param string $revenueaccount
     */
    public function setRevenueaccount($revenueaccount)
    {
        $this->revenueaccount = $revenueaccount;
    }
}