<?php

    require_once '../../../config.php';

    $aColumns = array( 'id', 'art_picture', 'title', 'number', 'article_tags', 'tradegroup_title', 'shop_customer', 'shoprel' );
     
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
            if ( isset($_GET['bSearchable_'.$i]) && $_GET['bSearchable_'.$i] == "true" && $aColumns[$i] != "id" && $aColumns[$i] != "shop_customer" && $aColumns[$i] != "art_picture" && $aColumns[$i] != "article_tags" )
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
            $tag_where .= " OR tag LIKE '".utf8_decode($tag)."' ";
        }
        $sTagArticles = Array();
        $tQuery = "SELECT article, count(article) as count FROM article_tags WHERE 1=2 {$tag_where} GROUP BY article";
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
    
    if ($_REQUEST["tradegroup"] > 0)
    {
        $selected_tgs = Array();
        $selected_tgs[] = $_REQUEST["tradegroup"];
        $tg_sql = "SELECT id FROM tradegroup WHERE tradegroup_parentid = {$_REQUEST["tradegroup"]}";

        $rResulttg = mysql_query( $tg_sql, $gaSql['link'] ) or fatal_error( 'MySQL Error: ' . mysql_errno() );
        while ($stg_row = mysql_fetch_array($rResulttg))
        {
            $selected_tgs[] = $stg_row["id"];
        }
        $tgs = implode(",", $selected_tgs);
        $sWhere .= " AND tradegroup.id IN ({$tgs}) ";
    }
    
    if ($_REQUEST["bc"] || $_REQUEST["cp"])
    {
        if ($_REQUEST["bc"] > 0)
            $bccp_sql = "SELECT article FROM `article_shop_approval` WHERE bc = {$_REQUEST["bc"]}";
        if ($_REQUEST["cp"] > 0)
            $bccp_sql = "SELECT article FROM `article_shop_approval` WHERE bc = {$_REQUEST["bc"]}";
        
        $bccp_articles = Array();
        $rResultbccp = mysql_query( $bccp_sql, $gaSql['link'] ) or fatal_error( 'MySQL Error: ' . mysql_errno() );
        while ($sbccp_row = mysql_fetch_array($rResultbccp))
        {
            $bccp_articles[] = $sbccp_row["article"];
        }
        if (count($bccp_articles)>0)
        {
            $bccp_articles = implode(",", $bccp_articles);
            $sWhere .= " AND article.id IN ({$bccp_articles}) ";
            $aWhere = " AND article.id IN ({$bccp_articles}) ";
        } else {
            $sWhere .= " AND 1=2 ";
        }
    }

    $cfwhere = "";
    foreach(array_keys($_REQUEST) as $key)
    {
        if (preg_match("/cfield_(?P<id>\d+)/", $key, $m)) {
            $fieldid = str_replace("cfield_","",$key);
            $value = $_REQUEST[$key];
            $cfwhere .= " AND (custom_fields.id = {$fieldid} AND custom_fields_values.`value` = {$value}) ";
        }
    }
    $sWhere .= $cfwhere;
    
    /*
     * SQL queries
     * Get data to display
     */
    $sQuery = "SELECT DISTINCT article.id, '' as art_picture, article.title, article.number, tradegroup.tradegroup_title, article.shoprel
               FROM article 
               LEFT OUTER JOIN tradegroup ON tradegroup.id = article.tradegroup
               LEFT OUTER JOIN custom_fields_values ON article.id = custom_fields_values.object
               LEFT OUTER JOIN custom_fields ON custom_fields_values.field = custom_fields.id
               $sWhere
               $sOrder
               $sLimit
               ";
    
//     var_dump($sQuery);
    
    $rResult = mysql_query( $sQuery, $gaSql['link'] ) or fatal_error( 'MySQL Error: ' . mysql_errno() );
     
    /* Data set length after filtering */
    $sQuery = " SELECT COUNT(id)
                FROM
                (
                SELECT DISTINCT article.id
                FROM article
                LEFT OUTER JOIN tradegroup ON tradegroup.id = article.tradegroup
                LEFT OUTER JOIN custom_fields_values ON article.id = custom_fields_values.object
                LEFT OUTER JOIN custom_fields ON custom_fields_values.field = custom_fields.id
                $sWhere
                ) t1
    ";
//     var_dump($sQuery);
    $rResultFilterTotal = mysql_query( $sQuery, $gaSql['link'] ) or fatal_error( 'MySQL Error: ' . mysql_errno() );
    $aResultFilterTotal = mysql_fetch_array($rResultFilterTotal);
    $iFilteredTotal = $aResultFilterTotal[0];
    
     
    /* Total data set length */
    $sQuery = " SELECT COUNT(id)
                FROM
                (
                SELECT DISTINCT article.id
                FROM article
                LEFT OUTER JOIN tradegroup ON tradegroup.id = article.tradegroup
                LEFT OUTER JOIN custom_fields_values ON article.id = custom_fields_values.object
                LEFT OUTER JOIN custom_fields ON custom_fields_values.field = custom_fields.id
                WHERE article.status = 1 {$aWhere}
                ) t1
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
            else if ( $aColumns[$i] == 'shop_customer' )
            {
                $shop_sql = "SELECT
                             article_shop_approval.id,
                             article_shop_approval.article,
                             article_shop_approval.bc,
                             article_shop_approval.cp,
                             CONCAT(contactperson.name1,' ',contactperson.name2) as cp_name,
                             CONCAT(businesscontact.name1,' ',businesscontact.name2) as bc_name
                             FROM
                             article_shop_approval
                             LEFT JOIN contactperson ON article_shop_approval.cp = contactperson.id
                             LEFT JOIN businesscontact ON article_shop_approval.bc = businesscontact.id
                             WHERE article = {$aRow[ $aColumns[0] ]}";

                $rResultShop = mysql_query( $shop_sql, $gaSql['link'] ) or fatal_error( 'MySQL Error: ' . mysql_errno() );
                $shop_rows_bc = array();
                $shop_rows_cp = array();
                while ($shop_row = mysql_fetch_array($rResultShop))
                {
                    if ((int)$shop_row['bc']>0)
                        $shop_rows_bc[] = $shop_row['bc_name'];
                    else if ((int)$shop_row['cp']>0)
                        $shop_rows_cp[] = $shop_row['cp_name'];
                }
                if(count($shop_rows_bc) > 0 || count($shop_rows_cp) > 0){
                    $shoptitle = "";
                    if(count($shop_rows_bc) > 0)
                    {
                        $shoptitle = 'Geschäftskontakte:&#10;';
                        foreach ($shop_rows_bc as $shop_bc)
                        {
                            $shoptitle .= $shop_bc . "&#10;";
                        } 
                    }
                    if(count($shop_rows_cp) > 0)
                    {
                        $shoptitle = 'Ansprechpartner:&#10;';
                        foreach ($shop_rows_cp as $shop_cp)
                        {
                            $shoptitle .= $shop_cp . "&#10;";
                        } 
                    }
                    $shoptitle = utf8_encode($shoptitle);
                    $shopstr = '<img src="images/status/green_small.gif" title="'.$shoptitle.'">';
                    $row[] = $shopstr;
                } else {
                    if ($aRow['shoprel'] == 1){
                        $row[] = '<img src="images/status/green_small.gif" title="Freigabe für Alle">';
                    } else {
                        $row[] = '<img src="images/status/red_small.gif">';
                    }
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
                    $row[] = nl2br(htmlentities(utf8_encode($tags)));
                } else {
                    $row[] = '';
                }
            }
            else if ( $aColumns[$i] == 'shoprel' )
            {
                /* do not print the shoprel */
            }
            else if ( $aColumns[$i] == 'id' )
            {
                $row[] = $aRow[ $aColumns[$i] ];
            }
            else if ( $aColumns[$i] == 'article.matchcode' )
            {
                $row[] = nl2br(htmlentities(utf8_encode($aRow['matchcode'])));
            }
            else
            {
                /* General output */
                $row[] = nl2br(htmlentities(utf8_encode($aRow[ $aColumns[$i] ])));
            }
        }
        $row[] = '<a class="icon-link" href="index.php?page=libs/modules/article/article.php&exec=edit&aid='.$aRow[ $aColumns[0] ].'"><span class="glyphicons glyphicons-pencil" title="Bearbeiten"></span> </a>
        		  <a class="icon-link" href="index.php?page=libs/modules/article/article.php&exec=copy&aid='.$aRow[ $aColumns[0] ].'"><span class="glyphicons glyphicons-copy"></span></a>
        		  <a class="icon-link" href="#"	onclick="askDel(\'index.php?page=libs/modules/article/article.php&exec=delete&did='.$aRow[ $aColumns[0] ].'\')">
        		  <span  style="color: red;" class="glyphicons glyphicons-remove" title="L&ouml;schen"></span></a>';

        $output['aaData'][] = $row;
    }
     
    echo json_encode( $output, JSON_UNESCAPED_SLASHES );
?>