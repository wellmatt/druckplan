<?php
/**
 *  Copyright (c) 2017 Teuber Consult + IT GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <alexander.scherer@teuber-consult.de>, 2017
 *
 */
require_once 'libs/basic/model.php';

class ProducttemplateMachine extends Model{
    public $_table = 'producttemplates_machines';

    public $producttemplate;
    public $machine;

    public $default = 0;
    public $amount_from = 0;
    public $amount_to = 0;

    protected function bootClasses()
    {
        $this->producttemplate = new Producttemplate($this->producttemplate);
        $this->machine = new Machine($this->machine);
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
     * @return ProducttemplateMachine[]
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
     * @param Machine $machine
     * @return ProducttemplateMachine
     */
    public static function getForProducttemplateAndMachine(Producttemplate $producttemplate, Machine $machine)
    {
        $retval = self::fetchSingle([
            [
                'column'=>'producttemplate',
                'value'=>$producttemplate->getId()
            ],
            [
                'column'=>'machine',
                'value'=>$machine->getId()
            ]
        ]);
        return $retval;
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
            $ids[] = $item->getMachine()->getId();
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
     * @return Machine
     */
    public function getMachine()
    {
        return $this->machine;
    }

    /**
     * @param Machine $machine
     */
    public function setMachine($machine)
    {
        $this->machine = $machine;
    }

    /**
     * @return int
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * @param int $default
     */
    public function setDefault($default)
    {
        $this->default = $default;
    }

    /**
     * @return int
     */
    public function getAmountFrom()
    {
        return $this->amount_from;
    }

    /**
     * @param int $amount_from
     */
    public function setAmountFrom($amount_from)
    {
        $this->amount_from = $amount_from;
    }

    /**
     * @return int
     */
    public function getAmountTo()
    {
        return $this->amount_to;
    }

    /**
     * @param int $amount_to
     */
    public function setAmountTo($amount_to)
    {
        $this->amount_to = $amount_to;
    }
}