<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */
require_once 'libs/basic/model.php';
require_once 'libs/modules/suporder/suporder.class.php';
require_once 'libs/modules/collectiveinvoice/collectiveinvoice.class.php';

class SupOrderPosition extends Model
{
    public $_table = 'suporders_positions';

    public $suporder = 0;
    public $article = 0;
    public $amount = 0.0;
    public $colinvoice = 0;

    protected function bootClasses()
    {
        $this->suporder = new SupOrder($this->suporder);
        $this->article = new Article($this->article);
        $this->colinvoice = new CollectiveInvoice($this->colinvoice);
    }

    /**
     * @param SupOrder $supOrder
     * @return SupOrderPosition[]
     */
    public static function getAllForSupOrder(SupOrder $supOrder)
    {
        $positions = [];

        $retval = self::fetch([
            [
                'column'=>'suporder',
                'value'=>$supOrder->getId()
            ]
        ]);

        if ($retval){
            foreach ($retval as $position) {
                if ($position->getArticle()->getUsesstorage())
                    $positions[] = $position;
            }
        }

        return $positions;
    }

    /**
     * @return SupOrder
     */
    public function getSuporder()
    {
        return $this->suporder;
    }

    /**
     * @param SupOrder $suporder
     */
    public function setSuporder($suporder)
    {
        $this->suporder = $suporder;
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
     * @return CollectiveInvoice
     */
    public function getColinvoice()
    {
        return $this->colinvoice;
    }

    /**
     * @param CollectiveInvoice $colinvoice
     */
    public function setColinvoice($colinvoice)
    {
        $this->colinvoice = $colinvoice;
    }
}