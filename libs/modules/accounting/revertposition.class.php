<?php
/**
 *  Copyright (c) 2017 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2017
 *
 */
require_once 'libs/basic/model.php';
require_once 'libs/modules/collectiveinvoice/collectiveinvoice.class.php';
require_once 'libs/modules/accounting/revert.class.php';
require_once 'libs/modules/taxkeys/taxkey.class.php';


class RevertPosition extends Model{
    public $_table = 'reverts_positions';

    public $revert = 0;
    public $opos = 0;
    public $amount = 0;
    public $taxkey = 0;
    public $price = 0.0;

    protected function bootClasses()
    {
        $this->revert = new Revert($this->revert);
        $this->opos = new Orderposition($this->opos);
        $this->taxkey = new TaxKey($this->taxkey);
    }

    /**
     * @param Revert $revert
     * @return RevertPosition[]
     */
    public static function getAllForRevert(Revert $revert)
    {
        $retval = self::fetch([
            [
                'column'=>'revert',
                'value'=>$revert->getId()
            ]
        ]);
        return $retval;
    }

    /**
     * @param Orderposition $orderposition
     * @return RevertPosition[]
     */
    public static function getAllForOpos(Orderposition $orderposition)
    {
        $retval = self::fetch([
            [
                'column'=>'opos',
                'value'=>$orderposition->getId()
            ]
        ]);
        return $retval;
    }

    /**
     * @return Revert
     */
    public function getRevert()
    {
        return $this->revert;
    }

    /**
     * @param Revert $revert
     */
    public function setRevert($revert)
    {
        $this->revert = $revert;
    }

    /**
     * @return Orderposition
     */
    public function getOpos()
    {
        return $this->opos;
    }

    /**
     * @param Orderposition $opos
     */
    public function setOpos($opos)
    {
        $this->opos = $opos;
    }

    /**
     * @return int
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param int $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * @return TaxKey
     */
    public function getTaxkey()
    {
        return $this->taxkey;
    }

    /**
     * @param TaxKey $taxkey
     */
    public function setTaxkey($taxkey)
    {
        $this->taxkey = $taxkey;
    }

    /**
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param float $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }
}