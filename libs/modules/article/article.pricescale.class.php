<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */
require_once 'libs/basic/model.php';
require_once 'libs/modules/article/article.class.php';
require_once 'libs/modules/businesscontact/businesscontact.class.php';

class PriceScale extends Model{
    public $_table = 'article_pricescale';

    public $article = 0;        // zugeordneter Artikel
    public $type = 1;           // Type EK/VK
    public $min = 0;            // Min. Menge
    public $max = 0;            // Max. Menge
    public $price = 0.0;        // Preis
    public $supplier = 0;       // Lieferant (nur bei EK)
    public $artnum = '';        // Lieferant Art Num

    const TYPE_SELL = 1;
    const TYPE_BUY = 2;

    protected function bootClasses()
    {
        $this->article = new Article((int)$this->article);
        $this->supplier = new BusinessContact((int)$this->supplier);
    }

    /**
     * @param Article $article
     * @param int $type
     * @return PriceScale[]
     */
    public static function getAllForArticle(Article $article, $type = self::TYPE_SELL)
    {
        $retval = self::fetch([
            [
                'column'=>'article',
                'value'=>$article->getId()
            ],
            [
                'column'=>'type',
                'value'=>$type
            ]
        ]);
        return $retval;
    }

    /**
     * @param Article $article
     * @param BusinessContact $supplier
     * @param int $type
     * @return PriceScale[]
     */
    public static function getAllForArticleAndSupplier(Article $article, BusinessContact $supplier,$type = self::TYPE_SELL)
    {
        $retval = self::fetch([
            [
                'column'=>'article',
                'value'=>$article->getId()
            ],
            [
                'column'=>'supplier',
                'value'=>$supplier->getId()
            ],
            [
                'column'=>'type',
                'value'=>$type
            ]
        ]);
        return $retval;
    }

    /**
     * @param Article $article
     * @param int $amount
     * @param int $type
     * @return float
     */
    public static function getPriceForAmount(Article $article, $amount, $type = self::TYPE_SELL)
    {
        $retval = self::fetchSingle([
            [
                'column'=>'article',
                'value'=>$article->getId()
            ],
            [
                'column'=>'type',
                'value'=>$type
            ],
            [
                'column'=>'min',
                'operator'=>'<=',
                'value'=>$amount
            ],
            [
                'column'=>'max',
                'operator'=>'>=',
                'value'=>$amount
            ],
            [
                'orderby'=>'price'
            ]
        ]);
        if ($retval->getId()>0)
            return tofloat($retval->getPrice());
        else {
            $retval = self::fetchSingle([
                [
                    'column'=>'article',
                    'value'=>$article->getId()
                ],
                [
                    'column'=>'type',
                    'value'=>$type
                ],
                [
                    'orderby'=>'price'
                ]
            ]);
            if ($retval->getId()>0)
                return tofloat($retval->getPrice());
            else
                return 0;
        }
    }

    /**
     * @param Article $article
     * @param int $amount
     * @param int $type
     * @return PriceScale
     */
    public static function getPriceScaleForAmount(Article $article, $amount, $type = self::TYPE_SELL)
    {
        $retval = self::fetchSingle([
            [
                'column'=>'article',
                'value'=>$article->getId()
            ],
            [
                'column'=>'type',
                'value'=>$type
            ],
            [
                'column'=>'min',
                'operator'=>'>=',
                'value'=>$amount
            ],
            [
                'column'=>'max',
                'operator'=>'<=',
                'value'=>$amount
            ],
            [
                'orderby'=>'price'
            ]
        ]);
        if ($retval)
            return $retval;
        else {
            $retval = self::fetchSingle([
                [
                    'column'=>'article',
                    'value'=>$article->getId()
                ],
                [
                    'column'=>'type',
                    'value'=>$type
                ],
                [
                    'orderby'=>'price'
                ]
            ]);
            if ($retval)
                return $retval;
            else
                return false;
        }
    }

    /**
     * @param Article $article
     * @param int $type
     */
    public static function deleteAllForArticle(Article $article, $type = self::TYPE_BUY)
    {
        $retval = self::fetch([
            [
                'column'=>'article',
                'value'=>$article->getId()
            ],
            [
                'column'=>'type',
                'value'=>$type
            ]
        ]);
        if ($retval){
            foreach ($retval as $item) {
                $item->delete();
            }
        }
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
    public function getMin()
    {
        return $this->min;
    }

    /**
     * @param int $min
     */
    public function setMin($min)
    {
        $this->min = $min;
    }

    /**
     * @return int
     */
    public function getMax()
    {
        return $this->max;
    }

    /**
     * @param int $max
     */
    public function setMax($max)
    {
        $this->max = $max;
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

    /**
     * @return BusinessContact
     */
    public function getSupplier()
    {
        return $this->supplier;
    }

    /**
     * @param BusinessContact $supplier
     */
    public function setSupplier($supplier)
    {
        $this->supplier = $supplier;
    }

    /**
     * @return string
     */
    public function getArtnum()
    {
        return $this->artnum;
    }

    /**
     * @param string $artnum
     */
    public function setArtnum($artnum)
    {
        $this->artnum = $artnum;
    }
}