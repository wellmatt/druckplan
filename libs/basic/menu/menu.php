<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       12.06.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
require_once("menu.class.php");

function printSubTree($tree, $i = 1)
{
    foreach($tree as $t)
    {
        echo '<li class="dropdown-short nav_top">';
        
        echo '<a class="dropdown-toggle" href="javascript:;" data-toggle="dropdown">'.$t->getName().'<span class="caret"></span></a>';

        echo '<ul class="dropdown-menu" style="margin-top: 1px; margin-bottom: 1px; top: 100%; bottom: auto; border-top-width: 0px; border-bottom-width: 1px; border-radius: 0px 0px 4px 4px; padding: 15px; left: auto;">';
        echo '<li class="h-divider">';
        
        foreach ($t->getChilds() as $c)
        {
            if (count($c->getChilds()) > 0){
                echo '<li class="dropdown-right-onhover no-fix" style="position: relative;"><!-- Menu item with submenu -->';
                echo '<a class="dropdown-toggle collapsed" data-target="#subid_'.trim($c->getPid()).'" data-toggle="collapse"><i class="fa fa-bars"></i> '.$c->getName().'</a>';
                echo '<!-- start submenu --><ul id="subid_'.trim($c->getPid()).'" class="dropdown-menu collapse" style="margin: 0px 0px 0px -1px; padding: 15px; left: 100%; right: auto; top: 0px; bottom: auto; z-index: 1001; overflow: visible;">';
//                 echo '<li class="dropdown-header">'.$c->getName().'</li>';
                print2ndLevel($c->getChilds(), $i+1);
                echo '</ul><!-- end submenu --></li>';
            } else {
                printChildTree(Array($c), $i+1);
            }
        }
        
        echo '</li></ul></li>';
        
//         echo '<!-- divider -->';
//         echo '<li class="divider"></li>';
    }
}

function print2ndLevel($tree, $i = 1)
{
    print_r($t);
    foreach($tree as $t)
    {
        echo '<li><a class="" href="#" onclick="document.location=\'index.php?page='.$t->getPath().'\'"><img src="'.$t->getIcon().'"> '.$t->getName().'</a></li>';
    }
}

function printChildTree($tree, $i = 1)
{
    foreach($tree as $t)
    {
        if ($t->getName() == "Planungstafel"){
            echo '<a class="" href="'.$t->getPath().'" target="_blank"><img src="'.$t->getIcon().'"> '.$t->getName().'</a>';
        } else {
            echo '<a class="" href="#" onclick="document.location=\'index.php?page='.$t->getPath().'\'"><img src="'.$t->getIcon().'"> '.$t->getName().'</a>';
        }
        
    }
}

$_MENU = new Menu();

printSubTree($_MENU->getElements());

?>