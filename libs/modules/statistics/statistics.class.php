<?php


class Statistics {
    /**
     * @param $timestamp
     * @param $businesscontact
     * @param $user
     * @param $article
     * @param $tradegroup
     * @param $status
     * @return CollectiveInvoice[]
     */

    public static function ColinvCountDay($timestamp, $businesscontact, $user, $article, $tradegroup, $status )
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
     * @param $start
     * @param $end
     * @param $mid
     * @param $mgroup
     * @param $mname
     * @param $mtactual
     * @param $mplanned
     * @param $ccount
     * @return array
     */
    public static function Maschstat($start, $end, $mid, $mgroup, $mname, $mtactual, $mplanned, $ccount )
    {
        global $DB;
        date_default_timezone_set('Europe/Berlin');
        $retval = [];

        $where = "";
        if ($mid != 0){
            $where .= " AND machines.group.id = {$mid} ";
        }
        if ($mgroup != 0){
            $where .= " AND grpname = {$mgroup} ";
        }
        if ($mname != 0){
            $where .= " AND machname = {$mname} ";
        }
        if ($mplanned != 0){
            $where .= " AND planned = {$mplanned} ";
        }
        if ($mtactual != 0){
            $where .= " AND actual = {$mtactual} ";
        }
        if ($ccount != 0){
            $where .= " AND colcount = {$ccount} ";
        }

        $sql = "SELECT machines.name as machname, machine_groups.name as grpname, SUM(planning_jobs.tplanned) as planned, SUM(planning_jobs.tactual) as actual, count(collectiveinvoice.id) as colcount
                FROM
                machines
                INNER JOIN machine_groups ON machines.group = machine_groups.id
                INNER JOIN orders_machines ON machines.id = orders_machines.machine_id
                INNER JOIN orders_calculations ON orders_machines.calc_id = orders_calculations.id
                INNER JOIN orders ON orders_calculations.order_id = orders.id
                INNER JOIN article ON orders.id = article.orderid
                INNER JOIN collectiveinvoice_orderposition ON article.id = collectiveinvoice_orderposition.object_id
                INNER JOIN collectiveinvoice ON collectiveinvoice_orderposition.collectiveinvoice = collectiveinvoice.id
                INNER JOIN planning_jobs ON collectiveinvoice_orderposition.id = planning_jobs.opos AND machines.id = planning_jobs.artmach
                WHERE
                collectiveinvoice_orderposition.type = 1
                AND
                article.orderid > 0
                AND
                {$where}
                orders_calculations.product_amount = collectiveinvoice_orderposition.quantity
                GROUP BY machines.id
                ";


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

    /**
     * @param $timestamp
     * @param $businesscontact
     * @param $user
     * @param $article
     * @param $tradegroup
     * @param $status
     * @return CollectiveInvoice[]
     */
    public static function ColinvCust($timestamp, $businesscontact, $user, $article, $tradegroup, $status )
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




}