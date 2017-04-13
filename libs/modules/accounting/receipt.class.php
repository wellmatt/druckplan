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
require_once 'invoiceout.class.php';
require_once 'revert.class.php';


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
    public $exported = 0;

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
     * @param array $filterarray
     * @param int $single
     * @return Receipt[]
     */
    public static function fetch($filterarray = Array(), $single = 0)
    {
        return parent::fetch($filterarray, $single);
    }

    /**
     * Validates the receipt for successful export
     * @return array
     */
    public function validate()
    {
        $fatal = [];
        $warning = [];
        $info = [];

        if ($this->getOriginType() == self::ORIGIN_INVOICE) {
            $positions = Orderposition::getAllOrderposition($this->_origin->getColinv()->getId());
            if (count($positions) == 0)
                $warning[] = 'Keine Positionen!';
            foreach ($positions as $position) {
                if ($position->getStatus() == 1){
                    $revenue = new RevenueaccountCategory();
                    $costobject = new CostObject();
                    $article = $position->getMyArticle();
                    if ($article->getId()>0){
                        if ($article->getTradegroup()->getId() == 0)
                            $fatal[] = 'Artikel hat keine Warengruppe!';
                        if ($article->getCostobject()->getId() > 0) {
                            $costobject = $article->getCostobject();
                            $info[] = 'Kostenstelle aus Artikel ('.$article->getTitle().') übernommen';
                        } else if ($article->getTradegroup()->getRecursiveCostobject()->getId()>0) {
                            $costobject = $article->getTradegroup()->getRecursiveCostobject();
                            $info[] = 'Kostenstelle aus Warengruppe ('.$article->getTradegroup()->getTitle().') übernommen';
                        }
                        if ($article->getRevenueaccount()->getId() > 0) {
                            $revenue = $article->getRevenueaccount();
                            $info[] = 'Erlöskategorie ('.$revenue->getTitle().') aus Artikel ('.$article->getTitle().') übernommen';
                        } else if ($article->getTradegroup()->getRecursiveRevenueaccount()->getId() > 0) {
                            $revenue = $article->getTradegroup()->getRecursiveRevenueaccount();
                            $info[] = 'Erlöskategorie ('.$revenue->getTitle().') aus Warengruppe ('.$article->getTradegroup()->getTitle().') übernommen';
                        }
                    } else {
                        $fatal[] = 'Artikel nicht gefunden!';
                    }
                    if ($revenue->getId() == 0){
                        $fatal[] = 'Erlöskategorie nicht gesetzt!';
                    }
                    if ($costobject->getId() == 0){
                        $fatal[] = 'Kostenstelle nicht gesetzt!';
                    }

                    $revenueaccount = RevenueAccount::fetchForCategoryAndTaxkeyOrDefault($revenue, $position->getTaxkey());
                    $revenueaccount_default = RevenueAccount::fetchDefaultForCategory($revenue);
                    if ($revenue->getId() > 0 && $revenueaccount->getId() == $revenueaccount_default->getId()){
                        $warning[] = 'Keine Übereinstimmung Erlöskategorie + Steuerschlüssel, benutze Standard der Kategorie: '.$revenue->getTitle();
                    }
                    if ($revenueaccount->getId() == 0){
                        $fatal[] = 'Erlöskonto nicht gesetzt!';
                    }
                    if ($this->_origin->getColinv()->getBusinesscontact()->getCustomernumber() == null || $this->_origin->getColinv()->getBusinesscontact()->getCustomernumber() == '')
                        $fatal[] = 'Kundennummer nicht gesetzt!';
                }
            }
        } else {
            $positions = RevertPosition::getAllForRevert($this->getOrigin());
            if (count($positions) == 0)
                $warning[] = 'Keine Positionen!';
            foreach ($positions as $position) {
                $revenue = new RevenueaccountCategory();
                $costobject = new CostObject();
                $article = $position->getOpos()->getMyArticle();
                if ($article->getId()>0){
                    if ($article->getTradegroup()->getId() == 0)
                        $fatal[] = 'Artikel hat keine Warengruppe!';
                    if ($article->getCostobject()->getId() > 0) {
                        $costobject = $article->getCostobject();
                        $info[] = 'Kostenstelle aus Artikel ('.$article->getTitle().') übernommen';
                    } else if ($article->getTradegroup()->getRecursiveCostobject()->getId() > 0) {
                        $costobject = $article->getTradegroup()->getRecursiveCostobject();
                        $info[] = 'Kostenstelle aus Warengruppe ('.$article->getTradegroup()->getTitle().') übernommen';
                    }
                    if ($article->getRevenueaccount()->getId() > 0) {
                        $revenue = $article->getRevenueaccount();
                        $info[] = 'Erlöskategorie ('.$revenue->getTitle().') aus Artikel ('.$article->getTitle().') übernommen';
                    } else if ($article->getTradegroup()->getRecursiveRevenueaccount()->getId() > 0) {
                        $revenue = $article->getTradegroup()->getRecursiveRevenueaccount();
                        $info[] = 'Erlöskategorie ('.$revenue->getTitle().') aus Warengruppe ('.$article->getTradegroup()->getTitle().') übernommen';
                    }
                } else {
                    $fatal[] = 'Artikel nicht gefunden!';
                }
                if ($revenue->getId() == 0){
                    $fatal[] = 'Erlöskategorie nicht gesetzt!';
                }
                if ($costobject->getId() == 0){
                    $fatal[] = 'Kostenstelle nicht gesetzt!';
                }

                $revenueaccount = RevenueAccount::fetchForCategoryAndTaxkeyOrDefault($revenue, $position->getTaxkey());
                $revenueaccount_default = RevenueAccount::fetchDefaultForCategory($revenue);
                if ($revenue->getId() > 0 && $revenueaccount->getId() == $revenueaccount_default->getId()){
                    $warning[] = 'Keine Übereinstimmung Erlöskategorie + Steuerschlüssel, benutze Standard der Kategorie: '.$revenue->getTitle();
                }
                if ($revenueaccount->getId() == 0){
                    $fatal[] = 'Erlöskonto nicht gesetzt!';
                }
                if ($this->_origin->getColinv()->getBusinesscontact()->getCustomernumber() == null || $this->_origin->getColinv()->getBusinesscontact()->getCustomernumber() == '')
                    $fatal[] = 'Kundennummer nicht gesetzt!';
            }
        }
        $errors = ['fatal'=>$fatal,'warning'=>$warning,'info'=>$info];
        return $errors;
    }

    public static function formatValidation($errors, $break = '/r/n')
    {
        $res = '';
        if (count($errors['info'])>0){
            $res .= 'INFO:'.$break;
            foreach ($errors['info'] as $info) {
                $res .= '-> '.$info.$break;
            }
        }
        if (count($errors['warning'])>0){
            $res .= 'WARNUNG:'.$break;
            foreach ($errors['warning'] as $warning) {
                $res .= '-> '.$warning.$break;
            }
        }
        if (count($errors['fatal'])>0){
            $res .= 'FEHLER:'.$break;
            foreach ($errors['fatal'] as $fatal) {
                $res .= '-> '.$fatal.$break;
            }
        }
        return $res;
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
            $origin_type = self::ORIGIN_REVERT;
            $origin_id = $origin->getId();
        } else {
            return false;
        }
        $number = $origin->getNumber();
        $date = $origin->getCrtdate();

        if ($origin_type == self::ORIGIN_INVOICE)
            $description = 'Rechnung zu Auftrag '.$origin->getColinv()->getNumber();
        else
            $description = 'Gutschrift zu Auftrag '.$origin->getColinv()->getNumber();
        $positions = Orderposition::getAllOrderposition($origin->getColinv()->getId());
        if (count($positions)>0){
            $description_tmp = substr($positions[0]->getCommentStripped(),0,49);
            if (strlen($description_tmp)>0)
                $description = $description_tmp;
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

            $creditposition = [
                'receipt'=>$this->getId(),
                'type'=>1,
                'postingkey'=>210,
                'accountnumber'=>$this->getOrigin()->getColinv()->getBusinesscontact()->getCustomernumber(),
                'amount' => 0.0
            ];

            foreach ($positions as $position) {
                if ($position->getStatus() == 1){

                    if ($position->getType() == 1){
                        $netto = $position->getPrice();
                        $gross = $netto * (1+($position->getTax()/100));
                    } else {
                        $netto = $position->getPrice() * $position->getAmount();
                        $gross = $netto * (1+($position->getTax()/100));
                    }

                    $creditposition['amount'] = $creditposition['amount'] + $gross;

                    // create debit positon
                    $costobject = '';
                    $revenue = new RevenueaccountCategory();
                    $article = $position->getMyArticle();
                    if ($article->getId()>0){
                        if ($article->getTradegroup()->getRecursiveCostobject()->getId()>0) {
                            $tmp_cost_obj = $article->getTradegroup()->getRecursiveCostobject();
                            $costobject = $tmp_cost_obj->getNumber();
                        }
                        if ($article->getCostobject()->getId()>0)
                            $costobject = $article->getCostobject()->getNumber();
                    }
                    if ($article->getId()>0){
                        if ($article->getTradegroup()->getRecursiveRevenueaccount()->getId()>0)
                            $revenue = $article->getTradegroup()->getRecursiveRevenueaccount();
                        if ($article->getRevenueaccount()->getId()>0)
                            $revenue = $article->getRevenueaccount();
                    }
                    $revenueaccount = RevenueAccount::fetchForCategoryAndTaxkeyOrDefault($revenue, $position->getTaxkey());
                    $array = [
                        'receipt' => $this->getId(),
                        'type' => 2,
                        'postingkey' => 150,
                        'accountnumber' => $costobject,
                        'amount' => $gross,
                        'tax_key' => $position->getTaxkey()->getKey(),
                        'tax_amount' => ($gross-$netto),
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
                        'amount' => ($gross-$netto),
                        'percent' => $position->getTaxkey()->getValue(),
                    ];
                    $rctp_pos_tax = new ReceiptTaxPosition(0,$array);
                    $rctp_pos_tax = $rctp_pos_tax->save();
                    if ($rctp_pos_tax === false)
                        return false;
                }
            }

            if ($this->getOrigin()->getColinv()->getDeliveryterm()->getId()>0){
                $deliv = $this->getOrigin()->getColinv()->getDeliveryterm();
                if ($deliv->getCharges() > 0){
                    $netvalue = $deliv->getCharges() - ($deliv->getCharges()/100*$deliv->getTaxkey()->getValue());
                    $grossvalue = $deliv->getCharges();

                    $creditposition['amount'] = $creditposition['amount'] + $grossvalue;

                    $revenue = new RevenueaccountCategory();
                    if ($deliv->getRevenueaccount()->getId()>0)
                        $revenue = $deliv->getRevenueaccount();
                    $revenueaccount = RevenueAccount::fetchForCategoryAndTaxkeyOrDefault($revenue, $deliv->getTaxkey());

                    $array = [
                        'receipt' => $this->getId(),
                        'type' => 2,
                        'postingkey' => 150,
                        'accountnumber' => $deliv->getCostobject()->getNumber(),
                        'amount' => $deliv->getCharges(),
                        'tax_key' => $deliv->getTaxkey()->getKey(),
                        'tax_amount' => ($grossvalue-$netvalue),
                        'revenueaccount' => $revenueaccount->getNumber(),
                    ];
                    $rctp_pos_debit = new ReceiptPosition(0,$array);
                    $rctp_pos_debit = $rctp_pos_debit->save();
                    if ($rctp_pos_debit === false)
                        return false;

                    // create tax positon
                    $array = [
                        'receipt' => $this->getId(),
                        'key' => $deliv->getTaxkey()->getKey(),
                        'amount' => ($grossvalue-$netvalue),
                        'percent' => $deliv->getTaxkey()->getValue(),
                    ];
                    $rctp_pos_tax = new ReceiptTaxPosition(0,$array);
                    $rctp_pos_tax = $rctp_pos_tax->save();
                    if ($rctp_pos_tax === false)
                        return false;

                }
            }

            // create credit positon
            $rctp_pos_credit = new ReceiptPosition(0,$creditposition);
            $rctp_pos_credit = $rctp_pos_credit->save();
            if ($rctp_pos_credit === false)
                return false;

        } else {
            $positions = RevertPosition::getAllForRevert($this->getOrigin());

            $creditposition = [
                'receipt'=>$this->getId(),
                'type'=>1,
                'postingkey'=>210,
                'accountnumber'=>$this->getOrigin()->getColinv()->getBusinesscontact()->getCustomernumber(),
                'amount' => 0.0
            ];

            foreach ($positions as $position) {

                $netto = $position->getPrice();
                $gross = $netto * (1+($position->getTaxkey()->getValue()/100));

                $creditposition['amount'] = $creditposition['amount'] + $gross;

                // create debit positon
                $costobject = '';
                $revenue = new RevenueaccountCategory();
                $article = $position->getOpos()->getMyArticle();
                if ($article->getId()>0){
                    if ($article->getTradegroup()->getRecursiveCostobject()->getId()>0) {
                        $tmp_cost_obj = $article->getTradegroup()->getRecursiveCostobject();
                        $costobject = $tmp_cost_obj->getNumber();
                    }
                    if ($article->getCostobject()->getId()>0)
                        $costobject = $article->getCostobject()->getNumber();
                }
                if ($article->getId()>0){
                    if ($article->getTradegroup()->getRecursiveRevenueaccount()->getId()>0)
                        $revenue = $article->getTradegroup()->getRecursiveRevenueaccount();
                    if ($article->getRevenueaccount()->getId()>0)
                        $revenue = $article->getRevenueaccount();
                }
                $revenueaccount = RevenueAccount::fetchForCategoryAndTaxkeyOrDefault($revenue, $position->getTaxkey());
                $array = [
                    'receipt' => $this->getId(),
                    'type' => 2,
                    'postingkey' => 110,
                    'accountnumber' => $costobject,
                    'amount' => $gross,
                    'tax_key' => $position->getTaxkey()->getKey(),
                    'tax_amount' => ($gross-$netto),
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
                    'amount' => ($gross-$netto),
                    'percent' => $position->getTaxkey()->getValue(),
                ];
                $rctp_pos_tax = new ReceiptTaxPosition(0,$array);
                $rctp_pos_tax = $rctp_pos_tax->save();
                if ($rctp_pos_tax === false)
                    return false;
            }

            // create credit positon
            $rctp_pos_credit = new ReceiptPosition(0,$creditposition);
            $rctp_pos_credit = $rctp_pos_credit->save();
            if ($rctp_pos_credit === false)
                return false;

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
            $origin_type = self::ORIGIN_REVERT;
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
     * @param string $datemin
     * @param string $datemax
     * @param int $exported
     * @return Receipt[]
     */
    public static function getAllFiltered($datemin, $datemax, $exported = 0)
    {
        if (strtotime($datemin) == 0 || strtotime($datemax) == 0){
            $datemin = date("d.m.y",mktime(0,0,1,date("m",time()),1,date("y",time())));
            $datemax = date("d.m.y",mktime(0,0,1,date("m",time()),date("t",time()),date("y",time())));
        }

        $filters = [
            [
                'column'=>'date',
                'value'=>strtotime($datemin),
                'operator'=>'>='
            ],
            [
                'column'=>'date',
                'value'=>strtotime($datemax),
                'operator'=>'<='
            ]
        ];

        if ($exported == 0){
            $filters[] = [
                    'column'=>'exported',
                    'value'=>$exported
            ];
        }

        $receipts = self::fetch($filters);

        return $receipts;
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

    /**
     * @return InvoiceOut|Revert
     */
    public function getOrigin()
    {
        return $this->_origin;
    }

    /**
     * @return int
     */
    public function getExported()
    {
        return $this->exported;
    }

    /**
     * @param int $exported
     */
    public function setExported($exported)
    {
        $this->exported = $exported;
    }
}