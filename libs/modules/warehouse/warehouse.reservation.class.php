<?php
// ----------------------------------------------------------------------------------
// Author: Klein Druck+Medien GmbH
// Updated: 23.12.2014
// Copyright: Klein Druck+Medien GmbH - All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------
require_once 'libs/modules/collectiveinvoice/orderposition.class.php';
require_once 'libs/modules/article/article.class.php';
require_once 'libs/modules/warehouse/warehouse.class.php';

class Reservation
{
    const Reservation_Delete = 0;
    const Reservation_Active = 1;
    private $id = 0;
    private $gid = 0;
    private $warehouse; // Name des Lagerplatzesid
    private $orderposition;
    private $article;
    private $amount;
    private $status = self::Reservation_Active;

    function __construct($id = 0)
    {
        global $DB;
        global $_USER;
        
        $this->whid = new Warehouse();
        $this->article = new Article();
        $this->orderposition = new Orderposition();
        
        if ($id > 0) {
            $sql = "SELECT * FROM warehouse_reservations WHERE id = {$id}";
            $r = $DB->select($sql);
            if (is_array($r)) {
                $this->id = $r[0]["id"];
                $this->gid = $r[0]["gid"];
                $this->warehouse = new Warehouse($r[0]["wh_id"]);
                $this->orderposition = new Orderposition($r[0]["op_id"]);
                $this->article = new Article($r[0]["article_id"]);
                $this->amount = $r[0]["amount"];
                $this->status = $r[0]["status"];
            }
        }
    }

    /**
     * Speicherfunktion fuer Reservierung
     *
     * @return boolean
     */
    function save()
    {
        global $DB;
        global $_USER;
        
        if ($this->id > 0) {
            $sql = "UPDATE warehouse_reservations
            SET
            id 		    = {$this->id},
            gid 		= {$this->gid},
            wh_id		= {$this->warehouse->getId()},
            op_id 	    = {$this->orderposition->getId()},
            article_id	= {$this->article->getId()},
            amount      = {$this->amount},
            status      = {$this->status}
            WHERE
            id = {$this->id};";
            $res = $DB->no_result($sql);
        } else {
            $sql = "INSERT INTO warehouse_reservations
            (gid,
             wh_id,
             op_id,
             article_id,
             amount,
             status)
            VALUES
            ({$this->gid},
             {$this->warehouse->getId()},
             {$this->orderposition->getId()}, 
             {$this->article->getId()}, 
             {$this->amount}, 
             {$this->status})";
            $res = $DB->no_result($sql);
            if ($res) {
                $sql = "SELECT MAX(id) id
                FROM warehouse_reservations
                WHERE
                wh_id = {$this->warehouse->getId()} AND
                op_id = {$this->orderposition->getId()} AND
                article_id = {$this->article->getId()};";
                $thisid = $DB->select($sql);
                $this->id = $thisid[0]["id"];
            }
        }
        return $res;
    }

    /**
     * Funktion zum Entfernen einer Reservierung
     *
     * @return boolean
     */
    function delete()
    {
        global $DB;
        global $_USER;
        $this->status = self::Reservation_Delete;
        $sql = "UPDATE warehouse_reservations SET status = {$this->status} WHERE id = {$this->id};";
        $res = $DB->no_result($sql);
        unset($this);
    }

    static function getAllReservationByArticle($articleid, $filter = "")
    {
        global $DB;
        
        $retval = Array();
        $del = self::Reservation_Delete;
        $sql = "SELECT id FROM warehouse_reservations
        WHERE status > {$del}
        AND article_id = {$articleid} {$filter};";
        // error_log($sql);
        $res = $DB->select($sql);
        if (is_array($res)) {
            foreach ($res as $r) {
                $retval[] = new Reservation($r["id"]);
            }
        }
        return $retval;
    }

	static function getAllReservationByWarehouse($whid, $filter = "")
    {
        global $DB;
        
        $retval = Array();
        $del = self::Reservation_Delete;
        $sql = "SELECT id FROM warehouse_reservations
        WHERE status > {$del}
        AND wh_id = {$whid} {$filter};";
        // error_log($sql);
        $res = $DB->select($sql);
        if (is_array($res)) {
            foreach ($res as $r) {
                $retval[] = new Reservation($r["id"]);
            }
        }
        return $retval;
    }
    
    static function getTotalReservationByWarehouse($whid, $filter = "")
    {
        global $DB;
    
        $retval = Array();
        $del = self::Reservation_Delete;
        $sql = "SELECT Sum(amount) AS sa FROM warehouse_reservations
        WHERE status > {$del}
        AND wh_id = {$whid} {$filter};";
        // error_log($sql);
        $res = $DB->select($sql);
        return $res[0]["sa"];
    }

    static function getAllReservationByOrderposition($opid, $filter = "")
    {
        global $DB;
        
        $retval = Array();
        $del = self::Reservation_Delete;
        $sql = "SELECT id FROM warehouse_reservations
        WHERE status > {$del}
        AND op_id = {$opid} {$filter};";
        // error_log($sql);
        $res = $DB->select($sql);
        if (is_array($res)) {
            foreach ($res as $r) {
                $retval[] = new Reservation($r["id"]);
            }
        }
        return $retval;
    }

    static function getAllReservation($filter = "")
    {
        global $DB;
        
        $retval = Array();
        $del = self::Reservation_Delete;
        $sql = "SELECT id FROM warehouse_reservations
        WHERE status > {$del} {$filter};";
        // error_log($sql);
        $res = $DB->select($sql);
        if (is_array($res)) {
            foreach ($res as $r) {
                $retval[] = new Reservation($r["id"]);
            }
        }
        return $retval;
    }
    
    static function getMaxReservationGroupId($filter = "")
    {
        global $DB;
    
        $retval = Array();
        $del = self::Reservation_Delete;
        $sql = "SELECT Max(gid) AS mgid FROM warehouse_reservations {$filter};";
        // error_log($sql);
        $res = $DB->select($sql);
        return $res[0]["mgid"];
    }
    
    static function getAllReservationByFilter($filter = "")
    {
        global $DB;
    
        $retval = Array();
        $del = self::Reservation_Delete;
        $sql = "SELECT id FROM warehouse_reservations {$filter};";
//         error_log($sql);
        $res = $DB->select($sql);
        if (is_array($res)) {
            foreach ($res as $r) {
                $retval[] = new Reservation($r["id"]);
            }
        }
        return $retval;
    }

    /**
     * @return the $id
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * @return the $gid
     */
    public function getGid()
    {
        return $this->gid;
    }
    
    /**
     * @return the $warehouse
     */
    public function getWarehouse()
    {
        return $this->warehouse;
    }
    
    /**
     * @return the $orderposition
     */
    public function getOrderposition()
    {
        return $this->orderposition;
    }
    
    /**
     * @return the $article
     */
    public function getArticle()
    {
        return $this->article;
    }
    
    /**
     * @return the $amount
     */
    public function getAmount()
    {
        return $this->amount;
    }
    
    /**
     * @return the $status
     */
    public function getStatus()
    {
        return $this->status;
    }
    
    /**
     * @param number $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }
    
    /**
     * @param Warehouse $warehouse
     */
    public function setWarehouse($warehouse)
    {
        $this->warehouse = $warehouse;
    }
    
    /**
     * @param Orderposition $orderposition
     */
    public function setOrderposition($orderposition)
    {
        $this->orderposition = $orderposition;
    }
    
    /**
     * @param Article $article
     */
    public function setArticle($article)
    {
        $this->article = $article;
    }
    
    /**
     * @param field_type $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }
    
    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

	/**
     * @param field_type $gid
     */
    public function setGid($gid)
    {
        $this->gid = $gid;
    }

    
}
?>
