<?php
/**
 *  Copyright (c) 2017 Teuber Consult + IT GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <alexander.scherer@teuber-consult.de>, 2017
 *
 */
require_once 'material.paper.class.php';
require_once 'material.roll.class.php';
require_once 'material.printingplate.class.php';
require_once 'material.tool.class.php';
require_once 'material.finish.class.php';
require_once 'material.packing.class.php';


class Material extends Model{
    public $name = '';
    public $type = 0;
    public $description = '';
    public $article;

    const TYPE_PAPER = 1;
    const TYPE_ROLL = 2;
    const TYPE_PRINTINGPLATE = 3;
    const TYPE_TOOL = 4;
    const TYPE_FINISH = 5;
    const TYPE_PACKING = 6;

    public static function getTypeArray()
    {
        return [
            ['id' => 1, 'name' => 'Papier'],
            ['id' => 2, 'name' => 'Rolle'],
            ['id' => 3, 'name' => 'Druckplatte'],
            ['id' => 4, 'name' => 'Werkzeug'],
            ['id' => 5, 'name' => 'Lack'],
            ['id' => 6, 'name' => 'Verpackung'],
        ];
    }

    protected function bootClasses()
    {
        $this->article = new Article($this->article);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
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
}