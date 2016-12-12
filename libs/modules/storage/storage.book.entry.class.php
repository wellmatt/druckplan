<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */
require_once 'libs/basic/model.php';
require_once 'libs/modules/storage/storage.area.class.php';
require_once 'libs/modules/suporder/suporder.class.php';


class StorageBookEnrty extends Model
{
    public $_table = 'storage_book_entries';
    public $area = 0;
    public $article = 0;
    public $type = 0;
    public $origin = 0;
    public $origin_pos = 0;
    public $amount = 0;
    public $alloc = 0;
    public $crtdate = 0;
    public $crtuser = 0;


    protected function bootClasses()
    {
        switch ($this->type) {
            case StorageGoods::TYPE_SUPORDER:
                $this->origin = new SupOrder($this->origin);
                $this->origin_pos = new SupOrderPosition($this->origin_pos);
                break;
            case StorageGoods::TYPE_COLINV:
                $this->origin = new CollectiveInvoice($this->origin);
                $this->origin_pos = new Orderposition($this->origin_pos);
                break;
        }
        $this->area = new StorageArea($this->area);
        $this->article = new Article($this->article);
        $this->crtuser = new User($this->crtuser);
    }

    /**
     * override default save to save storage position changes
     */
    public function save()
    {
        global $_USER;
        $ret = parent::save();
        if ($ret){
            $apos = StoragePosition::getFirstForAreaAndArticle($this->area,$this->article);
            if ($apos->getId()>0){
                if ($this->type == StorageGoods::TYPE_SUPORDER){
                    $apos->setAmount($apos->getAmount()+$this->getAmount());
                } else if ($this->type == StorageGoods::TYPE_COLINV){
                    $apos->setAmount($apos->getAmount()-$this->getAmount());
                    self::checkRemainingAmount($apos);
                }
                $apos->setAllocation($this->alloc);
                $apos->save();
            } else {
                $create = [
                    'area'=>$this->area->getId(),
                    'article'=>$this->article->getId(),
                    'businesscontact'=>0,
                    'amount'=>$this->amount,
                    'min_amount'=>0,
                    'respuser'=>$_USER->getId(),
                    'description'=>'',
                    'note'=>'',
                    'dispatch'=>'',
                    'packaging'=>'',
                    'allocation'=>$this->alloc,
                ];
                $storageposition = new StoragePosition(0,$create);
                $storageposition->save();
            }
        }
    }

    /**
     * @param StoragePosition $apos
     */
    private function checkRemainingAmount($apos)
    {
        $body = "Sehr geehrter Kunde,<br>
<br>
der Bestand des folgenden Artikels hat die Mindesmenge erreicht:<br>
<br>
{$apos->getArticle()->getTitle()} (#{$apos->getArticle()->getNumber()})<br>
Mindestmenge: {$apos->getMinAmount()} / Verbleibend: {$apos->getAmount()}<br>
Lagerplatz: {$apos->getArea()->getName()} (#{$apos->getArea()->getNumber()})<br>
<br>
Um einen reibungslosen Verkauf zu gewährleisten, muss dieser Artikel nachbestellt werden.<br>
<br>
Vielen Dank.<br>
<br>
Mit freundlich Grüßen<br>
Ihr Kundenportal-Betreuer
";

        if ($apos->getAmount() < $apos->getMinAmount()){
            if ($apos->getRespuser()->getId()>0 && $apos->getRespuser()->getEmail() != ""){
                $message = new MailMessage(null,[$apos->getRespuser()->getEmail()],"Lager Warnung",$body);
                $message->send();
            }
            $respcps = $apos->getAllRespContactpersons();
            if (count($respcps)>0){
                foreach ($respcps as $respcp) {
                    if ($respcp->getId()>0 && $respcp->getEmail() != ""){
                        $message = new MailMessage(null,[$respcp->getEmail()],"Lager Warnung",$body);
                        $message->send();
                    }
                }
            }
        }
    }

    /**
     * @param StorageArea $storagearea
     * @return StorageBookEnrty[]
     */
    public static function getAllForArea(StorageArea $storagearea)
    {
        $retval = self::fetch([
            [
                'column'=>'area',
                'value'=>$storagearea->getId()
            ],
            [
                'orderby'=>'crtdate',
                'orderbydir'=>'desc'
            ]
        ]);
        return $retval;
    }

    /**
     * @param Orderposition|SupOrderPosition $origin_pos
     * @return int
     */
    public static function calcutateToBookAmount($origin_pos)
    {
        $retval = 0;

        if (is_a($origin_pos,'SupOrderPosition')){
            if ($origin_pos->getArticle()->getUsesstorage() == 0)
                return 0;
            $retval = $origin_pos->getAmount();
            $positions = self::getAllForPosition($origin_pos);
            if (count($positions)>0){
                foreach ($positions as $position) {
                    if ($position->getArticle()->getId() == $origin_pos->getArticle()->getId()){
                        $retval = $retval - $position->getAmount();
                    }
                }
            }
        } else if (is_a($origin_pos,'Orderposition')){
            if ($origin_pos->getType() == 1 || $origin_pos->getType() == 2){
                $article = new Article($origin_pos->getObjectid());
                if ($article->getUsesstorage() == 0)
                    return 0;
                $retval = $origin_pos->getAmount();
                $positions = self::getAllForPosition($origin_pos);
                if (count($positions)>0){
                    foreach ($positions as $position) {
                        if ($position->getArticle()->getId() == $origin_pos->getObjectid()){
                            $retval = $retval - $position->getAmount();
                        }
                    }
                }
            }
        }

        return $retval;
    }

    /**
     * @param Orderposition|SupOrderPosition $origin
     * @return StorageBookEnrty[]
     */
    public static function getAllForPosition($origin)
    {
        $retval = [];

        if (is_a($origin,'SupOrderPosition')){
            $retval = self::fetch([
                [
                    'column'=>'origin_pos',
                    'value'=>$origin->getId()
                ],
                [
                    'column'=>'type',
                    'value'=>StorageGoods::TYPE_SUPORDER
                ],
            ]);
            return $retval;
        } else if (is_a($origin,'Orderposition')){
            $retval = self::fetch([
                [
                    'column'=>'origin_pos',
                    'value'=>$origin->getId()
                ],
                [
                    'column'=>'type',
                    'value'=>StorageGoods::TYPE_COLINV
                ],
            ]);
            return $retval;
        }
        return [];
    }

    /**
     * @param SupOrder|CollectiveInvoice $origin
     * @return StorageBookEnrty[]
     */
    public static function getAllForOrigin($origin)
    {
        $retval = [];

        if (is_a($origin,'SupOrder')){
            $retval = self::fetch([
                [
                    'column'=>'origin',
                    'value'=>$origin->getId()
                ],
                [
                    'column'=>'type',
                    'value'=>StorageGoods::TYPE_SUPORDER
                ],
            ]);
            return $retval;
        } elseif (is_a($origin,'CollectiveInvoice')){
            $retval = self::fetch([
                [
                    'column'=>'origin',
                    'value'=>$origin->getId()
                ],
                [
                    'column'=>'type',
                    'value'=>StorageGoods::TYPE_COLINV
                ],
            ]);
            return $retval;
        }
        return $retval;
    }

    /**
     * @return StorageArea
     */
    public function getArea()
    {
        return $this->area;
    }

    /**
     * @param StorageArea $area
     */
    public function setArea($area)
    {
        $this->area = $area;
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
     * @return CollectiveInvoice|SupOrder
     */
    public function getOrigin()
    {
        return $this->origin;
    }

    /**
     * @param CollectiveInvoice|SupOrder $origin
     */
    public function setOrigin($origin)
    {
        $this->origin = $origin;
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

    /**
     * @return int
     */
    public function getCrtdate()
    {
        return $this->crtdate;
    }

    /**
     * @param int $crtdate
     */
    public function setCrtdate($crtdate)
    {
        $this->crtdate = $crtdate;
    }

    /**
     * @return User
     */
    public function getCrtuser()
    {
        return $this->crtuser;
    }

    /**
     * @param User $crtuser
     */
    public function setCrtuser($crtuser)
    {
        $this->crtuser = $crtuser;
    }

    /**
     * @return int
     */
    public function getAlloc()
    {
        return $this->alloc;
    }

    /**
     * @param int $alloc
     */
    public function setAlloc($alloc)
    {
        $this->alloc = $alloc;
    }

    /**
     * @return int
     */
    public function getOriginPos()
    {
        return $this->origin_pos;
    }

    /**
     * @param int $origin_pos
     */
    public function setOriginPos($origin_pos)
    {
        $this->origin_pos = $origin_pos;
    }
}