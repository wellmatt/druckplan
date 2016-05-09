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


class ArticleOrderAmount extends Model
{
    public $_table = 'article_orderamounts';
    public $article_id = 0;
    public $amount = 0;

    /**
     * @param Article $article
     * @return ArticleOrderAmount[]
     */
    public static function getAllForArticle(Article $article)
    {
        $retval = self::fetch([
            [
                'column' => 'article_id',
                'value' => $article->getId()
            ]
        ]);
        return $retval;
    }

    /**
     * @param Article $article
     * @return array
     */
    public static function getAllForArticleAsArray(Article $article)
    {
        $retval = self::fetch([
            [
                'column' => 'article_id',
                'value' => $article->getId()
            ]
        ]);
        $ret = [];
        if ($retval)
            foreach ($retval as $item) {
                $ret[] = $item->getAmount();
            }
        return $ret;
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
     * @return int
     */
    public function getArticleId()
    {
        return $this->article_id;
    }

    /**
     * @param int $article_id
     */
    public function setArticleId($article_id)
    {
        $this->article_id = $article_id;
    }
}