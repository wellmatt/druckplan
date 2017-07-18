<?php
    error_reporting(-1);
    ini_set('display_errors', 1);
    chdir ("../../../");
    require_once 'config.php';
    require_once("libs/basic/mysql.php");
    require_once("libs/basic/globalFunctions.php");
    require_once("libs/basic/clients/client.class.php");
    require_once("libs/basic/translator/translator.class.php");
    require_once 'libs/basic/countries/country.class.php';
    require_once("libs/basic/groups/group.class.php");
    require_once("libs/modules/businesscontact/contactperson.class.php");
    require_once 'libs/modules/collectiveinvoice/collectiveinvoice.class.php';
    require_once 'libs/modules/collectiveinvoice/orderposition.class.php';
    require_once 'libs/modules/calculation/order.class.php';
    require_once 'libs/modules/calculation/calculation.class.php';
    require_once 'libs/modules/article/article.class.php';
    require_once 'libs/modules/planning/planning.job.class.php';
    require_once 'libs/modules/calculation/calculation.machineentry.class.php';
    require_once 'libs/modules/machines/machine.class.php';


    $DB = new DBMysql();
    $DB->connect($_CONFIG->db);
    global $_LANG;
    
    
    $aColumns = array( 'null', 'id', 'number', 'title', 'customer', 'deliverydate', 'comment' );
     
    /* Indexed column (used for fast and accurate table cardinality) */
    $sIndexColumn = "id";
     
    /* DB table to use */
    $sTable = "orders";
     
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
            if ( isset($_GET['bSearchable_'.$i]) && $_GET['bSearchable_'.$i] == "true" )
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
            $sWhere .= $aColumns[$i]." LIKE '%".mysqli_real_escape_string( $gaSql['link'], $_GET['sSearch_'.$i])."%' ";
        }
    }
    
    
    /*
     * SQL queries
     * Get data to display
     */
    $sQuery = "SELECT * FROM ( 
               SELECT collectiveinvoice.id,collectiveinvoice.number,collectiveinvoice.title,CONCAT(businesscontact.name1,' ',businesscontact.name2) as customer,
               collectiveinvoice.deliverydate,collectiveinvoice.`comment` 
               FROM collectiveinvoice INNER JOIN businesscontact ON collectiveinvoice.businesscontact = businesscontact.id
               WHERE `status` >= 1 AND `status` < 5 AND collectiveinvoice.needs_planning = 1) t1
               $sWhere
               $sOrder
               $sLimit";
    
//     var_dump($sQuery);
    
    $rResult = mysqli_query( $sQuery, $gaSql['link'] ) or fatal_error( 'MySQL Error: ' . mysqli_errno( $gaSql['link'] ) );
     
    /* Data set length after filtering */
//     $sQuery = "
//         SELECT FOUND_ROWS()
//     ";
    $sQuery = "
        SELECT COUNT(".$sIndexColumn.") 
        FROM ( 
               SELECT collectiveinvoice.id,collectiveinvoice.number,collectiveinvoice.title,CONCAT(businesscontact.name1,' ',businesscontact.name2) as customer,
               collectiveinvoice.deliverydate,collectiveinvoice.`comment` 
               FROM collectiveinvoice INNER JOIN businesscontact ON collectiveinvoice.businesscontact = businesscontact.id
               WHERE `status` >= 1 AND `status` < 5 AND collectiveinvoice.needs_planning = 1) t1
        $sWhere
    ";
//     var_dump($sQuery);

    $rResultFilterTotal = mysqli_query( $sQuery, $gaSql['link'] ) or fatal_error( 'MySQL Error: ' . mysqli_errno( $gaSql['link'] ) );
    $aResultFilterTotal = mysqli_fetch_array($rResultFilterTotal);
    $iFilteredTotal = $aResultFilterTotal[0];
    
     
    /* Total data set length */
    $sQuery = "
        SELECT COUNT(".$sIndexColumn.")
        FROM ( 
        SELECT collectiveinvoice.id,collectiveinvoice.number,collectiveinvoice.title,CONCAT(businesscontact.name1,' ',businesscontact.name2) as customer,
        collectiveinvoice.deliverydate,collectiveinvoice.`comment` 
        FROM collectiveinvoice INNER JOIN businesscontact ON collectiveinvoice.businesscontact = businesscontact.id
        WHERE `status` >= 1 AND `status` < 5 AND collectiveinvoice.needs_planning = 1) t1
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
//      $aColumns = array( 'id', 'number', 'title', 'customer', 'deliverydate', 'comment', 'type' );
        $row = array();
        for ( $i=0 ; $i<count($aColumns) ; $i++ )
        {
            if ( $aColumns[$i] == 'customer' )
            {
                $row[] = utf8_encode($aRow[ $aColumns[$i] ]);
            }
            else if ( $aColumns[$i] == 'null' )
            {
                $row[] = null;
            }
            else if ( $aColumns[$i] == 'number' )
            {
                $row[] = utf8_encode($aRow[ $aColumns[$i] ]);
            }
            else if ( $aColumns[$i] == 'title' )
            {
                $row[] = utf8_encode($aRow[ $aColumns[$i] ]);
            }
            else if ( $aColumns[$i] == 'comment' )
            {
                $row[] = utf8_encode($aRow[ $aColumns[$i] ]);
            }
            else if ( $aColumns[$i] == 'id' )
            {
                /* do not print the id */
                $row[] = $aRow[ $aColumns[$i] ];
            }
            else if ( $aColumns[$i] == 'deliverydate' )
            {
                $row[] = date("d.m.Y", $aRow[ $aColumns[$i] ]);
            }
        }
        $daCount = 0;
        $plCount = 0;
        
        $orderpositions = Orderposition::getAllOrderposition($aRow['id']);
        foreach ($orderpositions as $opos)
        {
            $opos_article = new Article($opos->getObjectid());
            
            
            if ($opos_article->getOrderid()>0)
            {
                $order = new Order($opos_article->getOrderid());
                $calcs = Calculation::getAllCalculations($order);
            
                foreach ($calcs as $calc)
                {
                    if ($calc->getState() && $calc->getAmount()==$opos->getQuantity())
                    {
                        $mes = Machineentry::getAllMachineentries($calc->getId());
                        foreach ($mes as $me)
                        {
            	            $opos_pjs = PlanningJob::getAllJobs(" AND object = {$opos->getCollectiveinvoice()} AND type = 2 AND opos = {$opos->getId()} AND artmach = {$me->getMachine()->getId()}");
            	            if (count($opos_pjs)>0)
            	                $plCount++;
            	            
                            $daCount++;
	                    }
	                }
	            }
	            
	            
	       } else {
	           $opos_pjs = PlanningJob::getAllJobs(" AND object = {$opos->getCollectiveinvoice()} AND type = 1 AND opos = {$opos->getId()} AND artmach = {$opos_article->getId()}");
	           if (count($opos_pjs)>0)
	               $plCount++;
	           $daCount++;
	       }
        }
        
        $row[] = $plCount.'/'.$daCount;
        $output['aaData'][] = $row;
    }
     
    echo json_encode( $output );