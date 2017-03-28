<?php
/**
 *  Copyright (c) 2017 Teuber Consult + IT GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <alexander.scherer@teuber-consult.de>, 2017
 *
 */
require_once 'libs/basic/model.php';
require_once 'receipt.position.class.php';
require_once 'receipt.taxposition.class.php';
require_once 'FibuXML.class.php';


class Receipt extends Model{
    public $_table = 'receipts';

    CONST ORIGIN_INVOICE = 1;
    CONST ORIGIN_REVERT = 2;

    public $origin_type = 1;                // ursprungsart
    public $origin_id;                      // ursprungsid

    public $number = '';                    // invoice number
    public $date = 0;                       // invoice date
    public $currency = 'EUR';               // default EUR
    public $description = '';               // max 50 chars

    public $_receiptpositions = [];
    public $_receipttaxpositions = [];
    public $_origin;

    protected function bootClasses()
    {
        $this->_receiptpositions = ReceiptPosition::getAllForReceipt($this);
        $this->_receipttaxpositions = ReceiptTaxPosition::getAllForReceipt($this);
        if ($this->origin_type == self::ORIGIN_INVOICE) {
            $this->_origin = new InvoiceOut($this->origin_id);
        } elseif ($this->origin_type == self::ORIGIN_REVERT) {
            $this->_origin = new Revert($this->origin_id);
        }
    }

    /**
     * @param InvoiceOut|Revert $origin
     * @return boolean|Receipt
     */
    public static function generateReceipt($origin)
    {
        if (is_a($origin,"InvoiceOut")) {
            $origin_type = self::ORIGIN_INVOICE;
            $origin_id = $origin->getId();
        } elseif (is_a($origin,"Revert")) {
            $origin_type = self::ORIGIN_INVOICE;
            $origin_id = $origin->getId();
        } else {
            return false;
        }
        $number = $origin->getNumber();
        $date = $origin->getCrtdate();

        $description = 'Buchung zu '.$number;
        $positions = Orderposition::getAllOrderposition($origin->getColinv()->getId());
        if (count($positions)>0){
            $description = substr($positions[0]->getCommentClean(),0,49);
        }

        $array = [
            'number' => $number,
            'origin_type' => $origin_type,
            'origin_id' => $origin_id,
            'date' => $date,
            'description' => $description,
        ];

        $receipt = new Receipt(0,$array);
        $ret = $receipt->save();
        if ($ret) {

            $posret = $receipt->generatePositions();
            if ($posret)
                return $receipt;
            else {
                $receipt->delete();
                return false;
            }
        } else
            return false;
    }

    private function generatePositions(){
        if ($this->getOriginType() == self::ORIGIN_INVOICE) {
            $positions = Orderposition::getAllOrderposition($this->_origin->getColinv()->getId());
            foreach ($positions as $position) {
                // create credit positon
                $array = [
                    'receipt' => $this->getId(),
                    'type' => 1,
                    'postingkey' => 210,
                    'accountnumber' => $this->_origin->getColinv()->getBusinesscontact()->getCustomernumber(),
                    'amount' => $position->getGross(),
                ];
                $rctp_pos_credit = new ReceiptPosition(0,$array);
                $rctp_pos_credit = $rctp_pos_credit->save();
                if ($rctp_pos_credit === false)
                    return false;

                // create debit positon
                $costobject = '';
                $revenue = new RevenueaccountCategory();
                $article = $position->getMyArticle();
                if ($article->getId()>0){
                    if ($article->getTradegroup()->getCostobject()->getId()>0)
                        $costobject = $article->getTradegroup()->getCostobject()->getNumber();
                    if ($article->getCostobject()->getId()>0)
                        $costobject = $article->getCostobject()->getNumber();
                }
                if ($article->getId()>0){
                    if ($article->getTradegroup()->getRevenueaccount()->getId()>0)
                        $revenue = $article->getTradegroup()->getRevenueaccount();
                    if ($article->getRevenueaccount()->getId()>0)
                        $revenue = $article->getRevenueaccount();
                }
                $revenueaccount = RevenueAccount::fetchForCategoryAndTaxkeyOrDefault($revenue, $position->getTaxkey());
                $array = [
                    'receipt' => $this->getId(),
                    'type' => 2,
                    'postingkey' => 150,
                    'accountnumber' => $costobject,
                    'amount' => $position->getGross(),
                    'tax_key' => $position->getTaxkey()->getKey(),
                    'tax_amount' => ($position->getGross()-$position->getPrice()),
                    'revenueaccount' => $revenueaccount->getNumber(),
                ];
                $rctp_pos_debit = new ReceiptPosition(0,$array);
                $rctp_pos_debit = $rctp_pos_debit->save();
                if ($rctp_pos_debit === false)
                    return false;

                // create tax positon
                $array = [
                    'receipt' => $this->getId(),
                    'key' => $position->getTaxkey()->getKey(),
                    'amount' => ($position->getGross()-$position->getPrice()),
                    'percent' => $position->getTaxkey()->getValue(),
                ];
                $rctp_pos_tax = new ReceiptTaxPosition(0,$array);
                $rctp_pos_tax = $rctp_pos_tax->save();
                if ($rctp_pos_tax === false)
                    return false;
            }
        } else {

        }
        return true;
    }

    /**
     * @param InvoiceOut|Revert $origin
     * @return Receipt
     */
    public static function getForOrigin($origin)
    {
        if (is_a($origin,"InvoiceOut")) {
            $origin_type = self::ORIGIN_INVOICE;
            $origin_id = $origin->getId();
        } elseif (is_a($origin,"Revert")) {
            $origin_type = self::ORIGIN_INVOICE;
            $origin_id = $origin->getId();
        } else {
            return new Receipt();
        }
        $retval = self::fetchSingle([
            [
                'column'=>'origin_id',
                'value'=>$origin_id
            ],
            [
                'column'=>'origin_type',
                'value'=>$origin_type
            ]
        ]);
        return $retval;
    }

    /**
     * Override default delete to also delete all associated objects
     */
    public function delete()
    {
        foreach ($this->_receiptpositions as $rcpt_pos) {
            $rcpt_pos->delete();
        }
        foreach ($this->_receipttaxpositions as $rcpt_taxpos) {
            $rcpt_taxpos->delete();
        }
        parent::delete();
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
     * @return int
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param int $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
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
     * @return int
     */
    public function getOriginType()
    {
        return $this->origin_type;
    }

    /**
     * @param int $origin_type
     */
    public function setOriginType($origin_type)
    {
        $this->origin_type = $origin_type;
    }

    /**
     * @return mixed
     */
    public function getOriginId()
    {
        return $this->origin_id;
    }

    /**
     * @param mixed $origin_id
     */
    public function setOriginId($origin_id)
    {
        $this->origin_id = $origin_id;
    }

    /**
     * @return ReceiptPosition[]
     */
    public function getReceiptpositions()
    {
        return $this->_receiptpositions;
    }

    /**
     * @return ReceiptTaxPosition[]
     */
    public function getReceipttaxpositions()
    {
        return $this->_receipttaxpositions;
    }
}