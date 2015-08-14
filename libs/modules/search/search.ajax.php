<?php
chdir("../../../");
require_once 'libs/basic/basic.importer.php';
require_once 'libs/modules/search/search.class.php';
require_once 'libs/modules/tickets/ticket.class.php';
require_once 'libs/modules/comment/comment.class.php';
require_once 'libs/modules/abonnements/abonnement.class.php';

/*


    $search_comment = new Search(Array(
        "fields" => Array ("module", "objectid", "`comment`"),
        "matchfields" => Array("`comment`"),
        "against" => $_REQUEST["mainsearch_string"]
    )
    );
    $search_comment_matches = $search_comment->performSearch();


 */

if($_REQUEST["exec"]=="search_comments")
{
    $term = urldecode($_REQUEST["query"]);
    
    
    $search_comment = new Search(Array(
        "table" => "comments",
        "join" => " LEFT JOIN `user` ON `comments`.crtuser = `user`.id ",
        "fields" => Array ("comments.id", "comments.module", "comments.objectid", "comments.`comment`", "comments.crtdate", "CONCAT(`user`.user_firstname,\" \",`user`.user_lastname) as crtuser"),
        "matchfields" => Array("comments.`comment`"),
        "against" => $term,
        "limit" => $_REQUEST["iDisplayLength"],
        "offset" => $_REQUEST["iDisplayStart"],
        "where" => " AND state > 0 AND module != 'BusinessContact' "
    )
    );
    $search_comment_matches = $search_comment->performSearch();
    
    $output = array(
        "sEcho" => intval($_GET['sEcho']),
        "iTotalRecords" => $search_comment->getTotal(),
        "iTotalDisplayRecords" => $search_comment->getTotal(),
        "aaData" => array()
    );
    
    foreach ($search_comment_matches as $match)
    {
        $row = Array();
        $row[] = $match["id"];
        $row[] = '<span title="'.htmlspecialchars(strip_tags($match["comment"])).'">'.substr($match["comment"], 0, 50).'</span>';
        $row[] = date("d.m.Y",$match["crtdate"]);
        $row[] = $match["crtuser"];
        $row[] = $match["module"];
        $row[] = $match["score"];
        $row[] = $match["comment"];
        $row[] = 'index.php?page=libs/modules/comment/comment.show.php&id='.$match["id"];
        
        $output['aaData'][] = $row;
    }
    
    echo json_encode( $output );
}

if($_REQUEST["exec"]=="search_tickets")
{
    $term = urldecode($_REQUEST["query"]);


    $search_comment = new Search(Array(
        "table" => "tickets",
        "join" => "LEFT JOIN businesscontact ON businesscontact.id = tickets.customer
                   LEFT JOIN tickets_states ON tickets_states.id = tickets.state
                   LEFT JOIN tickets_priorities ON tickets_priorities.id = tickets.priority
                   LEFT JOIN tickets_categories ON tickets_categories.id = tickets.category
                   LEFT JOIN `user` ON `user`.id = tickets.assigned_user
                   LEFT JOIN groups ON groups.id = tickets.assigned_group
                   LEFT JOIN `user` AS user2 ON user2.id = tickets.crtuser ",
        "fields" => Array ("tickets.id", "tickets.number", "tickets_categories.title as category", "tickets_categories.id as tsid",
                           "tickets.crtdate", "tickets.duedate", "tickets.title", "tickets_states.title as state",
                           "businesscontact.name1 as customer", "tickets_priorities.title as priority_title",
                           "IF (`user`.login != '', CONCAT(`user`.user_firstname,' ',`user`.user_lastname), groups.group_name) assigned", 
                           "assigned_user", "assigned_group","CONCAT(user2.user_firstname,' ',user2.user_lastname) AS crtuser"
                          ),
        "matchfields" => Array("tickets.number", "tickets.title"),
        "against" => $term,
        "limit" => $_REQUEST["iDisplayLength"],
        "offset" => $_REQUEST["iDisplayStart"],
        "where" => " AND tickets.state > 0  "
    )
    );
    $search_comment_matches = $search_comment->performSearch();

    $output = array(
        "sEcho" => intval($_GET['sEcho']),
        "iTotalRecords" => $search_comment->getTotal(),
        "iTotalDisplayRecords" => $search_comment->getTotal(),
        "aaData" => array()
    );

    foreach ($search_comment_matches as $match)
    {
        $tc = new TicketCategory((int)$match["tsid"]);
        if ($tc->cansee())
        {
            $row = Array();
            $row[] = $match["id"];
            $row[] = $match["number"];
            $row[] = $match["category"];
            $row[] = date("d.m.Y",$match["crtdate"]);
            $row[] = $match["crtuser"];
            if ($match["duedate"] == 0)
                $row[] = "ohne";
            else
                $row[] = date("d.m.Y", $match["duedate"]);
            $row[] = $match["title"];
            $row[] = $match["state"];
            $row[] = $match["customer"];
            $row[] = $match["priority_title"];
            $row[] = $match["assigned"];
            $row[] = $match["score"];
    
            $output['aaData'][] = $row;
        }
    }

    echo json_encode( $output );
}


if($_REQUEST["exec"]=="search_notes")
{
    $term = urldecode($_REQUEST["query"]);

    $sWhere = " AND state > 0 AND module = 'BusinessContact' ";

    if ((int)$_REQUEST["access"] != 1)
        $sWhere .= " AND (visability IN (1,4) OR (visability = 3 AND `user`.id = {$_REQUEST["userid"]}))";
    else
        $sWhere .= " AND (visability IN (1,4,2) OR (visability = 3 AND `user`.id = {$_REQUEST["userid"]}))";

    $search_comment = new Search(Array(
        "table" => "comments", 
        "join" => " LEFT JOIN `user` ON `comments`.crtuser = `user`.id INNER JOIN businesscontact ON comments.objectid = businesscontact.id", 
        "fields" => Array ( "comments.id", 
                            "comments.module", 
                            "comments.objectid", 
                            "comments.title", 
                            "comments.crtdate", 
                            "CONCAT(`user`.user_firstname,\" \",`user`.user_lastname) as crtuser", 
                            "CONCAT(businesscontact.name1,\" \",businesscontact.name2) as bcon", 
                            "user.id as crtuserid", 
                            "comments.module", 
                            "comments.objectid", 
                            "comments.visability", 
                            "comments.`comment`"
                          ),
        "matchfields" => Array("comments.`comment`","comments.title"),
        "against" => $term,
        "limit" => $_REQUEST["iDisplayLength"],
        "offset" => $_REQUEST["iDisplayStart"],
        "where" => $sWhere
    )
    );
    $search_comment_matches = $search_comment->performSearch();

    $output = array(
        "sEcho" => intval($_GET['sEcho']),
        "iTotalRecords" => $search_comment->getTotal(),
        "iTotalDisplayRecords" => $search_comment->getTotal(),
        "aaData" => array()
    );

    foreach ($search_comment_matches as $match)
    {
        $row = Array();
        $row[] = $match["id"];
        $row[] = '<span title="'.htmlspecialchars(strip_tags($match["comment"])).'">'.$match["title"].'</span>';
        $row[] = date("d.m.Y",$match["crtdate"]);
        $row[] = $match["crtuser"];
        $row[] = $match["bcon"];
        $row[] = $match["score"];
        $row[] = 'index.php?page=libs/modules/comment/comment.show.php&id='.$match["id"];

        $output['aaData'][] = $row;
    }

    echo json_encode( $output );
}