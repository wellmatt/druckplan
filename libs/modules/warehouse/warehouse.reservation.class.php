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
        $this->$orderposition = new Orderposition();
        
        if ($this->crtdate == 0) {
            $this->crtdate = time();
        }
        
        if ($id > 0) {
            $sql = "SELECT * FROM reservations WHERE id = {$id}";
            $r = $DB->select($sql);
            if (is_array($r)) {
                $this->id = $r[0]["id"];
                $this->warehouse = new Warehouse($r[0]["whid"]);
                $this->orderposition = new Orderposition($r[0]["opid"]);
                $this->article = new Article($r[0]["articleid"]);
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
            $sql = "UPDATE reservations
            SET
            id 		    = {$this->id},
            whid		= {$this->warehouse->getId()},
            opid 	    = {$this->orderposition->getId()},
            articleid	= {$this->article->getId()},
            amount      = {$this->amount},
            status      = {$this->status}
            WHERE
            id = {$this->id};";
            $res = $DB->no_result($sql);
        } else {
            $sql = "INSERT INTO reservations
            (whid, orderid, articleid,
            amount, status)
            VALUES
                ({$this->name}, {$this->warehouse->getId()}, {$this->orderposition->getId()}, {$this->article->getId()}, {$this->amount}, {$this->status})";
            $res = $DB->no_result($sql);
            if ($res) {
                $sql = "SELECT MAX(id) id
                FROM reservations
                WHERE
                whid = {$this->warehouse->getId()} AND
                opid = {$this->orderposition->getId()} AND
                articleid = {$this->article->getId()};";
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
        $sql = "UPDATE reservations SET status = {$this->Reservation_Delete} WHERE id = {$this->id};";
        $res = $DB->no_result($sql);
        unset($this);
    }

    static function getAllReservationByArticle($articleid, $filter = "")
    {
        global $DB;
        
        $retval = Array();
        $del = self::Reservation_Delete;
        $sql = "SELECT id FROM reservations
        WHERE status > {$del}
        AND articleid = {$articleid} {$filter};";
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
        $sql = "SELECT id FROM reservations
        WHERE status > {$del}
        AND whid = {$whid} {$filter};";
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
        $sql = "SELECT SUM(amount) as sa FROM reservations
        WHERE status > {$del}
        AND whid = {$whid} {$filter};";
        // error_log($sql);
        $res = $DB->select($sql);
        return $res[0]["sa"];
    }

    static function getAllReservationByOrderposition($opid, $filter = "")
    {
        global $DB;
        
        $retval = Array();
        $del = self::Reservation_Delete;
        $sql = "SELECT id FROM reservations
        WHERE status > {$del}
        AND opid = {$opid} {$filter};";
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
        $sql = "SELECT id FROM reservations
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

    /**
     * @return the $id
     */
    public function getId()
    {
        return $this->id;
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
}
?>
