<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */
require_once 'libs/basic/model.php';
require_once 'libs/modules/storage/storage.position.class.php';

class StorageArea extends Model {
    public $_table = 'storage_areas';
    public $name = '';
    public $description = '';
    public $location = '';
    public $corridor = '';
    public $shelf = '';
    public $line = '';
    public $layer = '';
    public $prio = 1;

    const PRIO_LOW = 0;
    const PRIO_MED = 1;
    const PRIO_HIG = 2;


    /**
     * Override default delete to also delete all associated StoragePositions
     */
    public function delete()
    {
        $positions = StoragePosition::getAllForArea($this);
        foreach ($positions as $position) {
            $position->delete();
        }
        parent::delete();
    }

    public static function getAll(){
        $retval = self::fetch();
        return $retval;
    }

    /**
     * Liefert nur Lagerplaetze in dem sich angegebener Artikel befindet
     * @param Article $article
     * @return array
     */
    public static function getStoragesPrioArticle(Article $article){
        global $DB;
        $exept = [];
        $prio = [];
        $other = [];

        $sql = "SELECT DISTINCT
                storage_areas.id,
                COALESCE(SUM(storage_positions.allocation),0) as alloc,
                storage_positions.id as posid
                FROM
                storage_areas
                LEFT JOIN storage_positions ON storage_areas.id = storage_positions.area
                WHERE
                storage_positions.article = {$article->getId()}
                HAVING SUM(storage_positions.allocation) < 100";

        if($DB->no_result($sql)){
            $result = $DB->select($sql);
            foreach($result as $r){
                $exept[] = $r["id"];
                $prio[] = ['id'=>$r['id'],'alloc'=>$r['alloc'],'posid'=>$r['posid']];
            }
        }

        if (count($exept)>0)
            $exept = ' WHERE storage_areas.id NOT IN ('.implode(',',$exept).')';
        else
            $exept = '';

        $sql2 =    "SELECT storage_areas.id,
                    COALESCE(SUM(storage_positions.allocation),0) as alloc
                    FROM
                    storage_areas
                    LEFT OUTER JOIN storage_positions ON storage_areas.id = storage_positions.area
                    {$exept}
                    GROUP BY storage_areas.id";

        if($DB->no_result($sql2)){
            $result = $DB->select($sql2);
            foreach($result as $r){
                $other[] = ['id'=>$r['id'],'alloc'=>$r['alloc']];
            }
        }

        $retval = ['prio'=>$prio,'other'=>$other];
        return $retval;
    }

    /**
     * Liefert nur Lagerplaetze in dem sich angegebener Artikel befindet
     * @param Article $article
     * @return StorageArea[]
     */
    public static function getStoragesForArticle(Article $article){
        global $DB;
        $retval = [];

        $sql = "SELECT DISTINCT
                storage_areas.id
                FROM
                storage_areas
                INNER JOIN storage_positions ON storage_areas.id = storage_positions.area
                WHERE
                storage_positions.article = {$article->getId()}
                ORDER BY storage_areas.prio DESC";

        if($DB->no_result($sql)){
            $result = $DB->select($sql);
            foreach($result as $r){
                $retval[] = new StorageArea($r["id"]);
            }
        }
        return $retval;
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
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param string $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

    /**
     * @return string
     */
    public function getCorridor()
    {
        return $this->corridor;
    }

    /**
     * @param string $corridor
     */
    public function setCorridor($corridor)
    {
        $this->corridor = $corridor;
    }

    /**
     * @return string
     */
    public function getShelf()
    {
        return $this->shelf;
    }

    /**
     * @param string $shelf
     */
    public function setShelf($shelf)
    {
        $this->shelf = $shelf;
    }

    /**
     * @return string
     */
    public function getLine()
    {
        return $this->line;
    }

    /**
     * @param string $line
     */
    public function setLine($line)
    {
        $this->line = $line;
    }

    /**
     * @return string
     */
    public function getLayer()
    {
        return $this->layer;
    }

    /**
     * @param string $layer
     */
    public function setLayer($layer)
    {
        $this->layer = $layer;
    }

    /**
     * @return int
     */
    public function getPrio()
    {
        return $this->prio;
    }

    /**
     * @param int $prio
     */
    public function setPrio($prio)
    {
        $this->prio = $prio;
    }
}