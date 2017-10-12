<?php
/**
 *  Copyright (c) 2017 Teuber Consult + IT GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <alexander.scherer@teuber-consult.de>, 2017
 *
 */
require_once 'libs/basic/model.php';

class ProducttemplatePaper extends Model{
    public $_table = 'producttemplates_papers';

    public $producttemplate;
    public $paper;

    protected function bootClasses()
    {
        $this->producttemplate = new Producttemplate($this->producttemplate);
        $this->paper = new Paper($this->paper);
    }

    /**
     * @param Producttemplate $producttemplate
     * @return ProducttemplatePaper[]
     */
    public static function getAllForProducttemplate(Producttemplate $producttemplate)
    {
        $retval = self::fetch([
            [
                'column'=>'producttemplate',
                'value'=>$producttemplate->getId()
            ]
        ]);
        return $retval;
    }

    /**
     * @return Producttemplate
     */
    public function getProducttemplate()
    {
        return $this->producttemplate;
    }

    /**
     * @param Producttemplate $producttemplate
     */
    public function setProducttemplate($producttemplate)
    {
        $this->producttemplate = $producttemplate;
    }

    /**
     * @return Paper
     */
    public function getPaper()
    {
        return $this->paper;
    }

    /**
     * @param Paper $paper
     */
    public function setPaper($paper)
    {
        $this->paper = $paper;
    }
}