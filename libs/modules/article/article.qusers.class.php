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


class ArticleQualifiedUser extends Model
{
    public $_table = 'article_qualified_users';
    public $article = 0;
    public $user = 0;

    protected function bootClasses()
    {
        $this->user = new User((int)$this->user);
    }

    /**
     * @param Article $article
     * @return ArticleQualifiedUser[]
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
        $retval = self::fetch([
            [
                'column' => 'article',
                'value' => $article->getId()
            ]
        ]);
        $ret = [];
        if ($retval)
            foreach ($retval as $item) {
                $ret[] = $item->getUser();
            }
        return $ret;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
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