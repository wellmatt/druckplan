<?php
//     error_reporting(-1);
//     ini_set('display_errors', 1);

    chdir("../../../");
    require_once 'config.php';
    require_once 'libs/basic/basic.importer.php';
    require_once 'libs/modules/tickets/ticket.class.php';
    require_once 'libs/modules/comment/comment.class.php';

    $aColumns = array( 'id', 'number', 'category', 'crtdate', 'crtuser', 'duedate', 'title', 'state', 'customer', 'priority', 'assigned' );
     
    /* Indexed column (used for fast and accurate table cardinality) */
    $sIndexColumn = "id";
     
    /* DB table to use */
    $sTable = "tickets";
     
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
        $_GET['sSearch'] = $_GET['sSearch'];
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
    
    if ($_REQUEST["forme"]){
        $foruser = new User((int)$_REQUEST["forme"]);
        $forname = $foruser->getFirstname() . " " . $foruser->getLastname();
        $forgroups = $foruser->getGroups();
        if (count($forgroups) > 0){
            $groupsql = " OR assigned IN (";
            foreach ($forgroups as $ugroup){
                $groupsql .= "'".$ugroup->getName() . "',";
            }
            $groupsql = substr($groupsql, 0, strlen($groupsql)-1);
            $groupsql .= ") ";
        }
        if ($sWhere == ""){
            $sWhere .= " WHERE (assigned = '" . $forname . "' " . $groupsql . " OR crtuser = '" . $forname . "') ";
        } else {
            $sWhere .= " AND (assigned = '" . $forname . "' " . $groupsql . " OR crtuser = '" . $forname . "') ";
        }
    } elseif ($_REQUEST["bcid"]){
        if ($sWhere == ""){
            $sWhere .= " WHERE bcid = " . (int)$_REQUEST["bcid"];
        } else {
            $sWhere .= " AND bcid = " . (int)$_REQUEST["bcid"];
        }
        if ((int)$_REQUEST["notes_only"] == 1){
            if ($sWhere == ""){
                $sWhere .= " WHERE tcid = 1 ";
            } else {
                $sWhere .= " AND tcid = 1 ";
            }
        }
    }
    
    /*
     * SQL queries
     * Get data to display
     */ 
    $sQuery = "SELECT * FROM (SELECT
               tickets.id, tickets.number, tickets_categories.title as category, tickets_categories.id as tcid, tickets.crtdate, tickets.duedate, tickets.title, tickets_states.title as state,
               businesscontact.name1 as customer, businesscontact.id as bcid, tickets_priorities.value as priority, tickets_priorities.title as priority_title, 
               IF (`user`.login != '', CONCAT(`user`.user_firstname,' ',`user`.user_lastname), groups.group_name) assigned, 
               CONCAT(user2.user_firstname,' ',user2.user_lastname) AS crtuser 
               FROM tickets
               LEFT JOIN businesscontact ON businesscontact.id = tickets.customer
               LEFT JOIN tickets_states ON tickets_states.id = tickets.state
               LEFT JOIN tickets_priorities ON tickets_priorities.id = tickets.priority
               LEFT JOIN tickets_categories ON tickets_categories.id = tickets.category
               LEFT JOIN `user` ON `user`.id = tickets.assigned_user
               LEFT JOIN groups ON groups.id = tickets.assigned_group
               LEFT JOIN `user` AS user2 ON user2.id = tickets.crtuser 
               WHERE tickets_states.id > 1 
               $sLimit 
               ) tickets 
               $sWhere
               $sOrder";
    
//     var_dump($sQuery);
    
    $rResult = mysql_query( $sQuery, $gaSql['link'] ) or fatal_error( 'MySQL Error: ' . mysql_errno() );
     
    /* Data set length after filtering */
    $sQuery = "
        SELECT COUNT(".$sIndexColumn.")
        FROM   $sTable WHERE state > 1
    ";
//     var_dump($sQuery);
    $rResultFilterTotal = mysql_query( $sQuery, $gaSql['link'] ) or fatal_error( 'MySQL Error: ' . mysql_errno() );
    $aResultFilterTotal = mysql_fetch_array($rResultFilterTotal);
    $iFilteredTotal = $aResultFilterTotal[0];
    
     
    /* Total data set length */
    $sQuery = "
        SELECT COUNT(".$sIndexColumn.")
        FROM   $sTable WHERE state > 1
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
     
//     $aColumns = array( 'id', 'category', 'crtdate', 'duedate', 'title', 'state', 'customer', 'priority', 'assigned' );
    
    while ( $aRow = mysql_fetch_array( $rResult ) )
    {
        $row = array();
        for ( $i=0 ; $i<count($aColumns) ; $i++ )
        {
            if ( $aColumns[$i] == 'crtdate' )
            {
                $row[] = date("d.m.Y", $aRow[ $aColumns[$i] ]);
            }
            else if ( $aColumns[$i] == 'duedate' )
            {
                if (time() > $aRow[$aColumns[$i]]){
                    $row[] = date("d.m.Y", $aRow[ $aColumns[$i] ])."<img src='images/icons/exclamation--frame.png'>";
                } else {
                    $row[] = date("d.m.Y", $aRow[ $aColumns[$i] ]);
                }
            }
            else if ( $aColumns[$i] == 'state' )
            {
                $latestcomments = Comment::getLatestCommentsForObject("Ticket",$aRow['id']);
                $commenthtml = "<img src='images/icons/message_inbox.gif' title='";
                foreach ($latestcomments as $comment){
                    if ($_USER->isAdmin()
                        || $comment->getVisability() == Comment::VISABILITY_PUBLIC
                        || $comment->getVisability() == Comment::VISABILITY_INTERNAL
                        || $comment->getCrtuser() == $_USER)
                    {
                        $commenthtml .= date("d.m.Y H:i",$comment->getCrtdate()) . " (".$comment->getCrtuser()->getNameAsLine()."): " . $comment->getComment();
                    }
                }
                $commenthtml .= "'>";
                $row[] = nl2br(htmlentities(utf8_encode($aRow[ $aColumns[$i] ]))).$commenthtml;
            }
            else if ( $aColumns[$i] == 'priority' )
            {
                /* do not print the id */
                $row[] = nl2br(htmlentities(utf8_encode($aRow['priority_title'])));
            }
            else if ( $aColumns[$i] == 'id' )
            {
                /* do not print the id */
                $row[] = $aRow[ $aColumns[$i] ];
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