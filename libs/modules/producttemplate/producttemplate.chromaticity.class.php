<?php
/**
 *  Copyright (c) 2017 Teuber Consult + IT GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <alexander.scherer@teuber-consult.de>, 2017
 *
 */
require_once 'libs/basic/model.php';

class ProducttemplateChromaticity extends Model{
    public $_table = 'producttemplates_chromaticities';

    public $producttemplate;
    public $chromaticity;

    protected function bootClasses()
    {
        $this->producttemplate = new Producttemplate($this->producttemplate);
        $this->chromaticity = new Chromaticity($this->chromaticity);
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
     * @param Producttemplate $producttemplate
     * @return boolean
     */
    public static function deleteAllForProducttemplate(Producttemplate $producttemplate)
    {
        $retval = self::fetch([
            [
                'column'=>'producttemplate',
                'value'=>$producttemplate->getId()
            ]
        ]);
        foreach ($retval as $item) {
            $item->delete();
        }
        return true;
    }

    /**
     * @param Producttemplate $producttemplate
     * @return int[]
     */
    public static function getAllIdsForProducttemplate(Producttemplate $producttemplate)
    {
        $ids = [];
        $retval = self::fetch([
            [
                'column'=>'producttemplate',
                'value'=>$producttemplate->getId()
            ]
        ]);
        foreach ($retval as $item) {
            $ids[] = $item->getChromaticity()->getId();
        }
        return $ids;
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
     * @return Chromaticity
     */
    public function getChromaticity()
    {
        return $this->chromaticity;
    }

    /**
     * @param Chromaticity $chromaticity
     */
    public function setChromaticity($chromaticity)
    {
        $this->chromaticity = $chromaticity;
    }
}