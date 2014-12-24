<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       08.03.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
require_once 'contact.class.php';
require_once 'libs/modules/businesscontact/businesscontact.class.php';

//----------------------------------------------------------------------------------
function cmpByName($a, $b)
{
    if(is_a($a, "User"))
        $name1 = $a->getFirstname()." ".$a->getLastname();
    else 
        $name1 = $a->getNameAsLine();
    
    if(is_a($b, "User"))
        $name2 = $b->getFirstname()." ".$b->getLastname();
    else 
        $name2 = $b->getNameAsLine();
    
    if(strtolower($name1) == strtolower($name2))
        return 0;
    return (strtolower($name1) < strtolower($name2)) ? -1 : 1;
}

function filter(&$array, $selchar)
{
    // Wir filtern nach Buchstaben
    if($selchar >= 'A' && $selchar <= 'Z' || $selchar >= 'a' && $selchar <= 'z')
    {        
        foreach ($array as $i => $a)
        {
            if(is_a($a, "User")){
                if(substr(strtoupper($a->getFirstname()), 0, 1) != $selchar)
                    unset($array[$i]);
            } elseif(is_a($a, "Contactperson")) {
            	if(substr(strtoupper($a->getNameAsLine2()), 0, 1) != $selchar)
            		unset($array[$i]);
            } else {
                if(substr(strtoupper($a->getNameAsLine()), 0, 1) != $selchar)
                    unset($array[$i]);
            }
        }
    } else if($selchar == "0-9")
    {
        $preg = "/^[0-9]/";
        foreach($array as $i => $a)
        {
            if(is_a($a, "User"))
            {
                if(!preg_match($preg, substr($a->getFirstname(), 0, 1)))
                    unset($array[$i]);
            } else
            {
                if(!preg_match($preg, substr($a->getNameAsLine(), 0, 1)))
                    unset($array[$i]);
            }
        }
    }
}


//----------------------------------------------------------------------------------
$_REQUEST["selchar"] = trim(addslashes($_REQUEST["selchar"]));
if($_REQUEST["selchar"] == "")
    $_REQUEST["selchar"] = "A"; 
 
if ($_REQUEST["exec"] == "editcontact")
{
    require_once 'kontakte.edit.php';
} else if($_REQUEST["exec"] == "delete")
{
    $contact = new UserContact((int)$_REQUEST["id"]);
    if($_USER->getId() == $contact->getUser()->getId())
    {
        $savemsg = getSaveMessage($contact->delete());
    }
} else {
?>
<link rel="stylesheet" type="text/css" href="./css/contact.css" />
<table width="100%">
	<tr>
		<td width="200" class="content_header"><img src="<?=$_MENU->getIcon($_REQUEST['page'])?>"> <?=$_LANG->get('Kontakte')?></td>
		<td class="content_header"><?=$savemsg?></td>
    </tr>
</table>

<input type="button" name="selchar" style="width:90px" value="<?=$_LANG->get('Neuer Kontakt')?>"
class="kontakt" onmouseover="markbtn(this,0)" onmouseout="markbtn(this,1)"
onclick="location.href='index.php?page=<?=$_REQUEST['page']?>&exec=editcontact&selchar=new'">

<input type="button" name="selchar" style="width:28px" value="ALL"
class="kontakt<?if($_REQUEST["selchar"] == "ALL") echo "Active";?>" 
onmouseover="markbtn(this,0)" onmouseout="markbtn(this,1)"
onclick="location.href='index.php?page=<?=$_REQUEST['page']?>&selchar=' +this.value">
<? 
for ($i = ord('A'); $i <= ord('Z'); $i++)
{
    echo '<input type="button" name="selchar" style="width:21px" value="'.chr($i).'"
            class="kontakt'; if($_REQUEST["selchar"] == chr($i)) echo "Active";
    echo ' onmouseover="markbtn(this,0)" onmouseout="markbtn(this,1)"            
            onclick="location.href=\'index.php?page='.$_REQUEST['page'].'&selchar=\' +this.value">
    ';
}
?>
<input type="button" name="selchar" style="width:26px" value="0-9"
class="kontakt<?if($_REQUEST["selchar"] == "0-9") echo "Active";?>" onmouseover="markbtn(this,0)" onmouseout="markbtn(this,1)"
onclick="location.href='index.php?page=<?=$_REQUEST['page']?>&selchar=' +this.value">
<div>
<? 
// Benutzer
//$contacts = User::getAllUser(User::ORDER_NAME, $_USER->getClient()->getId());
$contacts = array();

/*
echo "<br><br><br><br>";
var_dump($contacts);
echo "<br><br><br><br>";
*/

// Kontakte des aktuellen Benutzers
foreach(UserContact::getAllUserContacts() as $c)
    array_push($contacts, $c);
//Geschaeftskontakte
/*
$businessContact = BusinessContact::getAllBusinessContacts();
foreach($businessContact as $bc)
{
    array_push($contacts, $bc);

    foreach($bc->getContactpersons() as $cp)
    {
        array_push($contacts, $cp);
    }
}
*/

// Filtern
if($_REQUEST["selchar"] != "" && $_REQUEST["selchar"] != "ALL")
{
    filter($contacts, $_REQUEST["selchar"]);
}

// Sortieren
usort($contacts, "cmpByName");

foreach($contacts as $c)
{
    
    if (is_a($c, "User") && $c->getId() > 1)
    {
    ?>
	<div class="contactVcard">
		<div class="headerLine">
		    <img src="images/icons/user.png" title="<?=$_LANG->get('Benutzer');?>" /> <?=$c->getFirstname()?> <?=$c->getLastname()?>
            <div class="vCardOptionen">
                <a class="icon-link" href="index.php?page=libs/modules/organizer/nachrichten.php&exec=newmail&contact_type=systemuser&contact_id=<?=$c->getId()?>"><img src="images/icons/mail.png" /></a>
            </div>
		</div>
	
		<div class="vCardContent">
		    <b><?=$c->getFirstname()?> <?=$c->getLastname()?></b><br>
		    <?=$c->getClient()->getName()?><br><br>
		    <?=$c->getEmail()?><br>
		    Tel.: <?=$c->getPhone()?><br>
		</div>
	</div>

	<?
    }
    
    if (is_a($c, "UserContact"))
    {
        ?>
        <div class="contactVcard">
            <div class="headerLine">
                <img src="images/icons/card-address.png" title="<?=$_LANG->get('Kontakt');?>" /> <?=$c->getNameAsLine()?>
                <div class="vCardOptionen">
                    <? if($c->getEmail() != "") { ?><a class="icon-link" href="index.php?page=libs/modules/organizer/nachrichten.php&exec=newmail&contact_type=usercontact&contact_id=<?=$c->getId()?>"><img src="images/icons/mail.png" /></a><? } ?>
                    <a class="icon-link" href="index.php?page=<?=$_REQUEST['page']?>&exec=editcontact&id=<?=$c->getId()?>&selchar=<?=$_REQUEST["selchar"]?>"><img src="images/icons/pencil.png" /></a>
                </div>
            </div>
            
            <div class="vCardContent">
                <b><?=nl2br($c->getName1())?></b><br>
                <? if($c->getName2() != "") echo $c->getName2()."<br>" ?><br>
                <?=nl2br($c->getAddressAsLine())?><br>
                <br>
                <? if($c->getPhone() != "") echo "Tel.: ".$c->getPhone()."<br>"?>
                <? if($c->getFax() != "") echo "Fax.: ".$c->getFax()."<br>"?>
                <? if($c->getCellphone() != "") echo "Handy: ".$c->getCellphone()."<br>"?>
                <? if($c->getEmail() != "") echo "E-Mail: ".$c->getEmail()."<br>"?>
            </div>
        </div>
    <?
    }

    if (is_a($c, "BusinessContact"))
    {
        ?>
        <div class="contactVcard">
            <div class="headerLine">
                <img src="images/icons/building.png" title="<?=$_LANG->get('Gesch&auml;ftkontakt');?>" /> <?=$c->getNameAsLine()?>
                <div class="vCardOptionen">
                <? if($c->getEmail() != "") { ?><a class="icon-link" href="index.php?page=libs/modules/organizer/nachrichten.php&exec=newmail&contact_type=businesscontact&contact_id=<?=$c->getId()?>"><img src="images/icons/mail.png" /></a><? } ?>
                </div>
            </div>
            
            <div class="vCardContent">
                <b><?=nl2br($c->getName1())?></b><br>
                <? if($c->getName2() != "") echo $c->getName2()."<br>" ?><br>
                <?=nl2br($c->getAddressAsLine())?><br><br>
                <? if($c->getPhone() != "") echo "Tel.: ".$c->getPhone()."<br>"?>
                <? if($c->getFax() != "") echo "Fax.: ".$c->getFax()."<br>"?>
                <? if($c->getEmail() != "") echo "E-Mail: ".$c->getEmail()."<br>"?>
            </div>
        </div>
    <?
    }
    
    if (is_a($c, "ContactPerson"))
    {
        ?>
        <div class="contactVcard">
            <div class="headerLine">
                <img src="images/icons/user-business.png" title="<?=$_LANG->get('Anprechpartner');?>" /> <?=$c->getNameAsLine()?>
                <div class="vCardOptionen">
                    <? if($c->getEmail() != "") { ?><a class="icon-link" href="index.php?page=libs/modules/organizer/nachrichten.php&exec=newmail&contact_type=contactperson&contact_id=<?=$c->getId()?>"><img src="images/icons/mail.png" /></a><? } ?>
                </div>
            </div>
            
            <div class="vCardContent">
                <b><?=nl2br($c->getName1())?></b><br>
                <? if($c->getName2() != "") echo $c->getName2()."<br>" ?><br>
                <?=nl2br($c->getAddressAsLine())?><br><br>
                <? if($c->getPhone() != "") echo "Tel.: ".$c->getPhone()."<br>"?>
                <? if($c->getFax() != "") echo "Fax.: ".$c->getFax()."<br>"?>
                <? if($c->getEmail() != "") echo "E-Mail: ".$c->getEmail()."<br>"?>
                </div>
        </div>
    <?
    }
    
}

?>
</div>
<? } ?>