<?php

    chdir("../../../");
    require_once 'config.php';
    
    require_once("config.php");
    require_once("libs/basic/mysql.php");
    require_once("libs/basic/globalFunctions.php");
    require_once("libs/basic/user/user.class.php");
    require_once("libs/basic/groups/group.class.php");
    require_once("libs/basic/clients/client.class.php");
    require_once("libs/basic/translator/translator.class.php");
    require_once("libs/basic/countries/country.class.php");

    session_start();
    
    $DB = new DBMysql();
    $DB->connect($_CONFIG->db);
    global $_LANG;
    
    // Login
    if ($_REQUEST["userid"]){
        $_USER = new User((int)$_REQUEST["userid"]);
    } else {
        $_USER = new User();
        $_USER = User::login($_SESSION["login"], $_SESSION["password"], $_SESSION["domain"]);
    }
    $_LANG = $_USER->getLang();
    
    if ($_USER == false){
        error_log("Login failed (basic-importer.php)");
        die("Login failed");
    }
    
    $clientid = $_USER->getClient()->getId();

    $aColumns = array( 'id', 'title', 'atitle', 'bname', 'wh_amount', 'orderdate', 'amount', 'status', 'comment', 'hash1', 'hash2' );
     
    /* Indexed column (used for fast and accurate table cardinality) */
    $sIndexColumn = "id";
     
    /* DB table to use */
    $sTable = "personalization_orders";
     
    /* Database connection information */
    $gaSql['user']       = $_CONFIG->db->user;
    $gaSql['password']   = $_CONFIG->db->pass;
    $gaSql['db']         = $_CONFIG->db->name;
    $gaSql['server']     = $_CONFIG->db->host;
     
     
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
 
     
    /*
     * MySQL connection
     */
    if ( ! $gaSql['link'] = mysqli_connect( $gaSql['server'], $gaSql['user'], $gaSql['password']  ) )
    {
        fatal_error( 'Could not open connection to server' );
    }
 
    if ( ! mysqli_select_db( $gaSql['db'], $gaSql['link'] ) )
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
            if ( isset($_GET['bSearchable_'.$i]) && $_GET['bSearchable_'.$i] == "true" && $aColumns[$i] != "id" && $aColumns[$i] != "art_picture" )
            {
                $sWhere .= $aColumns[$i]." LIKE '%".mysqli_real_escape_string( $gaSql['link'], $_GET['sSearch'] )."%' OR ";
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
            $sWhere .= $aColumns[$i]." LIKE '%".mysqli_real_escape_string( $gaSql['link'], $_GET['sSearch_'.$i])."%' ";
        }
    }
    
    if ( $sWhere == "" )
    {
        $sWhere = " WHERE `status` >= 1 AND orderdate > 0 ";
    }
    else
    {
        $sWhere .= " AND `status` >= 1 AND orderdate > 0 ";
    }
    
    if ($_GET['start'] != ""){
        if ($sWhere == ""){
            $sWhere .= " WHERE orderdate >= {$_GET['start']} ";
        } else {
            $sWhere .= " AND orderdate >= {$_GET['start']} ";
        }
    }
    if ($_GET['end'] != ""){
        if ($sWhere == ""){
            $sWhere .= " WHERE orderdate <= {$_GET['end']} ";
        } else {
            $sWhere .= " AND orderdate <= {$_GET['end']} ";
        }
    }
    if ($_GET['customer'] != ""){
        if ($sWhere == ""){
            $sWhere .= " WHERE bid = {$_GET['customer']} ";
        } else {
            $sWhere .= " AND bid = {$_GET['customer']} ";
        }
    }
    
    /*
     * SQL queries
     * Get data to display
     */
    $sQuery = "SELECT * FROM (
               SELECT
               personalization_orders.id,
               personalization_orders.title as title,
               article.title as atitle,
               CONCAT(businesscontact.name1,' ',businesscontact.name2) as bname,
               businesscontact.id as bid,
               (SELECT SUM(wh_amount) FROM warehouse WHERE warehouse.wh_status > 0 AND warehouse.wh_articleid = personalization.article) AS wh_amount,
               personalization_orders.orderdate,
               personalization_orders.amount,
               personalization_orders.`status`,
               personalization_orders.comment,
               (SELECT doc_hash FROM documents WHERE documents.doc_req_id = personalization_orders.id ORDER BY documents.id LIMIT 1) as hash1,
               (SELECT doc_hash FROM documents WHERE documents.doc_req_id = personalization_orders.id ORDER BY documents.id DESC LIMIT 1) as hash2
               FROM
               personalization_orders
               LEFT JOIN personalization ON personalization.id = personalization_orders.persoid
               LEFT JOIN article ON article.id = personalization.article
               LEFT JOIN businesscontact ON businesscontact.id = personalization_orders.customerid ) t1 
               $sWhere
               $sOrder
               $sLimit
               ";
    
//     var_dump($sQuery);
    
    $rResult = mysqli_query( $sQuery, $gaSql['link'] ) or fatal_error( 'MySQL Error: ' . mysqli_errno( $gaSql['link'] ) );
     
    /* Data set length after filtering */
    $sQuery = "
       SELECT COUNT(id) FROM 
       ( SELECT
       personalization_orders.id,
       personalization_orders.title as title,
       article.title as atitle,
       CONCAT(businesscontact.name1,' ',businesscontact.name2) as bname,
       businesscontact.id as bid,
       (SELECT SUM(wh_amount) FROM warehouse WHERE warehouse.wh_status > 0 AND warehouse.wh_articleid = personalization.article) AS wh_amount,
       personalization_orders.orderdate,
       personalization_orders.amount,
       personalization_orders.`status`,
       personalization_orders.comment,
       (SELECT doc_hash FROM documents WHERE documents.doc_req_id = personalization_orders.id ORDER BY documents.id LIMIT 1) as hash1,
       (SELECT doc_hash FROM documents WHERE documents.doc_req_id = personalization_orders.id ORDER BY documents.id DESC LIMIT 1) as hash2
       FROM
       personalization_orders
       LEFT JOIN personalization ON personalization.id = personalization_orders.persoid
       LEFT JOIN article ON article.id = personalization.article
       LEFT JOIN businesscontact ON businesscontact.id = personalization_orders.customerid ) t1
        $sWhere
    ";
//     var_dump($sQuery);
    $rResultFilterTotal = mysqli_query( $sQuery, $gaSql['link'] ) or fatal_error( 'MySQL Error: ' . mysqli_errno( $gaSql['link'] ) );
    $aResultFilterTotal = mysqli_fetch_array($rResultFilterTotal);
    $iFilteredTotal = $aResultFilterTotal[0];
    
     
    /* Total data set length */
    $sQuery = "
        SELECT COUNT(personalization_orders.id) 
        FROM
        personalization_orders
        LEFT JOIN personalization ON personalization.id = personalization_orders.persoid
        LEFT JOIN article ON article.id = personalization.article
        LEFT JOIN businesscontact ON businesscontact.id = personalization_orders.customerid
        WHERE personalization_orders.`status` >= 1 AND personalization_orders.orderdate > 0 
    ";
//     var_dump($sQuery);
    $rResultTotal = mysqli_query( $sQuery, $gaSql['link'] ) or fatal_error( 'MySQL Error: ' . mysqli_errno( $gaSql['link'] ) );
    $aResultTotal = mysqli_fetch_array($rResultTotal);
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
     
    while ( $aRow = mysqli_fetch_array( $rResult ) )
    {
        $row = array();
        for ( $i=0 ; $i<count($aColumns) ; $i++ )
        {
            if ( $aColumns[$i] == 'picture' )
            {
                if ($aRow[$aColumns[$i]] != NULL && $aRow[$aColumns[$i]] !=""){
					$row[] = '<img src="images/products/'.$aRow[$aColumns[$i]].'" width="100px">&nbsp;';
        		} else {
        			$row[] = '<img src="images/icons/image.png" title="Kein Bild hinterlegt" alt="Bild">';
        		}
            }
            else if ( $aColumns[$i] == 'id' )
            {
                /* do not print the id */
                $row[] = $aRow[ $aColumns[$i] ];
            }
            else if ( $aColumns[$i] == 'orderdate' )
            {
                /* do not print the id */
                $row[] = date("d.m.Y",$aRow[ $aColumns[$i] ]);
            }
            else if ( $aColumns[$i] == 'status' )
            {
//                 $tmp_row = '';

//                 $tmp_row .= '<a href="index.php?page=libs/modules/personalization/personalization.order.overview.php&poid='.$aRow[ $aColumns[0] ].'&setStatus=2">';
//                 $tmp_row .= '<img class="select" src="./images/status/';
//                 if($aRow[ $aColumns[$i] ] == 2){
//                     $tmp_row .= 'orange.gif';
//                 } else {
//                     $tmp_row .= 'gray.gif';
//                 }
//                 $tmp_row .= '" title="Gesendet u. Bestellt"></a>';


//                 $tmp_row .= '<a href="index.php?page=libs/modules/personalization/personalization.order.overview.php&poid='.$aRow[ $aColumns[0] ].'&setStatus=3">';
//                 $tmp_row .= '<img class="select" src="./images/status/';
//                 if($aRow[ $aColumns[$i] ] == 3){
//                     $tmp_row .= 'yellow.gif';
//                 } else {
//                     $tmp_row .= 'gray.gif';
//                 }
//                 $tmp_row .= '" title="In Bearbeitung"></a>';


//                 $tmp_row .= '<a href="index.php?page=libs/modules/personalization/personalization.order.overview.php&poid='.$aRow[ $aColumns[0] ].'&setStatus=4">';
//                 $tmp_row .= '<img class="select" src="./images/status/';
//                 if($aRow[ $aColumns[$i] ] == 4){
//                     $tmp_row .= 'lila.gif';
//                 } else {
//                     $tmp_row .= 'gray.gif';
//                 }
//                 $tmp_row .= '" title="Fertig u. im Versand"></a>';


//                 $tmp_row .= '<a href="index.php?page=libs/modules/personalization/personalization.order.overview.php&poid='.$aRow[ $aColumns[0] ].'&setStatus=5">';
//                 $tmp_row .= '<img class="select" src="./images/status/';
//                 if($aRow[ $aColumns[$i] ] == 5){
//                     $tmp_row .= 'green.gif';
//                 } else {
//                     $tmp_row .= 'gray.gif';
//                 }
//                 $tmp_row .= '" title="Fertig u. Abholbereit"></a>';
                
                
//                 $row[] = nl2br($tmp_row);
            }
            else if ( $aColumns[$i] == 'comment' )
            {
                if($aRow["comment"] != NULL && $aRow["comment"] != ""){
					$row[] = '<span class="glyphicons glyphicons-chat" title="'.$aRow["comment"].'"/></span>';
				} else {
					$row[] =  '&emsp;';
				}
            }
            else if ( $aColumns[$i] == 'hash1' )
            {
                /* do not print */
            }
            else if ( $aColumns[$i] == 'hash2' )
            {
                /* do not print */
            }
            else if ( $aColumns[$i] == 'bid' )
            {
                /* do not print */
            }
            else
            {
                /* General output */
                $row[] = nl2br(htmlentities(utf8_encode($aRow[ $aColumns[$i] ])));
            }
        }
        $tmp_options = '<a class="icon-link" target="_blank" href="./docs/personalization/'.$clientid.'.per_'.$aRow["hash1"].'_e.pdf">
                      <span class="glyphicons glyphicons-download-alt" title="Download Vorderseite mit Hintergrund"></span></a>
    	               <a href="./docs/personalization/'.$clientid.'.per_'.$aRow["hash1"].'_p.pdf" class="icon-link" target="_blank">
                       <span class="glyphicons glyphicons-download-alt" title="Download Vorderseite ohne Hintergrund"></span></a></br>';
        if ($aRow["hash2"] != $aRow["hash1"] && $aRow["hash2"] != "")
        {
            $tmp_options .= '<a class="icon-link" target="_blank" href="./docs/personalization/'.$clientid.'.per_'.$aRow["hash2"].'_e.pdf">
                           <span class="glyphicons glyphicons-download-alt" title="Download R&uuml;ckseite mit Hintergrund"></span></a>
        	                 <a href="./docs/personalization/'.$clientid.'.per_'.$aRow["hash2"].'_p.pdf" class="icon-link" target="_blank">
                            <span class="glyphicons glyphicons-download-alt" title="Download R&uuml;ckseite ohne Hintergrund"></span></a>';
        }
        $tmp_options .= '&ensp;<a class="icon-link" href="#" onclick="askDel(\'index.php?page=libs/modules/personalization/personalization.order.overview.php&exec=delete&delid='.$aRow[ $aColumns[0] ].'\')"><span style="color:red" class="glyphicons glyphicons-remove"></span></a>';
        $row[] = $tmp_options;
        $output['aaData'][] = $row;
    }
     
    echo json_encode( $output );
?>