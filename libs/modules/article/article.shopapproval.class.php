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


class ArticleShopApproval extends Model
{
    public $_table = 'article_shop_approval';
    public $article = 0;
    public $bc = 0;
    public $cp = 0;

    /**
     * @param Article $article
     * @return ArticleShopApproval[]
     */
    public static function getAllForArticle(Article $article)
    {
        $retval = self::fetch([
            [
                'column' => 'article',
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
        $ret = Array("BCs" => Array(), "CPs" => Array());
        $retval = self::fetch([
            [
                'column' => 'article',
                'value' => $article->getId()
            ]
        ]);
        if ($retval)
            foreach ($retval as $item) {
                if ($item->getBc() > 0)
                    $ret["BCs"][] = $item->getBc();
                elseif ($item->getCp() > 0)
                    $ret["CPs"][] = $item->getCp();
            }
        return $ret;
    }

    /**
     * @return int
     */
    public function getCp()
    {
        return $this->cp;
    }

    /**
     * @param int $cp
     */
    public function setCp($cp)
    {
        $this->cp = $cp;
    }

    /**
     * @return int
     */
    public function getBc()
    {
        return $this->bc;
    }

    /**
     * @param int $bc
     */
    public function setBc($bc)
    {
        $this->bc = $bc;
    }

    /**
     * @return int
     */
    public function getArticle()
    {
        return $this->article;
    }

    /**
     * @param int $article
     */
    public function setArticle($article)
    {
        $this->article = $article;
    }
}