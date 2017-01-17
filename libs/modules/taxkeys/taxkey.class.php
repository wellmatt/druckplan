<?php
/**
 *  Copyright (c) 2017 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2017
 *
 */

require_once 'libs/basic/model.php';

class TaxKey extends Model{
    public $_table = 'taxkeys';
    public $value = '';
    public $key = '';
    public $default = 0;

    /**
     * @return TaxKey[]
     */
    public static function getAll()
    {
        return self::fetch();
    }

    public function star()
    {
        $all = self::getAll();
        foreach ($all as $item) {
            if ($item->getDefault() == 1) {
                $item->setDefault(0);
                $item->save();
            }
        }
        $this->default = 1;
        $this->save();
    }

    /**
     * @return TaxKey
     */
    public static function getDefaultTaxKey()
    {
        $ret = self::fetchSingle([
            [
                "column" => "default",
                "value" => 1
            ]
        ]);
        return $ret;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
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
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param string $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }
}