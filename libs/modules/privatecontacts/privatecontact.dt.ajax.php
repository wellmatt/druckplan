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
    require_once 'libs/modules/privatecontacts/privatecontact.class.php';

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
 
    $userid = (int)$_REQUEST["userid"];
    /*
     * Paging
     */
    $iDisplayStart = $_GET['iDisplayStart'];
    $iDisplayLength = $_GET['iDisplayLength'];
    /*
     * Ordering
     */
    $sDir = $_GET['sSortDir_0'];
    $sSortDir = " asc ";
    switch ($sDir)
    {
        case "desc":
            $sSortDir = " desc ";
            break;
        default:
            $sSortDir = " asc ";
            break;
    }
    
    $sSort = SORTDATE;
    $sOrder = $_GET['iSortCol_0'];
    switch ($sOrder)
    {
        case 0:
            $sSort = PrivateContact::ORDER_ID;
            break;
        case 1:
            if ($sSortDir == " desc ")
            {
                $sSort = " privatecontacts.name1 desc, privatecontacts.name2 desc ";
                $sSortDir = null;
            }
            else
            { 
                $sSort = " privatecontacts.name1 asc, privatecontacts.name2 asc ";
                $sSortDir = null;
            }
            break;
        case 2:
            $sSort = PrivateContact::ORDER_BCON;
            break;
        default:
            if ($sSortDir == " desc ")
            {
                $sSort = " privatecontacts.name1 desc, privatecontacts.name2 desc ";
                $sSortDir = null;
            }
            else
            { 
                $sSort = " privatecontacts.name1 asc, privatecontacts.name2 asc ";
                $sSortDir = null;
            }
            break;
    }
    /*
     * Filtering
     */
    $sSearch = "";
    $sSearch = $_GET['sSearch'];
    
    $prvt_contacts = PrivateContact::getAllPrivateContacts($sSort . " " . $sSortDir,"",$userid,$sSearch," LIMIT {$iDisplayStart},{$iDisplayLength} ");
    $prvt_contacts_total = PrivateContact::getAllPrivateContacts($sSort . " " . $sSortDir,"",$userid,$sSearch);

    $output = array(
        "sEcho" => intval($_GET['sEcho']),
        "iTotalRecords" => PrivateContact::countPrivateContacts(),
        "iTotalDisplayRecords" => count($prvt_contacts_total), // count($prvt_contacts)
        "aaData" => array()
    );
    
    if (count($prvt_contacts)>0)
    {
        foreach ($prvt_contacts as $pcontact)
        {
            $row = Array();
            $row[] = $pcontact->getId();
            $row[] = $pcontact->getNameAsLine();
            $row[] = $pcontact->getBusinessContact()->getNameAsLine();
            $row[] = $pcontact->getCrtuser()->getNameAsLine();
            
            $output['aaData'][] = $row;
        }
    }
     
    echo json_encode( $output );
?>