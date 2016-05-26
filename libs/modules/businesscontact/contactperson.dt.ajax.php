<?php
    error_reporting(-1);
    ini_set('display_errors', 1);

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
    require_once 'libs/modules/organizer/contact.class.php';
    require_once 'libs/modules/businesscontact/businesscontact.class.php';
    require_once 'libs/modules/chat/chat.class.php';
    require_once 'libs/modules/calculation/order.class.php';
    require_once 'libs/modules/schedule/schedule.class.php';
    require_once 'libs/modules/tickets/ticket.class.php';
    require_once 'libs/modules/comment/comment.class.php';

    session_start();
    
    $DB = new DBMysql();
    $DB->connect($_CONFIG->db);
    global $_LANG;
    
    $_USER = new User();
    $_USER = User::login($_SESSION["login"], $_SESSION["password"], $_SESSION["domain"]);
    $_LANG = $_USER->getLang();

    $aColumns = array( 'id', 'cpname', 'bcname' );
     
    /* Indexed column (used for fast and accurate table cardinality) */
    $sIndexColumn = "id";
     
    /* DB table to use */
    $sTable = "contactperson";
     
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
    
    /*
     * SQL queries
     * Get data to display
     */
    $sQuery = "
        SELECT * FROM 
        (SELECT
        contactperson.id,
        CONCAT(contactperson.name1,', ',contactperson.name2) cpname,
        CONCAT(businesscontact.name1,' ',businesscontact.name2) bcname,
        businesscontact.id as bcid,
        contactperson.phone,
        contactperson.mobil,
        contactperson.email
        FROM
        contactperson
        INNER JOIN businesscontact ON businesscontact.id = contactperson.businesscontact) cps 
        $sWhere
        $sOrder
        $sLimit
    ";
    
//     var_dump($sQuery);
    
    $rResult = mysql_query( $sQuery, $gaSql['link'] ) or fatal_error( 'MySQL Error: ' . mysql_errno() );
     
    /* Data set length after filtering */
    $sQuery = "
        SELECT COUNT(".$sIndexColumn.") FROM 
        (SELECT
        contactperson.id,
        CONCAT(contactperson.name1,', ',contactperson.name2) cpname,
        CONCAT(businesscontact.name1,' ',businesscontact.name2) bcname,
        businesscontact.id as bcid,
        contactperson.phone,
        contactperson.mobil,
        contactperson.email
        FROM
        contactperson
        INNER JOIN businesscontact ON businesscontact.id = contactperson.businesscontact) cps 
        $sWhere
        $sOrder
    ";
//     var_dump($sQuery);
    $rResultFilterTotal = mysql_query( $sQuery, $gaSql['link'] ) or fatal_error( 'MySQL Error: ' . mysql_errno() );
    $aResultFilterTotal = mysql_fetch_array($rResultFilterTotal);
    $iFilteredTotal = $aResultFilterTotal[0];
    
     
    /* Total data set length */
    $sQuery = "
        SELECT COUNT(".$sIndexColumn.")
        FROM  (SELECT
        contactperson.id,
        CONCAT(contactperson.name1,', ',contactperson.name2) cpname,
        CONCAT(businesscontact.name1,' ',businesscontact.name2) bcname,
        businesscontact.id as bcid,
        contactperson.phone,
        contactperson.mobil,
        contactperson.email
        FROM
        contactperson
        INNER JOIN businesscontact ON businesscontact.id = contactperson.businesscontact) cps
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
     
    while ( $aRow = mysql_fetch_array( $rResult ) )
    {
        $row = array();
        for ( $i=0 ; $i<count($aColumns) ; $i++ )
        {
            if ( $aColumns[$i] == 'id' )
            {
                $row[] = $aRow[ $aColumns[$i] ];
            }
            else if ( $aColumns[$i] == 'phone' ){
                
            }
            else if ( $aColumns[$i] == 'mobil' ){
                
            }
            else if ( $aColumns[$i] == 'email' ){
                
            }
            else if ( $aColumns[$i] == 'bcid' ){
                
            }
            else
            {
                /* General output */
                $row[] = nl2br(htmlentities(utf8_encode($aRow[ $aColumns[$i] ])));
            }
        }

        $tmp_row = '<a class="icon-link" href="index.php?page=libs/modules/businesscontact/businesscontact.php&exec=edit_cp&cpid='.$aRow[ $aColumns[0] ].'&id='.$aRow[ "bcid" ].'"><span class="glyphicons glyphicons-user" title="zum Ansprechpartner"></span></a>
		            <a href="mailto:'.$aRow[ "email" ].'"><span class="glyphicons glyphicons-envelope" title="Mail schicken"></span></a>';
        
        $tmp_phone = $aRow[ "phone" ];
        if ($tmp_phone != "" || $tmp_phone != NULL){
            $phone = str_replace(" ", "", $tmp_phone);  	// leerzeichen entfernen
            $phone = str_replace("+", "", $phone);			// + entfernen
            $phone = str_replace("/", "", $phone);			// / entfernen
            $phone = str_replace("-", "", $phone);			// - entfernen
            if (substr($phone, 0, 2) == "49"){
                $phone = "0".substr($phone, 2);	 // Landesvorwahl (0049) von Deutschladn durch 0 ersetzen
            } else {
                $phone = "00".$phone;							// 00 voransetzen, wenn Ausland
            }
            $phonefordial = $phone;
            $tmp_row .= '<a class="icon-link" onclick="dialNumber(\''.$_USER->getTelefonIP().'/command.htm?number='.$phonefordial.'\')" href="Javascript:"><span class="glyphicons glyphicons-phone-alt" title="'.$aRow[ "phone" ].' anrufen"></span></a>';
        }
       
        $tmp_phone = $aRow[ "mobil" ];
        if ($tmp_phone != "" || $tmp_phone != NULL){
            $phone = str_replace(" ", "", $tmp_phone);  	// leerzeichen entfernen
            $phone = str_replace("+", "", $phone);			// + entfernen
            $phone = str_replace("/", "", $phone);			// / entfernen
            $phone = str_replace("-", "", $phone);			// - entfernen
            if (substr($phone, 0, 2) == "49"){
                $phone = "0".substr($phone, 2);	 // Landesvorwahl (0049) von Deutschladn durch 0 ersetzen
            } else {
                $phone = "00".$phone;							// 00 voransetzen, wenn Ausland
            }
            $phonefordial = $phone;
            $tmp_row .= '<a class="icon-link" onclick="dialNumber(\''.$_USER->getTelefonIP().'/command.htm?number='.$phonefordial.'\')" href="Javascript:"><span class="glyphicons glyphicons-iphone" title="'.$aRow[ "mobil" ].' anrufen"></span></a>';
        }
		
        $tmp_row .= '<a class="icon-link" href="index.php?page=libs/modules/businesscontact/businesscontact.php&exec=edit&id='.$aRow[ "bcid" ].'"><span class="glyphicons glyphicons-user" title="zum Geschäftskontakt"></span></a>';
        
		$row[] = $tmp_row;
        $output['aaData'][] = $row;
    }
     
    echo json_encode( $output );
?>