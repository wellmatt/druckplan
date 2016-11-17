<?php

class Statistics {

    /**
     * @param $timestamp
     * @param $businesscontact
     * @param $user
     * @param $article
     * @param $tradegroup
     * @param $number
     * @param $status
     * @return CollectiveInvoice[]
     */
    public static function ColinvCountDay($timestamp, $businesscontact, $user, $number, $article, $tradegroup, $status )
    {
        global $DB;
        date_default_timezone_set('Europe/Berlin');
        $retval = [];

        $where = "";
        if ($businesscontact != 0 && $businesscontact != NULL){
            $where .= " AND businesscontact.id = {$businesscontact} ";
        }
        if ($user != 0 && $user != NULL){
            $where .= " AND `user`.id = {$user} ";
        }
        if ($number != 0 && $number != NULL){
            $where .= " AND `collectiveinvoice.number` = {$number} ";
        }
        if ($article != 0 && $article != NULL){
            $where .= " AND article.id = {$article} ";
        }
        if ($tradegroup != 0 && $tradegroup != NULL){
            $where .= " AND tradegroup.id = {$tradegroup} ";
        }
        if ($status != 0 && $status != NULL){
            $where .= " AND collectiveinvoice.`status` = {$status} ";
        }

        $start = mktime(0, 0, 0, date('m', $timestamp), date('d', $timestamp), date('Y', $timestamp));
        $end = mktime(23, 59, 59, date('m', $timestamp), date('d', $timestamp), date('Y', $timestamp));

        $sql = "SELECT DISTINCT
                collectiveinvoice.id
                FROM
                collectiveinvoice
                LEFT JOIN businesscontact ON collectiveinvoice.businesscontact = businesscontact.id
                LEFT JOIN collectiveinvoice_orderposition ON collectiveinvoice.id = collectiveinvoice_orderposition.collectiveinvoice
                LEFT JOIN `user` ON collectiveinvoice.crtuser = `user`.id
                LEFT JOIN article ON collectiveinvoice_orderposition.object_id = article.id
                LEFT JOIN tradegroup ON article.tradegroup = tradegroup.id
                WHERE collectiveinvoice_orderposition.type IN (2,1)
                AND collectiveinvoice_orderposition.`status` = 1
                AND collectiveinvoice.`status` > 0
                AND collectiveinvoice.crtdate >= {$start}
                AND collectiveinvoice.crtdate <= {$end}
                {$where}
                ";

        if ($DB->no_result($sql)) {
            foreach($DB->select($sql) as $r){
                $retval[] = new CollectiveInvoice($r["id"]);
            }
        }
        return $retval;
    }


    public static function ColinvCountMonth($timestamp, $businesscontact, $user, $article, $tradegroup, $status )
    {
        global $DB;
        date_default_timezone_set('Europe/Berlin');
        $retval = [];

        $where = "";
        if ($businesscontact != 0){
            $where .= " AND businesscontact.id = {$businesscontact} ";
        }
        if ($user != 0){
            $where .= " AND `user`.id = {$user} ";
        }
        if ($article != 0){
            $where .= " AND article.id = {$article} ";
        }
        if ($tradegroup != 0){
            $where .= " AND tradegroup.id = {$tradegroup} ";
        }
        if ($status != 0){
            $where .= " AND collectiveinvoice.`status` = {$status} ";
        }

        $start = mktime(0, 0, 0, date('m', $timestamp), 1, date('Y', $timestamp));
        $end = mktime(23, 59, 59, date('m', $timestamp),cal_days_in_month(CAL_GREGORIAN,date('m', $timestamp), date('Y', $timestamp)), date('Y', $timestamp));


        $sql = "SELECT DISTINCT
                collectiveinvoice.id
                FROM
                collectiveinvoice
                LEFT JOIN businesscontact ON collectiveinvoice.businesscontact = businesscontact.id
                LEFT JOIN collectiveinvoice_orderposition ON collectiveinvoice.id = collectiveinvoice_orderposition.collectiveinvoice
                LEFT JOIN `user` ON collectiveinvoice.crtuser = `user`.id
                LEFT JOIN article ON collectiveinvoice_orderposition.object_id = article.id
                LEFT JOIN tradegroup ON article.tradegroup = tradegroup.id
                WHERE collectiveinvoice_orderposition.type IN (2,1)
                AND collectiveinvoice_orderposition.`status` = 1
                AND collectiveinvoice.`status` > 0
                AND collectiveinvoice.crtdate >= {$start}
                AND collectiveinvoice.crtdate <= {$end}
                {$where}
                ";



        if ($DB->no_result($sql)) {
            foreach($DB->select($sql) as $r){
                $retval[] = new CollectiveInvoice($r["id"]);
            }
        }
        return $retval;
    }
    public static function ColinvCountYear($timestamp, $businesscontact, $user, $article, $tradegroup, $status)
    {
        global $DB;
        date_default_timezone_set('Europe/Berlin');
        $retval = [];

        $where = "";
        if ($businesscontact != 0){
            $where .= " AND businesscontact.id = {$businesscontact} ";
        }
        if ($user != 0){
            $where .= " AND `user`.id = {$user} ";
        }
        if ($article != 0){
            $where .= " AND article.id = {$article} ";
        }
        if ($tradegroup != 0){
            $where .= " AND tradegroup.id = {$tradegroup} ";
        }
        if ($status != 0){
            $where .= " AND collectiveinvoice.`status` = {$status} ";
        }

        $start = mktime(0, 0, 0, 1, 1, date('Y', $timestamp));
        $end = mktime(23, 59, 59, 12,cal_days_in_month(CAL_GREGORIAN,12, date('Y', $timestamp)), date('Y', $timestamp));


        $sql = "SELECT DISTINCT
                collectiveinvoice.id
                FROM
                collectiveinvoice
                LEFT JOIN businesscontact ON collectiveinvoice.businesscontact = businesscontact.id
                LEFT JOIN collectiveinvoice_orderposition ON collectiveinvoice.id = collectiveinvoice_orderposition.collectiveinvoice
                LEFT JOIN `user` ON collectiveinvoice.crtuser = `user`.id
                LEFT JOIN article ON collectiveinvoice_orderposition.object_id = article.id
                LEFT JOIN tradegroup ON article.tradegroup = tradegroup.id
                WHERE collectiveinvoice_orderposition.type IN (2,1)
                AND collectiveinvoice_orderposition.`status` = 1
                AND collectiveinvoice.`status` > 0
                AND collectiveinvoice.crtdate >= {$start}
                AND collectiveinvoice.crtdate <= {$end}
                {$where}
                ";



        if ($DB->no_result($sql)) {
            foreach($DB->select($sql) as $r){
                $retval[] = new CollectiveInvoice($r["id"]);
            }
        }
        return $retval;
    }





    public static function ColinvInProgress()
    {

    }

    public static function ColinvStats()
    {

    }

    /**
     * @param int $start
     * @param int $end
     * @param int $mgroup
     * @return array
     */
    public static function Maschstat($start, $end, $mgroup)
    {
        global $DB;
        date_default_timezone_set('Europe/Berlin');
        $retval = [];

        $where = "";
        if ($mgroup != 0){
            $where .= " AND machines.group = {$mgroup} ";
        }

        $sql = "SELECT
                machines.id,
                machines.`name`,
                SUM(planning_jobs.tplanned) as zeitsoll,
                SUM(planning_jobs.tactual) as zeitist,
                SUM(collectiveinvoice_orderposition.price) as auftragswert,
                count(planning_jobs.id) as anzahlauftraege
                FROM
                planning_jobs
                INNER JOIN collectiveinvoice_orderposition ON planning_jobs.opos = collectiveinvoice_orderposition.id
                INNER JOIN machines ON planning_jobs.artmach = machines.id
                WHERE
                planning_jobs.type = 2 AND
                planning_jobs.state = 1 AND
                planning_jobs.start <= {$end} AND
                planning_jobs.start >= {$start}
                {$where}
                GROUP BY machines.id ";
//        prettyPrint($sql);

        if ($DB->no_result($sql)) {
            foreach($DB->select($sql) as $r){
                $retval[] = $r;
            }
        }
        return $retval;
}


    /**
     * @param $start
     * @param $end
     * @param $user
     * @param $businesscontact
     * @param $tradegroup
     * @param $article
     * @param $status
     * @return array
     */
    public static function Calcstat( $start, $end, $businesscontact, $user, $article, $tradegroup, $status )
    {

        global $DB;
        date_default_timezone_set('Europe/Berlin');
        $retval = [];

        $where = "";
        if ($businesscontact != 0){
            $where .= " AND businesscontact.id = {$businesscontact} ";
        }
        if ($user != 0){
            $where .= " AND `user`.id = {$user} ";
        }
        if ($tradegroup != 0){
            $where .= " AND tradegroup.id = {$tradegroup} ";
        }

        if ($article != 0){
            $where .= " AND article.id = {$article} ";
        }
        if ($status != 0){
            $where .= " AND collectiveinvoice.`status` = {$status} ";
        }



       $sql ="SELECT
        businesscontact.id,
        count(collectiveinvoice.id) as anzauft,
        SUM(collectiveinvoice_orderposition.price) as wert,
        SUM(collectiveinvoice_orderposition.price/100*collectiveinvoice_orderposition.tax) as steuer
        FROM
        businesscontact
        INNER JOIN collectiveinvoice ON collectiveinvoice.businesscontact = businesscontact.id
        INNER JOIN collectiveinvoice_orderposition ON collectiveinvoice.id= collectiveinvoice_orderposition.collectiveinvoice
        INNER JOIN article ON collectiveinvoice_orderposition.object_id = article.id
        INNER JOIN tradegroup ON article.tradegroup = tradegroup.id
        WHERE collectiveinvoice_orderposition.type IN (2,1)
                AND collectiveinvoice_orderposition.`status` = 1
                AND collectiveinvoice.`status` > 0
                AND collectiveinvoice.crtdate >= {$start}
        AND collectiveinvoice.crtdate <= {$end}
        {$where}
        GROUP BY businesscontact.id";

//      prettyPrint($sql);


        if ($DB->no_result($sql)) {
            foreach($DB->select($sql) as $r){
                $retval[] = $r;
            }
        }
        return $retval;
    }






}