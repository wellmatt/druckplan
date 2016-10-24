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
    require_once 'libs/basic/cachehandler/cachehandler.class.php';
    require_once 'thirdparty/phpfastcache/phpfastcache.php';
    require_once 'libs/modules/organizer/contact.class.php';
    require_once 'libs/modules/businesscontact/businesscontact.class.php';
    require_once 'libs/modules/chat/chat.class.php';
    require_once 'libs/modules/calculation/order.class.php';
    require_once 'libs/modules/tickets/ticket.class.php';
    require_once 'libs/modules/comment/comment.class.php';
    require_once 'libs/modules/abonnements/abonnement.class.php';

//     error_reporting(-1);
//     ini_set('display_errors', 1);
    
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
    
    if ($_REQUEST["details"] == "1")
        $aColumns = array( 'null', 'id', 'number', 'category', 'crtdate', 'crtuser', 'duedate', 'title', 'state', 'customer', 'priority', 'assigned' );
    else
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
            $sWhere .= " WHERE (assigned = '" . $forname . "' " . $groupsql . " OR crtuser = '" . $forname . "' ) ";
        } else {
            $sWhere .= " AND (assigned = '" . $forname . "' " . $groupsql . " OR crtuser = '" . $forname . "' ) ";
        }
    } elseif ($_REQUEST["formeabo"]){
        $foruser = new User((int)$_REQUEST["formeabo"]);
        $abonnoments = Abonnement::getMyTicketAbonnementsForDtList();
        if ($abonnoments != ""){
            $abonnoments = " AND id IN (" . $abonnoments . ") ";
        }
        
        $forname = $foruser->getFirstname() . " " . $foruser->getLastname();
        $forgroups = $foruser->getGroups();
        if (count($forgroups) > 0){
            $groupsql = " AND assigned NOT IN (";
            foreach ($forgroups as $ugroup){
                $groupsql .= "'".$ugroup->getName() . "',";
            }
            $groupsql = substr($groupsql, 0, strlen($groupsql)-1);
            $groupsql .= ") ";
        }
        if ($sWhere == ""){
            $sWhere .= " WHERE (assigned != '" . $forname . "' " . $groupsql . " AND crtuser != '" . $forname . "' ".$abonnoments.") ";
        } else {
            $sWhere .= " AND (assigned != '" . $forname . "' " . $groupsql . " AND crtuser != '" . $forname . "' ".$abonnoments.") ";
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

    if ($_GET['start_due'] != ""){
        if ($sWhere == ""){
            $sWhere .= " WHERE duedate >= {$_GET['start_due']} ";
        } else {
            $sWhere .= " AND duedate >= {$_GET['start_due']} ";
        }
    }
    if ($_GET['end_due'] != ""){
        if ($sWhere == ""){
            $sWhere .= " WHERE duedate <= {$_GET['end_due']} ";
        } else {
            $sWhere .= " AND duedate <= {$_GET['end_due']} ";
        }
    }
    
    if ($_GET['category'] != ""){
        if ($sWhere == ""){
            $sWhere .= " WHERE tcid = {$_GET['category']} ";
        } else {
            $sWhere .= " AND tcid = {$_GET['category']} ";
        }
    }
    
    if ($_GET['state'] != ""){
        if ($sWhere == ""){
            $sWhere .= " WHERE tsid = {$_GET['state']} ";
        } else {
            $sWhere .= " AND tsid = {$_GET['state']} ";
        }
    }
    
    if ((int)$_GET['showclosed'] == 0){
        if ($sWhere == ""){
            $sWhere .= " WHERE tsid != 3 ";
        } else {
            $sWhere .= " AND tsid != 3 ";
        }
    } else {
        if ($sWhere == ""){
            $sWhere .= " WHERE tsid = 3 ";
        } else {
            $sWhere .= " AND tsid = 3 ";
        }
    }
    
    if ($_GET['crtuser'] != ""){
        if ($sWhere == ""){
            $sWhere .= " WHERE crtuserid = {$_GET['crtuser']} ";
        } else {
            $sWhere .= " AND crtuserid = {$_GET['crtuser']} ";
        }
    }
    
    if ($_GET['assigned'] != ""){
        
        if (substr($_GET['assigned'], 0, 2) == "u_"){
            $assigned = (int)substr($_GET['assigned'], 2);
            if ($sWhere == ""){
                $sWhere .= " WHERE assigned_user = {$assigned} ";
            } else {
                $sWhere .= " AND assigned_user = {$assigned} ";
            }
        } elseif (substr($_GET['assigned'], 0, 2) == "g_") {
            $assigned = (int)substr($_GET['assigned'], 2);
            if ($sWhere == ""){
                $sWhere .= " WHERE assigned_group = {$assigned} ";
            } else {
                $sWhere .= " AND assigned_group = {$assigned} ";
            }
        }
        
    }
    
    if ($_GET['tourmarker'] != ""){
        if ($sWhere == ""){
            $sWhere .= " WHERE tourmarker LIKE '%{$_GET['tourmarker']}%' ";
        } else {
            $sWhere .= " AND tourmarker LIKE '%{$_GET['tourmarker']}%' ";
        }
    }
    
    if ($_GET['cl_start'] != ""){
        if ($sWhere == ""){
            $sWhere .= " WHERE closedate >= {$_GET['cl_start']} ";
        } else {
            $sWhere .= " AND closedate >= {$_GET['cl_start']} ";
        }
    }
    if ($_GET['cl_end'] != ""){
        if ($sWhere == ""){
            $sWhere .= " WHERE closedate <= {$_GET['cl_end']} ";
        } else {
            $sWhere .= " AND closedate <= {$_GET['cl_end']} ";
        }
    }
    
    if ((int)$_GET['showdeleted'] == 1){
        if ($sWhere == ""){
            $sWhere .= " WHERE tsid = 1 ";
        } else {
            $sWhere .= " AND tsid = 1 ";
        }
    } else {
        $nodeleted = " WHERE tickets_states.id > 1 ";
        if ($sWhere == ""){
            $sWhere .= " WHERE tsid > 1 ";
        } else {
            $sWhere .= " AND tsid > 1 ";
        }
    }
    
    if ((int)$_GET['withoutdue'] != 1){
        if ($sWhere == ""){
            $sWhere .= " WHERE duedate > 0 ";
        } else {
            $sWhere .= " AND duedate > 0 ";
        }
    }
    
    /*
     * SQL queries
     * Get data to display
     */ 
    $sQuery = "SELECT * FROM (SELECT
               tickets.id, tickets.number, tickets_categories.title as category, tickets_categories.id as tcid, tickets.closedate, tickets.crtdate, tickets.duedate, tickets.title, tickets_states.title as state,
               tickets.state as tsid, businesscontact.name1 as customer, businesscontact.id as bcid, tickets_priorities.value as priority, tickets_priorities.title as priority_title, 
               IF (`user`.login != '', CONCAT(`user`.user_firstname,' ',`user`.user_lastname), groups.group_name) assigned, assigned_user, assigned_group, 
               CONCAT(user2.user_firstname,' ',user2.user_lastname) AS crtuser, tickets.crtuser as crtuserid, businesscontact.tourmarker 
               FROM tickets
               LEFT JOIN businesscontact ON businesscontact.id = tickets.customer
               LEFT JOIN tickets_states ON tickets_states.id = tickets.state
               LEFT JOIN tickets_priorities ON tickets_priorities.id = tickets.priority
               LEFT JOIN tickets_categories ON tickets_categories.id = tickets.category
               LEFT JOIN `user` ON `user`.id = tickets.assigned_user
               LEFT JOIN groups ON groups.id = tickets.assigned_group
               LEFT JOIN `user` AS user2 ON user2.id = tickets.crtuser 
               $nodeleted
               ) tickets 
               $sWhere
               $sOrder 
               $sLimit ";
    
//     var_dump($sQuery);
    
    $rResult = mysql_query( $sQuery, $gaSql['link'] ) or fatal_error( 'MySQL Error: ' . mysql_errno() );
     
    /* Data set length after filtering */
//     $sQuery = "
//         SELECT COUNT(".$sIndexColumn.")
//         FROM   $sTable WHERE state > 1
//     ";
    $sQuery = "
        SELECT COUNT(".$sIndexColumn.") FROM (SELECT
               tickets.id, tickets.number, tickets_categories.title as category, tickets_categories.id as tcid, tickets.closedate, tickets.crtdate, tickets.duedate, tickets.title, tickets_states.title as state,
               tickets.state as tsid, businesscontact.name1 as customer, businesscontact.id as bcid, tickets_priorities.value as priority, tickets_priorities.title as priority_title, 
               IF (`user`.login != '', CONCAT(`user`.user_firstname,' ',`user`.user_lastname), groups.group_name) assigned, assigned_user, assigned_group, 
               CONCAT(user2.user_firstname,' ',user2.user_lastname) AS crtuser, tickets.crtuser as crtuserid, businesscontact.tourmarker 
               FROM tickets
               LEFT JOIN businesscontact ON businesscontact.id = tickets.customer
               LEFT JOIN tickets_states ON tickets_states.id = tickets.state
               LEFT JOIN tickets_priorities ON tickets_priorities.id = tickets.priority
               LEFT JOIN tickets_categories ON tickets_categories.id = tickets.category
               LEFT JOIN `user` ON `user`.id = tickets.assigned_user
               LEFT JOIN groups ON groups.id = tickets.assigned_group
               LEFT JOIN `user` AS user2 ON user2.id = tickets.crtuser 
               $nodeleted
               ) tickets 
               $sWhere
    ";
    
//     var_dump($sQuery);
    $rResultFilterTotal = mysql_query( $sQuery, $gaSql['link'] ) or fatal_error( 'MySQL Error: ' . mysql_errno() );
    $aResultFilterTotal = mysql_fetch_array($rResultFilterTotal);
    $iFilteredTotal = $aResultFilterTotal[0];
    
     
    /* Total data set length */
    $sQuery = "
        SELECT COUNT(".$sIndexColumn.")
        FROM (SELECT
               tickets.id, tickets.number, tickets_categories.title as category, tickets_categories.id as tcid, tickets.closedate, tickets.crtdate, tickets.duedate, tickets.title, tickets_states.title as state,
               tickets.state as tsid, businesscontact.name1 as customer, businesscontact.id as bcid, tickets_priorities.value as priority, tickets_priorities.title as priority_title, 
               IF (`user`.login != '', CONCAT(`user`.user_firstname,' ',`user`.user_lastname), groups.group_name) assigned, assigned_user, assigned_group, 
               CONCAT(user2.user_firstname,' ',user2.user_lastname) AS crtuser, tickets.crtuser as crtuserid, businesscontact.tourmarker 
               FROM tickets
               LEFT JOIN businesscontact ON businesscontact.id = tickets.customer
               LEFT JOIN tickets_states ON tickets_states.id = tickets.state
               LEFT JOIN tickets_priorities ON tickets_priorities.id = tickets.priority
               LEFT JOIN tickets_categories ON tickets_categories.id = tickets.category
               LEFT JOIN `user` ON `user`.id = tickets.assigned_user
               LEFT JOIN groups ON groups.id = tickets.assigned_group
               LEFT JOIN `user` AS user2 ON user2.id = tickets.crtuser 
               $nodeleted
               ) tickets 
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
    
    if ($_REQUEST["cpid"]){
        $contactperson = new ContactPerson((int)$_REQUEST["cpid"]);
    } else {
        $contactperson = new ContactPerson();
    }
    
    while ( $aRow = mysql_fetch_array( $rResult ) )
    {
        $row = array();
        $tc = new TicketCategory((int)$aRow['tcid']);
        if (($tc->cansee() && $contactperson->getId()==0) || $_REQUEST["forme"] || ($contactperson->getId()>0 && $contactperson->TC_cansee($tc))){
            for ( $i=0 ; $i<count($aColumns) ; $i++ )
            {
                if ( $aColumns[$i] == 'crtdate' )
                {
                    $row[] = date("d.m.Y", $aRow[ $aColumns[$i] ]);
                }
                else if ( $aColumns[$i] == 'duedate' )
                {
                    if (time() > $aRow[$aColumns[$i]] && $aRow[$aColumns[$i]] > 0){
                        if ($_REQUEST["portal"] == 1){
                            $row[] = date("d.m.Y", $aRow[ $aColumns[$i] ])."<span class=\"glyphicons glyphicons-exclamation-sign\"></span>";
                        } else {
                            $row[] = date("d.m.Y", $aRow[ $aColumns[$i] ])."<span class=\"glyphicons glyphicons-exclamation-sign\"></span>";
                        }
                    } else if ($aRow[$aColumns[$i]] == 0) {
                        $row[] = "ohne";
                    } else {
                        $row[] = date("d.m.Y", $aRow[ $aColumns[$i] ]);
                    }
                }
                else if ( $aColumns[$i] == 'state' )
                {
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
                                    INNER JOIN tickets ON association.objectid2 = tickets.id
                                    INNER JOIN tickets_states ON tickets.state = tickets_states.id
                                    LEFT JOIN `user` ON tickets.assigned_user = `user`.id
                                    LEFT JOIN groups ON tickets.assigned_group = groups.id
                                    WHERE association.objectid1 = {$aRow['id']}
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
                        $pj_state = '<img src="images/status/red_small.svg" title=\''.$pj_title.'\'/>';
                    } elseif ($pj_all_closed && $pj_all_open == false)
                    {
                        $pj_state = '<img src="images/status/green_small.svg" title=\''.$pj_title.'\'/>';
                    } elseif ($pj_all_closed == false && $pj_all_open == false)
                    {
                        $pj_state = '<img src="images/status/yellow_small.svg" title=\''.$pj_title.'\'/>';
                    }

                    if ($pj_state == '')
                        $pj_state = '<img src="images/status/green_small.svg" title="Keine Verkn."/>';
                    
                    $commenthtml = "<span class=\"glyphicons glyphicons-inbox-in pointer commentimg\"></span>";
                    
                    $row[] = nl2br(htmlentities(utf8_encode($aRow[ $aColumns[$i] ])))." ".$pj_state." ".$commenthtml;
                }
                else if ( $aColumns[$i] == 'priority' )
                {
                    $row[] = nl2br(htmlentities(utf8_encode($aRow['priority_title'])));
                }
                else if ( $aColumns[$i] == 'null' )
                {
                }
                else if ( $aColumns[$i] == 'id' )
                {
                    if ($_REQUEST["details"] == "1")
                        $row[] = "";
                    $row[] = $aRow[ $aColumns[$i] ];
                }
                else if ( $aColumns[$i] == 'closedate' )
                {
                    /* do not print */
                }
                else if ( $aColumns[$i] == 'tourmarker' )
                {
                    /* do not print */
                }
                else
                {
                    /* General output */
                    $row[] = nl2br(htmlentities(utf8_encode($aRow[ $aColumns[$i] ])));
                }
            }
            $output['aaData'][] = $row;
        }
    }
     
    echo json_encode( $output );
?>