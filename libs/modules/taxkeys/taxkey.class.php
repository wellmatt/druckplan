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
    public $type = 1;

    const TYPE_DEFAUTL = 1;
    const TYPE_NOVAT = 2; // UST Frei 0%
    const TYPE_INTRACOM = 3; // Innergemeinschaftlich 0%
    const TYPE_REVERSECHARGE = 4; // Reverse Charge 0%
    const TYPE_EXPORT = 5; // Ausfuhr 0%

    const ARTICLE_TYPE_SERVICE = 1;
    const ARTICLE_TYPE_DELIVERY = 2;

    const CUSTOMER_PRIVATE = 1;
    const CUSTOMER_BUSINESS = 2;

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
                "column" => "`default`",
                "value" => 1
            ]
        ]);
        return $ret;
    }

    /**
     * @return string
     */
    public function getTypeText()
    {
        switch ($this->type){
            case self::TYPE_DEFAUTL:
                return 'Standard';
            case self::TYPE_NOVAT:
                return 'USt Frei';
            case self::TYPE_INTRACOM:
                return 'Innergemeinschaftlich';
            case self::TYPE_REVERSECHARGE:
                return 'Reverse Charge';
            case self::TYPE_EXPORT:
                return 'Ausfuhr';
            default:
                return '';
        }
    }

    /**
     * @param $type int
     * @return TaxKey
     */
    public static function fetchFirstByType($type)
    {
        $ret = self::fetchSingle([
            [
                "column" => "type",
                "value" => $type
            ]
        ]);
        return $ret;
    }

    /**
     * @param $collectinv CollectiveInvoice
     * @param $article Article
     * @return TaxKey
     *
    Kunde Privat Inland Lieferartikel = UST DE
    Kunde Privat Inland Dienstleistung = UST DE
    Kunde Privat EU Lieferartikel = UST Empfänger Land
    Kunde Privat EU Dienstleistung = UST DE
    Kunde Privat Außland Drittland Lieferartikel = 0% (außer Abholung)
    Kunde Privat Außland Drittland Dienstleistung= UST DE
     *
    Kunde Gewerblich Inland Lieferartikel = UST DE
    Kunde Gewerblich Inland Dienstleistung = UST DE
    Kunde Gewerblich EU Lieferartikel = 0% Innergemeinschaftlich
    Kunde Gewerblich EU Dienstleistung = 0% (Reverse Charge)
    Kunde Gewerblich Außland Drittland Lieferartikel = 0% (Ausfuhr)
    Kunde Gewerblich Außland Drittland Dienstleistung = 0% (Reverse Charge)
     */
    public static function evaluateTax(CollectiveInvoice $collectinv, Article $article)
    {
        $customer = $collectinv->getCustomer();
        $targetcounty = $customer->getCountry();
        $targeteu = $customer->getCountry()->getEu();

        if ($article->getIsWorkHourArt())
            $articletype = self::ARTICLE_TYPE_SERVICE;
        else
            $articletype = self::ARTICLE_TYPE_DELIVERY;

        if ($customer->getIsprivate())
            $customertype = self::CUSTOMER_PRIVATE;
        else
            $customertype = self::CUSTOMER_BUSINESS;

        if ($customertype == self::CUSTOMER_PRIVATE){ // PRIVAT
            if ($targetcounty->getId() == $collectinv->getCrtuser()->getClient()->getCountry()->getId()){
                // Kunde Privat Inland Lieferartikel = UST DE
                // Kunde Privat Inland Dienstleistung = UST DE
                return $article->getTaxkey();
            } else if ($targeteu){
                if ($articletype == self::ARTICLE_TYPE_DELIVERY){ // Kunde Privat EU Lieferartikel = UST Empfänger Land
                    if ($targetcounty->getTaxkey()->getId() > 0){
                        return $targetcounty->getTaxkey();
                    } else {
                        return TaxKey::getDefaultTaxKey(); // FALLBACK falls Zielland keine USt gesetzt hat
                    }
                } else { // Kunde Privat EU Dienstleistung = 19%
                    return $collectinv->getCrtuser()->getClient()->getCountry()->getTaxkey();
                }
            } else {
                if ($articletype == self::ARTICLE_TYPE_DELIVERY){ // Kunde Privat Außland Drittland Lieferartikel = 0% (außer Abholung)
                    return self::fetchFirstByType(self::TYPE_NOVAT);
                } else { // Kunde Privat Außland Drittland Dienstleistung= UST DE
                    return $collectinv->getCrtuser()->getClient()->getCountry()->getTaxkey();
                }
            }
        } else { // GESCHÄFTLICH
            if ($targetcounty->getId() == $collectinv->getCrtuser()->getClient()->getCountry()->getId()){
                if ($articletype == self::ARTICLE_TYPE_DELIVERY){ // Kunde Gewerblich Inland Lieferartikel = UST DE
                    return $article->getTaxkey();
                } else { // Kunde Gewerblich Inland Dienstleistung = UST DE
                    return $article->getTaxkey();
                }
            } else if ($targeteu){
                if ($articletype == self::ARTICLE_TYPE_DELIVERY){ // Kunde Gewerblich EU Lieferartikel = 0% Innergemeinschaftlich
                    return self::fetchFirstByType(self::TYPE_NOVAT);
                } else { // Kunde Gewerblich EU Dienstleistung = 0% (Reverse Charge)
                    return self::fetchFirstByType(self::TYPE_REVERSECHARGE);
                }
            } else {
                if ($articletype == self::ARTICLE_TYPE_DELIVERY){ // Kunde Gewerblich Außland Drittland Lieferartikel = 0% (Ausfuhr)
                    return self::fetchFirstByType(self::TYPE_EXPORT);
                } else { // Kunde Gewerblich Außland Drittland Dienstleistung = 0% (Reverse Charge)
                    return self::fetchFirstByType(self::TYPE_REVERSECHARGE);
                }
            }
        }
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
}