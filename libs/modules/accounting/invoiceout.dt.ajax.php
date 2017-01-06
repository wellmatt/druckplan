<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Christian Schroeer <cschroeer@ipactor.de>, 2016
 *
 */
    chdir('../../../');
    require_once("config.php");
    require_once("libs/basic/mysql.php");
    require_once("libs/basic/globalFunctions.php");
    require_once("libs/basic/user/user.class.php");
    require_once("libs/basic/groups/group.class.php");
    require_once("libs/basic/clients/client.class.php");
    require_once("libs/basic/translator/translator.class.php");
    require_once 'libs/basic/countries/country.class.php';
    require_once 'libs/basic/cachehandler/cachehandler.class.php';
    require_once 'thirdparty/phpfastcache/phpfastcache.php';
    session_start();

    $aColumns = array( 'id', 'renr', 'vonr', 'title', 'bname', 'netvalue', 'grossvalue', 'crtdate', 'duedate', 'payeddate', 'status' );
     
    /* Indexed column (used for fast and accurate table cardinality) */
    $sIndexColumn = "id";
     
    /* DB table to use */
    $sTable = "invoiceouts";
     
    /* Database connection information */
    $gaSql['user']       = $_CONFIG->db->user;
    $gaSql['password']   = $_CONFIG->db->pass;
    $gaSql['db']         = $_CONFIG->db->name;
    $gaSql['server']     = $_CONFIG->db->host;
     
    $DB = new DBMysql();
    $DB->connect($_CONFIG->db);
    
    Global $_USER;
    $_USER = new User();
    $_USER = User::login($_SESSION["login"], $_SESSION["password"], $_SESSION["domain"]);
     
    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     * If you just want to use the basic configuration for DataTables with PHP server-side, there is
     * no need to edit below this line
     */
     
    /*
     * Local functions
     */
    function fatal_error ( $sErrorMessage = '' )
    {
        header( $_SERVER['SERVER_PROTOCOL'] .' 500 Internal Server Error' );
        die( $sErrorMessage );
    }

    $newline = '
';
 
     
    /*
     * MySQL connection
     */
    if ( ! $gaSql['link'] = mysql_pconnect( $gaSql['server'], $gaSql['user'], $gaSql['password']  ) )
    {
        fatal_error( 'Could not open connection to server' );
    }
 
    if ( ! mysql_select_db( $gaSql['db'], $gaSql['link'] ) )
    {
        fatal_error( 'Could not select database ' );
    }
     
     
    /*
     * Paging
     */
    $sLimit = "";
    if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
    {
        $sLimit = "LIMIT ".intval( $_GET['iDisplayStart'] ).", ".
            intval( $_GET['iDisplayLength'] );
    }
     
     
    /*
     * Ordering
     */
    $sOrder = "";
    if ( isset( $_GET['iSortCol_0'] ) )
    {
        $sOrder = "ORDER BY  ";
        for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
        {
            if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
            {
                $sOrder .= $aColumns[ intval( $_GET['iSortCol_'.$i] ) ]."
                    ".($_GET['sSortDir_'.$i]==='asc' ? 'asc' : 'desc') .", ";
            }
        }
         
        $sOrder = substr_replace( $sOrder, "", -2 );
        if ( $sOrder == "ORDER BY" )
        {
            $sOrder = "";
        }
    }
     
     
    /*
     * Filtering
     * NOTE this does not match the built-in DataTables filtering which does it
     * word by word on any field. It's possible to do here, but concerned about efficiency
     * on very large tables, and MySQL's regex functionality is very limited
     */
    $sWhere = "";
    if ( isset($_GET['sSearch']) && $_GET['sSearch'] != "" )
    {
        $_GET['sSearch'] = utf8_decode($_GET['sSearch']);
        $sWhere = "WHERE (";
        for ( $i=0 ; $i<count($aColumns) ; $i++ )
        {
            if ( isset($_GET['bSearchable_'.$i]) && $_GET['bSearchable_'.$i] == "true" )
            {
                $sWhere .= $aColumns[$i]." LIKE '%".mysql_real_escape_string( $_GET['sSearch'] )."%' OR ";
            }
        }
        $sWhere = substr_replace( $sWhere, "", -3 );
        $sWhere .= ')';
    }
     
    /* Individual column filtering */
    for ( $i=0 ; $i<count($aColumns) ; $i++ )
    {
        if ( isset($_GET['bSearchable_'.$i]) && $_GET['bSearchable_'.$i] == "true" && $_GET['sSearch_'.$i] != '' )
        {
            if ( $sWhere == "" )
            {
                $sWhere = "WHERE ";
            }
            else
            {
                $sWhere .= " AND ";
            }
            $sWhere .= $aColumns[$i]." LIKE '%".mysql_real_escape_string($_GET['sSearch_'.$i])."%' ";
        }
    }
    
    if ( $sWhere == "" )
    {
        $sWhere = " WHERE `status` > 0 ";
    }
    else
    {
        $sWhere .= " AND `status` > 0 ";
    }

    if ($_GET['start'] != ""){
        if ($sWhere == ""){
            $sWhere .= " WHERE crtdate >= {$_GET['start']} ";
        } else {
            $sWhere .= " AND crtdate >= {$_GET['start']} ";
        }
    }
    if ($_GET['end'] != ""){
        if ($sWhere == ""){
            $sWhere .= " WHERE crtdate <= {$_GET['end']} ";
        } else {
            $sWhere .= " AND crtdate <= {$_GET['end']} ";
        }
    }

    if ($_REQUEST["bcid"]){
        if ($sWhere == ""){
            $sWhere .= " WHERE bcid = " . (int)$_REQUEST["bcid"];
        } else {
            $sWhere .= " AND bcid = " . (int)$_REQUEST["bcid"];
        }
    }

    if ($_REQUEST["status"] > 0){
        if ($sWhere == ""){
            $sWhere .= " WHERE `status` = " . (int)$_REQUEST["status"];
        } else {
            $sWhere .= " AND `status` = " . (int)$_REQUEST["status"];
        }
    }

    /*
     * SQL queries
     * Get data to display
     */
    $sQuery = "
        SELECT * FROM
        (
        SELECT
        invoiceouts.id,
        invoiceouts.number as renr,
        collectiveinvoice.number as vonr,
        collectiveinvoice.title,
        CONCAT(businesscontact.name1,' ',businesscontact.name2) as bname,
        businesscontact.id as bcid,
        invoiceouts.netvalue,
        invoiceouts.grossvalue,
        invoiceouts.crtdate,
        invoiceouts.duedate,
        invoiceouts.payeddate,
        invoiceouts.`status`
        FROM
        invoiceouts
        INNER JOIN collectiveinvoice ON invoiceouts.colinv = collectiveinvoice.id
        INNER JOIN businesscontact ON collectiveinvoice.businesscontact = businesscontact.id
        ) t1
        $sWhere
        $sOrder
        $sLimit
    ";
    
//     var_dump($sQuery);
    
    $rResult = mysql_query( $sQuery, $gaSql['link'] ) or fatal_error( 'MySQL Error: ' . mysql_errno() );
     
    /* Data set length after filtering */
    $sQuery = "
        SELECT count(id) FROM
        (
        SELECT
        invoiceouts.id,
        invoiceouts.number as renr,
        collectiveinvoice.number as vonr,
        collectiveinvoice.title,
        CONCAT(businesscontact.name1,' ',businesscontact.name2) as bname,
        businesscontact.id as bcid,
        invoiceouts.netvalue,
        invoiceouts.grossvalue,
        invoiceouts.crtdate,
        invoiceouts.duedate,
        invoiceouts.payeddate,
        invoiceouts.`status`
        FROM
        invoiceouts
        INNER JOIN collectiveinvoice ON invoiceouts.colinv = collectiveinvoice.id
        INNER JOIN businesscontact ON collectiveinvoice.businesscontact = businesscontact.id
        ) t1
        $sWhere
    ";
//     var_dump($sQuery);
    $rResultFilterTotal = mysql_query( $sQuery, $gaSql['link'] ) or fatal_error( 'MySQL Error: ' . mysql_errno() );
    $aResultFilterTotal = mysql_fetch_array($rResultFilterTotal);
    $iFilteredTotal = $aResultFilterTotal[0];
    
     
    /* Total data set length */
    $sQuery = "
        SELECT COUNT(".$sIndexColumn.")
        FROM   $sTable WHERE `status` > 0
    ";
//     var_dump($sQuery);
    $rResultTotal = mysql_query( $sQuery, $gaSql['link'] ) or fatal_error( 'MySQL Error: ' . mysql_errno() );
    $aResultTotal = mysql_fetch_array($rResultTotal);
    $iTotal = $aResultTotal[0];
     
     
    /*
     * Output
     */
    $output = array(
        "sEcho" => intval($_GET['sEcho']),
        "iTotalRecords" => $iTotal,
        "iTotalDisplayRecords" => $iFilteredTotal,
        "aaData" => array()
    );

//$aColumns = array( 'id', 'rene', 'vonr', 'title', 'bname', 'netvalue', 'grossvalue', 'duedate', 'payeddate', 'status' );
    while ( $aRow = mysql_fetch_array( $rResult ) )
    {
        $row = array();
        for ( $i=0 ; $i<count($aColumns) ; $i++ )
        {
            if ( $aColumns[$i] == 'status' )
            {
                switch ($aRow[ $aColumns[$i] ]) {
                    case 0:
                        $row[] = 'gelöscht';
                        break;
                    case 1:
                        $row[] = 'offen';
                        break;
                    case 2:
                        $row[] = 'bezahlt';
                        break;
                    case 3:
                        $row[] = 'storniert';
                        break;
                }
            }
            else if ( $aColumns[$i] == 'netvalue' )
            {
                $row[] = printPrice($aRow[ $aColumns[$i] ],2).'€';
            }
            else if ( $aColumns[$i] == 'grossvalue' )
            {
                $row[] = printPrice($aRow[ $aColumns[$i] ],2).'€';
            }
            else if ( $aColumns[$i] == 'crtdate' )
            {
                $row[] = date('d.m.y',$aRow[ $aColumns[$i] ]);
            }
            else if ( $aColumns[$i] == 'duedate' )
            {
                $today = mktime(0,0,1,date('m',time()),date('d',time()),date('y',time()));
                $today_end = mktime(23,59,59,date('m',time()),date('d',time()),date('y',time()));
                $duedate = date('d.m.y',$aRow[ $aColumns[$i] ]);
                if ($aRow[ $aColumns[$i] ] < $today) {
                    $row[] = "<span style='color: red'>{$duedate}</span>";
                } else if ($aRow[ $aColumns[$i] ] >= $today && $aRow[ $aColumns[$i] ] <= $today_end) {
                    $row[] = "<span style='color: orange'>{$duedate}</span>";
                } else if ($aRow[ $aColumns[$i] ] >= $today) {
                    $row[] = "<span style='color: green'>{$duedate}</span>";
                }
            }
            else if ( $aColumns[$i] == 'payeddate' )
            {
                if ($aRow[ $aColumns[$i] ] > 0)
                    $row[] = date('d.m.y',$aRow[ $aColumns[$i] ]);
                else
                    $row[] = '';
            }
            else if ( $aColumns[$i] == 'bname' )
            {
                $row[] = nl2br(htmlentities(utf8_encode($aRow[ $aColumns[$i] ])));
            }
            else if ( $aColumns[$i] == 'id' )
            {
                $row[] = $aRow[ $aColumns[$i] ];
            }
            else
            {
                $row[] = nl2br(htmlentities(utf8_encode($aRow[ $aColumns[$i] ])));
            }
        }
        $output['aaData'][] = $row;
    }
    echo json_encode( $output );
?>