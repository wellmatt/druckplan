<?php
/**
 *  Copyright (c) 2018 Teuber Consult + IT GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <alexander.scherer@teuber-consult.de>, 2018
 *
 */
require_once 'libs/basic/model.php';
require_once 'libs/modules/article/article.class.php';
require_once 'libs/modules/partslists/partslist.class.php';


class CalculationArticle extends Model{
    public $_table = 'orders_calculations_articles';
    public $calc = 0;
    public $article = 0;
    public $amount = 0.00;
    public $type = 1;

    CONST TYPE_MANUAL = 1;
    CONST TYPE_PERAMOUNT = 2;

    protected function bootClasses()
    {
        $this->calc = new Calculation($this->calc);
        $this->article = new Article($this->article);
    }

    /**
     * @return CalculationArticle[]
     */
    public static function getAll()
    {
        $retval = self::fetch();
        return $retval;
    }

    /**
     * @param Calculation $calc
     * @return CalculationArticle[]
     */
    public static function getAllForCalc(Calculation $calc)
    {
        $retval = self::fetch([
            [
                'column'=>'calc',
                'value'=>$calc->getId()
            ]
        ]);
        return $retval;
    }

    public function getTotalAmount()
    {
        switch ($this->type){
            case 1:
                return $this->amount;
            case 2:
                return $this->getCalc()->getAmount() * $this->amount;
            default:
                return 0;
        }
    }

    public function clearId()
    {
        $this->id = 0;
    }

    /**
     * @return Calculation
     */
    public function getCalc()
    {
        return $this->calc;
    }

    /**
     * @param Calculation $calc
     */
    public function setCalc($calc)
    {
        $this->calc = $calc;
    }

    /**
     * @return Article
     */
    public function getArticle()
    {
        return $this->article;
    }

    /**
     * @param Article $article
     */
    public function setArticle($article)
    {
        $this->article = $article;
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
}