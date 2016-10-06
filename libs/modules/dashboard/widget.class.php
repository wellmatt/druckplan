<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */

class Widget{

    public $result;
    public $type;

    const TYPE_COLINVPERMONTH = 1;
    const TYPE_TOP5BESTARTICLES = 2;
    const TYPE_PROFITPERMONTH = 3;
    const TYPE_SHOPORDERSPERMONTH = 4;

    /**
     * Widget constructor.
     * @param $type
     */
    public function __construct($type)
    {
        global $_CONFIG;
        $this->type = $type;

        $keyword = $_CONFIG->cookieSecret;
        $keyword .= '_widget_'.$type;

        if (Cachehandler::exists($keyword) && 1==2) {
            $this->result =  Cachehandler::fromCache($keyword);
        } else {
            switch($this->type){
                case self::TYPE_COLINVPERMONTH:
                    self::genColinvPerMonth();
                    break;
                case self::TYPE_TOP5BESTARTICLES:
                    self::genTop5BestArticles();
                    break;
                case self::TYPE_SHOPORDERSPERMONTH:
                    self::genShopOrdersPerMonth();
                    break;
                case self::TYPE_PROFITPERMONTH:
                    self::genProfitPerMonth();
                    break;
                default:
                    return false;
            }
        }
        Cachehandler::toCache($keyword,$this->result,3600);
        return true;
    }

    /**
     * @return mixed
     */
    public function getResult(){
        return $this->result;
    }


    private function genColinvPerMonth()
    {
        global $DB;

        $dates = [];
        $result = [];

        for ($i=0;$i<6;$i++){
            $month = new DateTime();
            $month->sub(date_interval_create_from_date_string($i.' months'));
            $start = mktime(0,0,0,$month->format('m'),1,$month->format('Y'));
            $month->modify('last day of this month');
            $end = mktime(0,0,0,$month->format('m'),$month->format('d'),$month->format('Y'));
            $dates[] = [$start,$end];
        }

        foreach ($dates as $date) {
            $sql = "SELECT id, crtdate FROM collectiveinvoice WHERE crtdate > {$date[0]} AND crtdate < {$date[1]}";
            $result[] = [date('M',$date[0]),$DB->num_rows($sql)];
        }

        $this->result = array_reverse($result);
    }

    private function genProfitPerMonth()
    {
        global $DB;

        $dates = [];
        $result = [];

        for ($i=0;$i<6;$i++){
            $month = new DateTime();
            $month->sub(date_interval_create_from_date_string($i.' months'));
            $start = mktime(0,0,0,$month->format('m'),1,$month->format('Y'));
            $month->modify('last day of this month');
            $end = mktime(0,0,0,$month->format('m'),$month->format('d'),$month->format('Y'));
            $dates[] = [$start,$end];
        }

        foreach ($dates as $date) {
            $sql = "SELECT
                    SUM(collectiveinvoice_orderposition.profit) as sum
                    FROM
                    collectiveinvoice
                    INNER JOIN collectiveinvoice_orderposition ON collectiveinvoice.id = collectiveinvoice_orderposition.collectiveinvoice
                    WHERE collectiveinvoice.crtdate > {$date[0]} AND collectiveinvoice.crtdate < {$date[1]}
                    ";
            $data = $DB->select($sql);
            $result[] = [date('M',$date[0]),$data[0]['sum'] ? $data[0]['sum'] : 0];
        }

        $this->result = array_reverse($result);
    }

    private function genTop5BestArticles()
    {
        global $DB;

        $sql = "SELECT
                collectiveinvoice_orderposition.object_id AS articleid,
                Sum(collectiveinvoice_orderposition.price) AS sum,
                article.title as arttitle
                FROM
                collectiveinvoice_orderposition
                INNER JOIN article ON collectiveinvoice_orderposition.object_id = article.id
                WHERE
                collectiveinvoice_orderposition.`status` = 1 AND
                (collectiveinvoice_orderposition.type = 1 OR
                collectiveinvoice_orderposition.type = 2)
                GROUP BY
                collectiveinvoice_orderposition.object_id
                ORDER BY sum(price) DESC
                LIMIT 5";

        $data = $DB->select($sql);
        $total = 0;
        $result = [];

        foreach ($data as $item) {
            $total += $item['sum'];
        }

        foreach ($data as $item) {
            $result[] = ['label'=>$item['arttitle'],'data'=>($total/100*$item['sum'])];
        }

        $this->result = $result;
    }


    private function genShopOrdersPerMonth()
    {
        global $DB;

        $dates = [];
        $result = [];

        for ($i=0;$i<6;$i++){
            $month = new DateTime();
            $month->sub(date_interval_create_from_date_string($i.' months'));
            $start = mktime(0,0,0,$month->format('m'),1,$month->format('Y'));
            $month->modify('last day of this month');
            $end = mktime(0,0,0,$month->format('m'),$month->format('d'),$month->format('Y'));
            $dates[] = [$start,$end];
        }

        foreach ($dates as $date) {
            $sql = "SELECT SUM(positioncount) as sum FROM (
                    SELECT
                    collectiveinvoice.id,
                    COUNT(collectiveinvoice_orderposition.id) as positioncount
                    FROM
                    collectiveinvoice
                    INNER JOIN collectiveinvoice_orderposition ON collectiveinvoice.id = collectiveinvoice_orderposition.collectiveinvoice
                    WHERE collectiveinvoice.crtdate > {$date[0]} AND collectiveinvoice.crtdate < {$date[1]} AND collectiveinvoice.type = 2
                    GROUP BY collectiveinvoice.id) t1
                    ";
            $data = $DB->select($sql);
            $result[] = [date('M',$date[0]),$data[0]['sum'] ? $data[0]['sum'] : 0];
        }

        $this->result = array_reverse($result);
    }
}