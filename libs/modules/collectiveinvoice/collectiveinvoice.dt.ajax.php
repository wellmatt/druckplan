<?php
    require_once '../../../config.php';

    $aColumns = array( 'id', 'number', 'cust_name', 'title', 'crtdate', 'status' );
     
    /* Indexed column (used for fast and accurate table cardinality) */
    $sIndexColumn = "id";
     
    /* DB table to use */
    $sTable = "collectiveinvoice";
     
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
        // $_GET['sSearch'] = $_GET['sSearch'];
        $sWhere = "WHERE (";
        for ( $i=0 ; $i<count($aColumns) ; $i++ )
        {
            if ( isset($_GET['bSearchable_'.$i]) && $_GET['bSearchable_'.$i] == "true" && $aColumns[$i] != "fremdleistung" && $aColumns[$i] != "crtdat" && $aColumns[$i] != "status" && $aColumns[$i] != "type" )
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
        if ( isset($_GET['bSearchable_'.$i]) && $_GET['bSearchable_'.$i] == "true" && $_GET['sSearch_'.$i] != '' && $aColumns[$i] != "status" )
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
        $sWhere .= " AND crtdate >= {$_GET['start']} ";
    }
    if ($_GET['end'] != ""){
        $sWhere .= " AND crtdate <= {$_GET['end']} ";
    }

    if ($_GET['cust_id'] != ""){
        $sWhere .= " AND bcid = {$_GET['cust_id']} ";
    }

    if ( isset($_GET['filter_attrib']) && $_GET['filter_attrib'] != "0" ){
        $tmp_attrib_filter = explode("|",$_GET['filter_attrib']);
        $sWhere .= " AND collectiveinvoice_attributes.attribute_id = ".$tmp_attrib_filter[0]." AND collectiveinvoice_attributes.item_id = ".$tmp_attrib_filter[1]." ";
    }
    
    
    /*
     * SQL queries
     * Get data to display
     */
    $sQuery = "SELECT id,number,cust_name,title,crtdate,`status`,bcid FROM
               (SELECT collectiveinvoice.id,collectiveinvoice.number,CONCAT(businesscontact.name1,' ',businesscontact.name2) as cust_name,collectiveinvoice.title,
               collectiveinvoice.crtdate,collectiveinvoice.`status`,businesscontact.id as bcid, collectiveinvoice_attributes.attribute_id as caid, collectiveinvoice_attributes.item_id as caiid
               FROM collectiveinvoice
               LEFT JOIN collectiveinvoice_attributes ON collectiveinvoice.id = collectiveinvoice_attributes.collectiveinvoice_id
               INNER JOIN businesscontact ON collectiveinvoice.businesscontact = businesscontact.id
               ) a
               $sWhere
               $sOrder
               $sLimit";
    
    // var_dump($sQuery);
    
    $rResult = mysql_query( $sQuery, $gaSql['link'] ) or fatal_error( 'MySQL Error: ' . mysql_errno() );
     
    /* Data set length after filtering */
//     $sQuery = "
//         SELECT FOUND_ROWS()
//     ";
    $sQuery = "
        SELECT COUNT(".$sIndexColumn.")
        FROM   
        (SELECT collectiveinvoice.id,collectiveinvoice.number,CONCAT(businesscontact.name1,' ',businesscontact.name2) as cust_name,
        collectiveinvoice.title,collectiveinvoice.crtdate,collectiveinvoice.`status`,businesscontact.id as bcid, collectiveinvoice_attributes.attribute_id as caid, collectiveinvoice_attributes.item_id as caiid
        FROM collectiveinvoice
        LEFT JOIN collectiveinvoice_attributes ON collectiveinvoice.id = collectiveinvoice_attributes.collectiveinvoice_id
        INNER JOIN businesscontact ON collectiveinvoice.businesscontact = businesscontact.id
        ) a
        $sWhere
    ";
    // var_dump($sQuery);

    $rResultFilterTotal = mysql_query( $sQuery, $gaSql['link'] ) or fatal_error( 'MySQL Error: ' . mysql_errno() );
    $aResultFilterTotal = mysql_fetch_array($rResultFilterTotal);
    $iFilteredTotal = $aResultFilterTotal[0];
    
     
    /* Total data set length */
    $sQuery = "
        SELECT COUNT(".$sIndexColumn.")
        FROM   
        (SELECT collectiveinvoice.id,collectiveinvoice.number,CONCAT(businesscontact.name1,' ',businesscontact.name2) as cust_name,collectiveinvoice.title,collectiveinvoice.crtdate,collectiveinvoice.`status`
        FROM collectiveinvoice
        LEFT JOIN collectiveinvoice_attributes ON collectiveinvoice.id = collectiveinvoice_attributes.collectiveinvoice_id
        INNER JOIN businesscontact ON collectiveinvoice.businesscontact = businesscontact.id) a
        WHERE status > 0
    ";
    // var_dump($sQuery);
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
            if ( $aColumns[$i] == 'cust_name' )
            {
                $row[] = nl2br(htmlentities(utf8_encode($aRow[ $aColumns[$i] ])));
            }
            else if ( $aColumns[$i] == 'number' )
            {
                $row[] = utf8_encode($aRow[ $aColumns[$i] ]);
            }
            else if ( $aColumns[$i] == 'title' )
            {
                $row[] = nl2br(htmlentities(utf8_encode($aRow[ $aColumns[$i] ])));
            }
            else if ( $aColumns[$i] == 'id' )
            {
                /* do not print the id */
                $row[] = $aRow[ $aColumns[$i] ];
            }
            else if ( $aColumns[$i] == 'crtdate' )
            {
                $row[] = date("d.m.Y", $aRow[ $aColumns[$i] ]);
            }
            else if ( $aColumns[$i] == 'status' )
            {
                $tmp_row = '';
                $tmp_row .= '<a href="index.php?page=libs/modules/collectiveinvoice/collectiveinvoice.php&ciid='.$aRow[ $aColumns[0] ].'&exec=setState&state=1">';
                $tmp_row .= '<img class="select" src="./images/status/';
                if($aRow[ $aColumns[$i] ] == 1){
                    $tmp_row .= 'red.svg';
                } else {
                    $tmp_row .= 'black.svg';
                }
                $tmp_row .= '" title="Vorgang angelegt"></a>';

                $tmp_row .= '<a href="index.php?page=libs/modules/collectiveinvoice/collectiveinvoice.php&ciid='.$aRow[ $aColumns[0] ].'&exec=setState&state=2">';
                $tmp_row .= '<img class="select" src="./images/status/';
                if($aRow[ $aColumns[$i] ] == 2){
                    $tmp_row .= 'orange.svg';
                } else {
                    $tmp_row .= 'black.svg';
                }
                $tmp_row .= '" title="Vorgang gesendet"></a>';
                
                $tmp_row .= '<a href="index.php?page=libs/modules/collectiveinvoice/collectiveinvoice.php&ciid='.$aRow[ $aColumns[0] ].'&exec=setState&state=3">';
                $tmp_row .= '<img class="select" src="./images/status/';
                if($aRow[ $aColumns[$i] ] == 3){
                    $tmp_row .= 'yellow.svg';
                } else {
                    $tmp_row .= 'black.svg';
                }
                $tmp_row .= '" title="Vorgang angenommen"></a>';
                
                $tmp_row .= '<a href="index.php?page=libs/modules/collectiveinvoice/collectiveinvoice.php&ciid='.$aRow[ $aColumns[0] ].'&exec=setState&state=4">';
                $tmp_row .= '<img class="select" src="./images/status/';
                if($aRow[ $aColumns[$i] ] == 4){
                    
                    $pj_state = '';
                    $pj_title = '';
                    $pj_all_closed = true;
                    $pj_all_open = true;
                    $pj_state_sql = "
                    SELECT
                    tickets.id AS tktid,
                    tickets.title,
                    tickets.number,
                    tickets_states.title as tktstate,
                    tickets_states.id as tktstateid,
                    CONCAT (`user`.user_firstname,' ',`user`.user_lastname) as user_name,
                    groups.group_name
                    FROM
                    association
                    INNER JOIN tickets ON association.objectid1 = tickets.id
                    INNER JOIN tickets_states ON tickets.state = tickets_states.id
                    LEFT JOIN `user` ON tickets.assigned_user = `user`.id
                    LEFT JOIN groups ON tickets.assigned_group = groups.id
                    WHERE association.objectid2 = {$aRow['id']} AND association.module2 = 'CollectiveInvoice' 
                    ";
                    $rResultPjState = mysql_query( $pj_state_sql, $gaSql['link'] ) or fatal_error( 'MySQL Error: ' . mysql_errno() );
                    while ($data = mysql_fetch_array($rResultPjState)){
                    $pj_title .= utf8_encode($data["tktstate"]).': '.utf8_encode($data["number"]).' - '.utf8_encode($data["title"]); //
                    if ($data["user_name"] != '')
                    $pj_title .= ' ('.utf8_encode($data["user_name"]).')';
                    else
                    $pj_title .= ' ('.utf8_encode($data["group_name"]).')';
                    $pj_title .= '
';
                    if ($data["tktstateid"] != 3)
                    $pj_all_closed = false;
                    if ($data["tktstateid"] != 2)
                    $pj_all_open = false;
                    }
                    if ($pj_all_open && $pj_all_closed == false)
                    {
                        $pj_state = 'lila.svg';
                    } elseif ($pj_all_closed && $pj_all_open == false)
                    {
                        $pj_state = 'green.svg';
                    } elseif ($pj_all_closed == false && $pj_all_open == false)
                    {
                        $pj_state = 'yellow.svg';
                    }
                    
                    if ($pj_state == '')
                        $pj_state = 'green.svg';
                        
                    $tmp_title = $pj_title;
                    $tmp_row .= $pj_state;
                } else {
                    $tmp_title = 'In Produktion';
                    $tmp_row .= 'black.svg';
                }
                $tmp_row .= '" title="'.$tmp_title.'"></a>';

                $tmp_row .= '<a href="index.php?page=libs/modules/collectiveinvoice/collectiveinvoice.php&ciid='.$aRow[ $aColumns[0] ].'&exec=setState&state=5">';
                $tmp_row .= '<img class="select" src="./images/status/';
                if($aRow[ $aColumns[$i] ] == 5){
                    $tmp_row .= 'blue.svg';
                } else {
                    $tmp_row .= 'black.svg';
                }
                $tmp_row .= '" title="Versandbereit"></a>';

                $tmp_row .= '<a href="index.php?page=libs/modules/collectiveinvoice/collectiveinvoice.php&ciid='.$aRow[ $aColumns[0] ].'&exec=setState&state=5">';
                $tmp_row .= '<img class="select" src="./images/status/';
                if($aRow[ $aColumns[$i] ] == 6){
                    $tmp_row .= 'light_blue.svg';
                } else {
                    $tmp_row .= 'black.svg';
                }
                $tmp_row .= '" title="Ware versand"></a>';

                $tmp_row .= '<a href="index.php?page=libs/modules/collectiveinvoice/collectiveinvoice.php&ciid='.$aRow[ $aColumns[0] ].'&exec=setState&state=5">';
                $tmp_row .= '<img class="select" src="./images/status/';
                if($aRow[ $aColumns[$i] ] == 7){
                    $tmp_row .= 'green.svg';
                } else {
                    $tmp_row .= 'black.svg';
                }
                $tmp_row .= '" title="Erledigt"></a>';
                
                
//                 $row[] = nl2br($tmp_row);
                $row[] = $tmp_row;
            }
        }
        $row[] = '<img class="pointer" onclick="askDel(\'index.php?page=libs/modules/collectiveinvoice/collectiveinvoice.php&exec=edit&subexec=copy&ciid='.$aRow[ $aColumns[0] ].'\')" src="images/icons/document-view.svg" title="Duplizieren">';

        $output['aaData'][] = $row;
    }
     
    echo json_encode( $output );
?>