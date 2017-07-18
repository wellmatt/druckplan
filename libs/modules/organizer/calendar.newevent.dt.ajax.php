<?php

    error_reporting(-1);
    ini_set('display_errors', 1);
    require_once '../../../config.php';

    $aColumns = array( 'bname', 'cname' );
     
    /* Indexed column (used for fast and accurate table cardinality) */
    $sIndexColumn = "id";
     
    /* DB table to use */
    $sTable = "businesscontact";
     
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
 
    if ( ! mysqli_select_db( $gaSql['link'], $gaSql['db'] ) )
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
        $sWhere = " WHERE active = 1 ";
    }
    else
    {
        $sWhere .= " AND active = 1 ";
    }
    
    /*
     * SQL queries
     * Get data to display
     */
    $sQuery = "SELECT * FROM (
                SELECT
                contactperson.id,
                CONCAT(businesscontact.name1,' ',businesscontact.name2) AS bname,
                CONCAT(contactperson.name1,', ',contactperson.name2) AS cname,
                businesscontact.active as active
                FROM
                contactperson
                INNER JOIN businesscontact ON contactperson.businesscontact = businesscontact.id
               ) t1 
               $sWhere
               $sOrder
               $sLimit
               ";
    
//     var_dump($sQuery);
    
    $rResult = mysqli_query( $gaSql['link'], $sQuery ) or fatal_error( 'MySQL Error: ' . mysqli_errno( $gaSql['link'] ) );
     
    /* Data set length after filtering */
    $sQuery = "
        SELECT COUNT(id) FROM (
            SELECT
            contactperson.id,
            CONCAT(businesscontact.name1,' ',businesscontact.name2) AS bname,
            CONCAT(contactperson.name1,', ',contactperson.name2) AS cname,
            businesscontact.active as active
            FROM
            contactperson
            INNER JOIN businesscontact ON contactperson.businesscontact = businesscontact.id
           ) t1 
        $sWhere
    ";
//     var_dump($sQuery);
    $rResultFilterTotal = mysqli_query( $gaSql['link'], $sQuery ) or fatal_error( 'MySQL Error: ' . mysqli_errno( $gaSql['link'] ) );
    $aResultFilterTotal = mysqli_fetch_array($rResultFilterTotal);
    $iFilteredTotal = $aResultFilterTotal[0];
    
     
    /* Total data set length */
    $sQuery = "
        SELECT COUNT(id) FROM (
            SELECT
            contactperson.id,
            CONCAT(businesscontact.name1,' ',businesscontact.name2) AS bname,
            CONCAT(contactperson.name1,', ',contactperson.name2) AS cname,
            businesscontact.active as active
            FROM
            contactperson
            INNER JOIN businesscontact ON contactperson.businesscontact = businesscontact.id
           ) t1 
        WHERE active = 1
    ";
//     var_dump($sQuery);
    $rResultTotal = mysqli_query( $gaSql['link'], $sQuery ) or fatal_error( 'MySQL Error: ' . mysqli_errno( $gaSql['link'] ) );
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
        $row[] = '<span class="glyphicons glyphicons-exclamation-sign"></span>'.nl2br(htmlentities(utf8_encode($aRow['bname'])));
        $row[] = '<input type="checkbox" id="chkb_'.$aRow['id'].'" onclick="add_contactperson(this)" value="'.$aRow['id'].'">'.nl2br(htmlentities(utf8_encode($aRow['cname']))).'</td>
				  <input type="hidden" name="contactperson_name_'.$aRow['id'].'" id="contactperson_name_'.$aRow['id'].'" value="'.nl2br(htmlentities(utf8_encode($aRow['cname']))).'">';
        $output['aaData'][] = $row;
    }
     
    echo json_encode( $output );
?>