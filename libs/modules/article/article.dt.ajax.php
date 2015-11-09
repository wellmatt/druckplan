<?php

    require_once '../../../config.php';

    $aColumns = array( 'id', 'art_picture', 'title', 'number', 'article_tags', 'tradegroup_title', 'customer', 'shop_customer_rel' );
     
    /* Indexed column (used for fast and accurate table cardinality) */
    $sIndexColumn = "id";
     
    /* DB table to use */
    $sTable = "article";
     
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
            if ( isset($_GET['bSearchable_'.$i]) && $_GET['bSearchable_'.$i] == "true" && $aColumns[$i] != "id" && $aColumns[$i] != "art_picture" && $aColumns[$i] != "article_tags" )
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
        $sWhere = " WHERE status = 1 ";
    }
    else
    {
        $sWhere .= " AND status = 1 ";
    }
    
    if ($_REQUEST["search_tags"])
    {
        $tags = explode(",", $_REQUEST["search_tags"]);
        $tag_where = "";
        foreach ($tags as $tag)
        {
            $tag_where .= " OR tag LIKE '{$tag}' ";
        }
        $sTagArticles = Array();
        $tQuery = "SELECT article, count(article) as count FROM article_tags WHERE 1=2 {$tag_where} GROUP BY article";
//         echo $tQuery . "</br>";
        $rResultStags = mysql_query( $tQuery, $gaSql['link'] ) or fatal_error( 'MySQL Error: ' . mysql_errno() );
        while ($stag_row = mysql_fetch_array($rResultStags))
        {
            if ($stag_row["count"] >= count($tags))
                $sTagArticles[] = $stag_row["article"];
        }
        if (count($sTagArticles)>0)
        {
            $sTagArticles = implode(",", $sTagArticles);
            $sWhere .= " AND article.id IN ({$sTagArticles}) ";
        } else
            $sWhere .= " AND 1=2 ";
    }
    
    
    /*
     * SQL queries
     * Get data to display
     */
    $sQuery = "SELECT article.id, '' as art_picture, article.title, article.number, tradegroup.tradegroup_title, article.shop_customer_rel, CONCAT(businesscontact.name1,' ',businesscontact.name2) as customer
               FROM article LEFT JOIN article_pictures ON article_pictures.articleid = article.id LEFT JOIN tradegroup ON tradegroup.id = article.tradegroup
               LEFT JOIN businesscontact ON article.shop_customer_id = businesscontact.id
               $sWhere
               $sOrder
               $sLimit
               ";
    
//     var_dump($sQuery);
    
    $rResult = mysql_query( $sQuery, $gaSql['link'] ) or fatal_error( 'MySQL Error: ' . mysql_errno() );
     
    /* Data set length after filtering */
    $sQuery = "
        SELECT COUNT(article.id)
               FROM article 
               LEFT JOIN article_pictures ON article_pictures.articleid = article.id 
               LEFT JOIN tradegroup ON tradegroup.id = article.tradegroup
               LEFT JOIN businesscontact ON article.shop_customer_id = businesscontact.id 
        $sWhere
    ";
//     var_dump($sQuery);
    $rResultFilterTotal = mysql_query( $sQuery, $gaSql['link'] ) or fatal_error( 'MySQL Error: ' . mysql_errno() );
    $aResultFilterTotal = mysql_fetch_array($rResultFilterTotal);
    $iFilteredTotal = $aResultFilterTotal[0];
    
     
    /* Total data set length */
    $sQuery = "
        SELECT COUNT(article.id) 
        FROM article 
        LEFT JOIN article_pictures ON article_pictures.articleid = article.id 
        LEFT JOIN tradegroup ON tradegroup.id = article.tradegroup 
        LEFT JOIN businesscontact ON article.shop_customer_id = businesscontact.id  
        WHERE status = 1
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
//         echo "Durchlauf f�r :" . $aRow[ $aColumns[0] ] . "</br> </br>";
        $row = array();
        for ( $i=0 ; $i<count($aColumns) ; $i++ )
        {
            if ( $aColumns[$i] == 'art_picture' )
            {
                $pic_sql = "SELECT url FROM article_pictures WHERE articleid = {$aRow[ $aColumns[0] ]} LIMIT 1";
				
				$rResultPics = mysql_query( $pic_sql, $gaSql['link'] ) or fatal_error( 'MySQL Error: ' . mysql_errno() );
				$aResultPics = mysql_fetch_array($rResultPics);
				
                if(count($aResultPics) > 0){
                    $row[] = '<a href="images/products/'.$aResultPics[0].'" target="_blank">
                              <img src="images/products/'.$aResultPics[0].'" style="max-width: 100px; max-height: 100px;" width="100px"></a>';
                } else {
                    $row[] = '<img src="images/icons/image.png" title="Kein Bild hinterlegt">&nbsp;';
                }
            }
            else if ( $aColumns[$i] == 'article_tags' )
            {
                $tag_sql = "SELECT DISTINCT tag FROM article_tags WHERE article = {$aRow[ $aColumns[0] ]}";
				
				$rResultTags = mysql_query( $tag_sql, $gaSql['link'] ) or fatal_error( 'MySQL Error: ' . mysql_errno() );
				$tag_rows = array();
                while ($tag_row = mysql_fetch_array($rResultTags))
                {
                    $tag_rows[] = $tag_row["tag"];
                }
                if(count($tag_rows) > 0){
                    $tags = implode("; ", $tag_rows);
                    $row[] = $tags;
                } else {
                    $row[] = '';
                }
            }
            else if ( $aColumns[$i] == 'id' )
            {
                /* do not print the id */
                $row[] = $aRow[ $aColumns[$i] ];
            }
            else if ( $aColumns[$i] == 'shop_customer_rel' )
            {
                if($_CONFIG->shopActivation){
                    if ($aRow[$aColumns[$i]] == 1){
                        $row[] = '<img src="images/status/green_small.gif">';
                    } else {
                        $row[] = '<img src="images/status/red_small.gif">';
                    }
                }
            }
            else if ( $aColumns[$i] == 'article.matchcode' )
            {
                /* do not print the id */
                $row[] = nl2br(htmlentities(utf8_encode($aRow['matchcode'])));
            }
            else
            {
                /* General output */
                $row[] = nl2br(htmlentities(utf8_encode($aRow[ $aColumns[$i] ])));
            }
        }
        $row[] = '<a class="icon-link" href="index.php?page=libs/modules/article/article.php&exec=edit&aid='.$aRow[ $aColumns[0] ].'"><img src="images/icons/pencil.png" title="Bearbeiten"></a>
        		  <a class="icon-link" href="index.php?page=libs/modules/article/article.php&exec=copy&aid='.$aRow[ $aColumns[0] ].'"><img src="images/icons/scripts.png" title="Kopieren"></a>
        		  <a class="icon-link" href="#"	onclick="askDel(\'index.php?page=libs/modules/article/article.php&exec=delete&did='.$aRow[ $aColumns[0] ].'\')">
        		  <img src="images/icons/cross-script.png" title="L&ouml;schen"></a>';
// 		var_dump($row); echo "</br>";
        $output['aaData'][] = $row;
    }
    
//     var_dump($output); echo "</br>";
     
    echo json_encode( $output );
    
//     echo  json_last_error();
?>