<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */
require_once 'libs/basic/model.php';

class CustomFieldPossibleValue extends Model{
    public $_table = 'custom_fields_possiblevalues';

    public $field;
    public $value;

    /**
     * @param $field
     * @param $value
     * @return bool
     */
    public static function exists($field, $value)
    {
        $possvalue = self::fetchSingle([
            [
                'column'=>'field',
                'value'=>$field
            ],
            [
                'column'=>'value',
                'value'=>$value
            ]
        ]);
        if ($possvalue->getId() > 0)
            return true;
        else
            return false;
    }

    /**
     * @return mixed
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @param mixed $field
     */
    public function setField($field)
    {
        $this->field = $field;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }
}