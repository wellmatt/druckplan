<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */
require_once 'libs/basic/model.php';
require_once 'custom.field.possible.value.class.php';
require_once 'custom.field.value.class.php';

class CustomField extends Model{
    public $_table = 'custom_fields';

    public $class;
    public $filter;
    public $name;
    public $type;
    public $_possiblevalues = [];

    public $_object = null;
    public $_value = null;

    const TYPE_INPUT = 1;
    const TYPE_SELECT = 2;
    const TYPE_CHECKBOX = 3;

    protected function bootClasses()
    {
        if ($this->id > 0){
            if ($this->type == self::TYPE_SELECT){
                $this->_possiblevalues = CustomFieldPossibleValue::fetch([['column'=>'field','value'=>$this->id]]);
            }
        }
    }

    /**
     * @param $object
     * @return bool|null
     */
    public function fetchValue($object)
    {
        if ($this->_value == null){
            if (is_a($object,$this->class)){
                $this->_object = $object;
                $this->_value = CustomFieldValue::fetchSingle([['column'=>'field','value'=>$this->id],['column'=>'object','value'=>$this->_object->getId()]]);
                return $this->_value->getValue();
            }
        } else {
            return $this->_value->getValue();
        }
        return false;
    }

    /**
     * process form data into custom fields
     * form data is 'custf' array in form fields
     * @param $class
     * @param $object
     * @param $data
     */
    public static function processField($class, $object, $data)
    {
        $fields = CustomField::fetch([
            [
                'column'=>'class',
                'value'=>$class
            ]
        ]);
        foreach ($fields as $field) {
            CustomFieldValue::clear($field,$object);

            switch ($field->type){
                case self::TYPE_INPUT:
                    if (array_key_exists($field->getId(),$data)){
                        $value = $data[$field->getId()];
                    } else {
                        $value = '';
                    }
                    break;
                case self::TYPE_SELECT:
                    if (array_key_exists($field->getId(),$data)){
                        $value = $data[$field->getId()];
                    } else {
                        $value = 0;
                    }
                    break;
                case self::TYPE_CHECKBOX:
                    if (array_key_exists($field->getId(),$data)){
                        $value = 1;
                    } else {
                        $value = 0;
                    }
                    break;
                default:
                    $value = '';
                    break;
            }

            $newvalue = new CustomFieldValue(0,[
                'field'=>$field->getId(),
                'value'=>$value,
                'object'=>$object->getId()
            ]);
            $newvalue->save();
        }
    }

    /**
     * @param $object
     * @return string
     */
    public function generateHTML($object)
    {
        switch ($this->type){
            case self::TYPE_INPUT:
                return $this->generateInput($object);
                break;
            case self::TYPE_SELECT:
                return $this->generateSelect($object);
                break;
            case self::TYPE_CHECKBOX:
                return $this->generateCheckbox($object);
                break;
        }
    }

    private function generateInput($object)
    {
        $html = '
        <div class="form-group">
          <label for="" class="col-sm-3 control-label">'.$this->name.'</label>
          <div class="col-sm-9">
              <input type="text" class="form-control" data-type="input" data-fieldid="'.$this->id.'" name="custf['.$this->id.']" value="'.$this->fetchValue($object).'">
          </div>
        </div>
        ';
        return $html;
    }

    private function generateSelect($object)
    {
        $html = '<div class="form-group"><label for="" class="col-sm-3 control-label">'.$this->name.'</label><div class="col-sm-9"><select data-type="select" data-fieldid="'.$this->id.'" name="custf['.$this->id.']" class="form-control">';
        foreach ($this->_possiblevalues as $item) {
            if ($item->getId() == $this->fetchValue($object))
                $html .= '<option selected value="' . $item->getId() . '">' . $item->getValue() . '</option>';
            else
                $html .= '<option value="' . $item->getId() . '">' . $item->getValue() . '</option>';
        }
        $html .= '</select></div></div>';
        return $html;
    }

    private function generateCheckbox($object)
    {
        $checked = '';
        if ($this->fetchValue($object) == 1){
            $checked = ' checked ';
        }
        $html = '
            <div class="form-group">
              <label for="" class="col-sm-3 control-label">'.$this->name.'</label>
              <div class="col-sm-9">
                  <div class="checkbox">
                      <label>
                          <input type="checkbox" data-type="checkbox" data-fieldid="'.$this->id.'" name="custf['.$this->id.']" value="1" '.$checked.'>
                      </label>
                  </div>
              </div>
            </div>
        ';
        return $html;
    }

    /**
     * @return mixed
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @param mixed $class
     */
    public function setClass($class)
    {
        $this->class = $class;
    }

    /**
     * @return mixed
     */
    public function getFilter()
    {
        return $this->filter;
    }

    /**
     * @param mixed $filter
     */
    public function setFilter($filter)
    {
        $this->filter = $filter;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return array
     */
    public function getPossiblevalues()
    {
        return $this->_possiblevalues;
    }

    /**
     * @param array $possiblevalues
     */
    public function setPossiblevalues($possiblevalues)
    {
        $this->_possiblevalues = $possiblevalues;
    }

    /**
     * @return null
     */
    public function getValue()
    {
        return $this->_value;
    }

    /**
     * @param null $value
     */
    public function setValue($value)
    {
        $this->_value = $value;
    }
}