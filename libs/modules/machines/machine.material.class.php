<?php
/**
 *  Copyright (c) 2017 Teuber Consult + IT GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <alexander.scherer@teuber-consult.de>, 2017
 *
 */


class MachineMaterial extends Model{
    public $_table = 'machines_materials';

    public $machine;
    public $material;
    public $materialtype = 0;
    public $operator = 0;
    public $modifier = 0;
    public $amount = 0;

    protected function bootClasses()
    {
        $this->machine = new Machine($this->machine);
        switch ($this->materialtype){
            case 1: // Paper
                $this->material = new MaterialPaper($this->material);
                break;
            case 2: // Roll
                $this->material = new MaterialRoll($this->material);
                break;
            case 3: // Printingplate
                $this->material = new MaterialPrintingplate($this->material);
                break;
            case 4: // Tool
                $this->material = new MaterialTool($this->material);
                break;
            case 5: // Finish
                $this->material = new MaterialFinish($this->material);
                break;
            case 6: // Packing
                $this->material = new MaterialPacking($this->material);
                break;
        }
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
     * @return MaterialPaper|MaterialRoll|MaterialPrintingplate|MaterialTool|MaterialFinish|MaterialPacking
     */
    public function getMaterial()
    {
        return $this->material;
    }

    /**
     * @param MaterialPaper|MaterialRoll|MaterialPrintingplate|MaterialTool|MaterialFinish|MaterialPacking $material
     */
    public function setMaterial($material)
    {
        $this->material = $material;
    }

    /**
     * @return int
     */
    public function getMaterialtype()
    {
        return $this->materialtype;
    }

    /**
     * @param int $materialtype
     */
    public function setMaterialtype($materialtype)
    {
        $this->materialtype = $materialtype;
    }

    /**
     * @return int
     */
    public function getOperator()
    {
        return $this->operator;
    }

    /**
     * @param int $operator
     */
    public function setOperator($operator)
    {
        $this->operator = $operator;
    }

    /**
     * @return int
     */
    public function getModifier()
    {
        return $this->modifier;
    }

    /**
     * @param int $modifier
     */
    public function setModifier($modifier)
    {
        $this->modifier = $modifier;
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
}