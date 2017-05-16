<?php
/**
 *  Copyright (c) 2017 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2017
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

    $aColumns = array( 'id', 'partner', 'crtdate', 'colinvnumber', 'doc_name', 'customer', 'percentage', 'value', 'creditdate', 'creditcolinvnumber', 'doc_storno_date' );
     
    /* Indexed column (used for fast and accurate table cardinality) */
    $sIndexColumn = "id";
     

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
            $sWhere .= " WHERE customerid = " . (int)$_REQUEST["bcid"];
        } else {
            $sWhere .= " AND customerid = " . (int)$_REQUEST["bcid"];
        }
    }

    if ($_REQUEST["partnerid"]){
        if ($sWhere == ""){
            $sWhere .= " WHERE partnerid = " . (int)$_REQUEST["partnerid"];
        } else {
            $sWhere .= " AND partnerid = " . (int)$_REQUEST["partnerid"];
        }
    }

    if ($_REQUEST["credited"] == 0){
        if ($sWhere == ""){
            $sWhere .= " WHERE creditcolinvid IS NULL ";
        } else {
            $sWhere .= " AND creditcolinvid IS NULL ";
        }
    } else {
        if ($sWhere == ""){
            $sWhere .= " WHERE creditcolinvid IS NOT NULL ";
        } else {
            $sWhere .= " AND creditcolinvid IS NOT NULL ";
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
            commissions.id,
            commissions.percentage,
            commissions.`value`,
            commissions.crtdate,
            commissions.creditdate,
            CONCAT(b1.name1, ' ', b1.name2) AS partner,
            b1.id AS partnerid,
            CONCAT(b2.name1, ' ', b2.name2) AS customer,
            b2.id AS customerid,
            c1.id AS colinvid,
            c1.number AS colinvnumber,
            c2.id AS creditcolinvid,
            c2.number AS creditcolinvnumber,
			d1.doc_name,
			d1.doc_storno_date
        FROM
            commissions
        INNER JOIN businesscontact AS b1 ON commissions.partner = b1.id
        INNER JOIN businesscontact AS b2 ON commissions.businesscontact = b2.id
        INNER JOIN collectiveinvoice AS c1 ON commissions.colinv = c1.id
        LEFT JOIN collectiveinvoice AS c2 ON commissions.creditcolinv = c2.id
		LEFT JOIN documents AS d1 ON commissions.doc = d1.id
        ) t1
        $sWhere
        $sOrder
        $sLimit
    ";
//mit datenbank verbinden
$test = mysql_query("Select count(status) From invoiceouts Where status = 2");
$status = mysql_fetch_assoc($test);
//            echo var_dump($test);

    
//     var_dump($sQuery);
    
    $rResult = mysql_query( $sQuery, $gaSql['link'] ) or fatal_error( 'MySQL Error: ' . mysql_errno() );

    /* Data set length after filtering */
    $sQuery = "
        SELECT count(id) FROM
        (
        SELECT
            commissions.id,
            commissions.percentage,
            commissions.`value`,
            commissions.crtdate,
            commissions.creditdate,
            CONCAT(b1.name1, ' ', b1.name2) AS partner,
            b1.id AS partnerid,
            CONCAT(b2.name1, ' ', b2.name2) AS customer,
            b2.id AS customerid,
            c1.id AS colinvid,
            c1.number AS colinvnumber,
            c2.id AS creditcolinvid,
            c2.number AS creditcolinvnumber,
			d1.doc_name,
			d1.doc_storno_date
        FROM
            commissions
        INNER JOIN businesscontact AS b1 ON commissions.partner = b1.id
        INNER JOIN businesscontact AS b2 ON commissions.businesscontact = b2.id
        INNER JOIN collectiveinvoice AS c1 ON commissions.colinv = c1.id
        LEFT JOIN collectiveinvoice AS c2 ON commissions.creditcolinv = c2.id
		LEFT JOIN documents AS d1 ON commissions.doc = d1.id
        ) t1
        $sWhere
    ";
//     var_dump($sQuery);
    $rResultFilterTotal = mysql_query( $sQuery, $gaSql['link'] ) or fatal_error( 'MySQL Error: ' . mysql_errno() );
    $aResultFilterTotal = mysql_fetch_array($rResultFilterTotal);
    $iFilteredTotal = $aResultFilterTotal[0];
    
     
    /* Total data set length */
    $sQuery = "
        SELECT count(id) FROM
        (
        SELECT
            commissions.id,
            commissions.percentage,
            commissions.`value`,
            commissions.crtdate,
            commissions.creditdate,
            CONCAT(b1.name1, ' ', b1.name2) AS partner,
            b1.id AS partnerid,
            CONCAT(b2.name1, ' ', b2.name2) AS customer,
            b2.id AS customerid,
            c1.id AS colinvid,
            c1.number AS colinvnumber,
            c2.id AS creditcolinvid,
            c2.number AS creditcolinvnumber,
			d1.doc_name,
			d1.doc_storno_date
        FROM
            commissions
        INNER JOIN businesscontact AS b1 ON commissions.partner = b1.id
        INNER JOIN businesscontact AS b2 ON commissions.businesscontact = b2.id
        INNER JOIN collectiveinvoice AS c1 ON commissions.colinv = c1.id
        LEFT JOIN collectiveinvoice AS c2 ON commissions.creditcolinv = c2.id
		LEFT JOIN documents AS d1 ON commissions.doc = d1.id
        ) t1
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

//$aColumns = array( 'id', 'partner', 'crtdate', 'colinvnumber', 'customer', 'percentage', 'value', 'creditdate', 'creditcolinvnumber', 'doc_name', 'doc_storno_date' );
    while ( $aRow = mysql_fetch_array( $rResult ) )
    {
        $row = array();
        for ( $i=0 ; $i<count($aColumns) ; $i++ )
        {
            if ( $aColumns[$i] == 'percentage' )
            {
                $row[] = printPrice($aRow[ $aColumns[$i] ],2).'%';
            }
            else if ( $aColumns[$i] == 'value' )
            {
                $row[] = printPrice($aRow[ $aColumns[$i] ],2).'€';
            }
            else if ( $aColumns[$i] == 'crtdate' )
            {
                $row[] = date('d.m.y',$aRow[ $aColumns[$i] ]);
            }
            else if ( $aColumns[$i] == 'creditdate' )
            {
                if ($aRow[ $aColumns[$i] ] > 0)
                    $row[] = date('d.m.y',$aRow[ $aColumns[$i] ]);
                else
                    $row[] = '';
            }
            else if ( $aColumns[$i] == 'colinvnumber' )
            {
                $row[] = '<a href="index.php?page=libs/modules/collectiveinvoice/collectiveinvoice.php&exec=edit&ciid='.$aRow['colinvid'].'">'.nl2br(htmlentities(utf8_encode($aRow[ $aColumns[$i] ]))).'</a>';
            }
            else if ( $aColumns[$i] == 'creditcolinvnumber' )
            {
                if ($aRow[ $aColumns[$i] ])
                    $row[] = '<a href="index.php?page=libs/modules/collectiveinvoice/collectiveinvoice.php&exec=edit&ciid='.$aRow['creditcolinvid'].'">'.nl2br(htmlentities(utf8_encode($aRow[ $aColumns[$i] ]))).'</a>';
                else
                    $row[] = '';
            }
            else if ( $aColumns[$i] == 'id' )
            {
                $row[] = $aRow[ $aColumns[$i] ];
            }
            else if ( $aColumns[$i] == 'colinvid' )
            {
            }
            else if ( $aColumns[$i] == 'creditcolinvid' )
            {
            }
            else if ( $aColumns[$i] == 'doc_name' )
            {
                if ($aRow['doc_storno_date'] > 0){
                    $row[] = "<s>".$aRow[ $aColumns[$i] ]."</s>";
                } else {
                    $row[] = $aRow[ $aColumns[$i] ];
                }
            }
            else if ( $aColumns[$i] == 'doc_storno_date' )
            {
            }
            else
            {
                $row[] = nl2br(htmlentities(utf8_encode($aRow[ $aColumns[$i] ])));
            }
        }
        if (!$aRow['creditcolinvid'] && !($aRow['doc_storno_date'] > 0)){
            $row[] = '<button class="btn btn-xs btn-success" type="button" onclick="createGS('.$aRow['id'].',this)"><span class="glyphicons glyphicons-plus"></span>GS-Erstellen</button>';
        } else {
            $row[] = '';
        }
        $output['aaData'][] = $row;
    }
    echo json_encode( $output );
?>