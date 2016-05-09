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


class ArticleTag extends Model
{
    public $_table = 'article_tags';
    public $article = 0;
    public $tag = '';

    /**
     * @param Article $article
     * @return ArticleTag[]
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
        $ret = [];
        $retval = self::fetch([
            [
                'column' => 'article',
                'value' => $article->getId()
            ]
        ]);
        if ($retval)
            foreach ($retval as $item) {
                $ret[] = $item->getTag();
            }
        return $ret;
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

    /**
     * @return string
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * @param string $tag
     */
    public function setTag($tag)
    {
        $this->tag = $tag;
    }
}