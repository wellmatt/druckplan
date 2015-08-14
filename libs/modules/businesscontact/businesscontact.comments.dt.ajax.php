<?php
    chdir('../../../');
    require_once 'config.php';
    require_once 'libs/modules/comment/comment.class.php';

    $aColumns = array( 'id', 'title', 'crtuser', 'crtdate', 'visability' );
     
    /* Indexed column (used for fast and accurate table cardinality) */
    $sIndexColumn = "id";
     
    /* DB table to use */
    $sTable = "comments";
     
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
            if ( isset($_GET['bSearchable_'.$i]) && $_GET['bSearchable_'.$i] == "true" && $aColumns[$i] != "id" && $aColumns[$i] != "art_picture" )
            {
                $sWhere .= $aColumns[$i]." LIKE '%".mysql_real_escape_string( utf8_decode($_GET['sSearch']) )."%' OR ";
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
        $sWhere = " WHERE state = 1 AND module = 'BusinessContact' AND objectid = {$_REQUEST["bcid"]}";
    }
    else
    {
        $sWhere .= " AND state = 1 AND module = 'BusinessContact' AND objectid = {$_REQUEST["bcid"]}";
    }

    if ((int)$_REQUEST["access"] != 1)
        $sWhere .= " AND (visability IN (1,4) OR (visability = 3 AND crtuserid = {$_REQUEST["userid"]}))";
    else
        $sWhere .= " AND (visability IN (1,4,2) OR (visability = 3 AND crtuserid = {$_REQUEST["userid"]}))";
        
    
    /*
     * SQL queries
     * Get data to display
     */
    $sQuery = "SELECT * FROM (
                SELECT
                comments.id,
                comments.title,
                CONCAT(`user`.user_firstname,' ',`user`.user_lastname) as crtuser,
                comments.crtdate,
                comments.visability,
                comments.state,
                comments.module,
                comments.objectid,
                comments.crtuser as crtuserid
                FROM
                comments
                INNER JOIN `user` ON comments.crtuser = `user`.id
               ) t1 
               $sWhere
               $sOrder
               $sLimit
               ";
    
//     var_dump($sQuery);
    
    $rResult = mysql_query( $sQuery, $gaSql['link'] ) or fatal_error( 'MySQL Error: ' . mysql_errno() );
     
    /* Data set length after filtering */
    $sQuery = "SELECT count(id) FROM (
                SELECT
                comments.id,
                comments.title,
                CONCAT(`user`.user_firstname,' ',`user`.user_lastname) as crtuser,
                comments.crtdate,
                comments.visability,
                comments.state,
                comments.module,
                comments.objectid,
                comments.crtuser as crtuserid
                FROM
                comments
                INNER JOIN `user` ON comments.crtuser = `user`.id
               ) t1  
        $sWhere
    ";
//     var_dump($sQuery);
    $rResultFilterTotal = mysql_query( $sQuery, $gaSql['link'] ) or fatal_error( 'MySQL Error: ' . mysql_errno() );
    $aResultFilterTotal = mysql_fetch_array($rResultFilterTotal);
    $iFilteredTotal = $aResultFilterTotal[0];
    
     
    /* Total data set length */
    $sQuery = "SELECT count(id) FROM (
                SELECT
                comments.id,
                comments.title,
                CONCAT(`user`.user_firstname,' ',`user`.user_lastname) as crtuser,
                comments.crtdate,
                comments.visability,
                comments.state,
                comments.module,
                comments.objectid,
                comments.crtuser as crtuserid
                FROM
                comments
                INNER JOIN `user` ON comments.crtuser = `user`.id 
                WHERE state = 1
               ) t1";
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
//         $aColumns = array( 'id', 'title', 'crtuser', 'crtdate', 'visability' );
        $row = array();
        for ( $i=0 ; $i<count($aColumns) ; $i++ )
        {
            if ( $aColumns[$i] == 'id' )
            {
                /* do not print the id */
                $row[] = $aRow[ $aColumns[$i] ];
            }
            else if ( $aColumns[$i] == 'crtdate' )
            {
                /* do not print the id */
                $row[] = date("d.m.Y H:i",$aRow[ $aColumns[$i] ]);
            }
            else if ( $aColumns[$i] == 'visability' )
            {
                switch ($aRow[ $aColumns[$i] ])
                {
                    case Comment::VISABILITY_INTERNAL:
                        $row[] = "INTERN";
                        break;
                    case Comment::VISABILITY_PRIVATE:
                        $row[] = "PRIVAT";
                        break;
                    case Comment::VISABILITY_PUBLIC:
                        $row[] = "PUBLIC";
                        break;
                    case Comment::VISABILITY_PUBLICMAIL:
                        break;
                }
            }
            else
            {
                /* General output */
                $row[] = nl2br(htmlentities(utf8_encode($aRow[ $aColumns[$i] ])));
            }
        }
        $output['aaData'][] = $row;
    }
     
    echo json_encode( $output );
?>