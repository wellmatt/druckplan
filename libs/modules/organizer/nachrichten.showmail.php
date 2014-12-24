<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       02.03.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
$nachricht = new Nachricht($_REQUEST["id"]);
$nachricht->setRead(true);
?>
<div class="showmailMenu">
    <a class="icon-link" href="index.php?page=<?=$_REQUEST['page']?>&exec=newmail&subexec=answer&answer_id=<?=$nachricht->getId()?>&folder=<?=$_REQUEST["folder"]?>"><img src="images/icons/mail--arrow.png" alt="<?=$_LANG->get('Antworten')?>" title="<?=$_LANG->get('Antworten')?>"></a>
    <a class="icon-link" href="index.php?page=<?=$_REQUEST['page']?>&exec=newmail&subexec=forward&forward_id=<?=$nachricht->getId()?>&folder=<?=$_REQUEST["folder"]?>"><img src="images/icons/mail--arrow.png" alt="<?=$_LANG->get('Weiterleiten')?>" title="<?=$_LANG->get('Weiterleiten')?>"></a>
    <a class="icon-link" href="#" onclick="askDel('index.php?page=<?=$_REQUEST['page']?>&exec=delete&chk_msg_<?=$nachricht->getId()?>&folder=<?=$_REQUEST["folder"]?>')"><img src="images/icons/cross.png" alt="<?=$_LANG->get('L&ouml;schen')?>" title="<?=$_LANG->get('L&ouml;schen')?>"></a>
    <?=$_LANG->get('Verschieben in')?>:
    <select name="move_to_folder" style="width:150px"  >
        <?
            foreach($folders as $f) 
            {
                ?>
                <option value="<?=$f->getId()?>" onclick="location.href='index.php?page=<?=$_REQUEST['page']?>&exec=move_to&folder=<?=$f->getId()?>&id=<?=$_REQUEST["id"]?>&crt_folder=<?=$_REQUEST["folder"]?>'"><?=$f->getName()?></option>
                <?
                if (is_array($f->getSub()))
                {
                    $subFolder = $f->getSub();
                    foreach ($subFolder as $sub)
                    {?>
                        <option value="<?=$sub->getId()?>" onclick="location.href='index.php?page=<?=$_REQUEST['page']?>&exec=move_to&folder=<?=$sub->getId()?>&id=<?=$_REQUEST["id"]?>&crt_folder=<?=$_REQUEST["folder"]?>'">&nbsp;&nbsp;&nbsp;&nbsp;<?=$sub->getName()?></option>  
                    <?}
                }
            }
        ?>
    </select>
</div>
<div class="showmailHeader">
    <table width="100%">
        <colgroup>
            <col width="100">
            <col>
        </colgroup>
        <tr>
            <td><b><?=$_LANG->get('Absender')?>:</b></td>
            <td><?=$nachricht->getFrom()->getNameAsLine()?></td>
        </tr>
        <tr>
            <td><b><?=$_LANG->get('Empf&auml;nger')?>:</b></td>
            <td><?
                $rcpt = $nachricht->getTo();
                foreach ($rcpt as $rc)
                {
                    echo $rc->getNameAsLine()."<br>";
                }?>
            </td>
        </tr>        
        <tr>
            <td><b><?=$_LANG->get('Betreff')?>:</b></td>
            <td><?=$nachricht->getSubject()?></td>
        </tr>
        <tr>
            <td><b><?=$_LANG->get('Anh&auml;nge')?>:</b></td>
            <td>
                <?
                    foreach($nachricht->getAttachments() as $at)
                    {
                        echo '<img src="images/icons/paper-clip.png"> <a href="libs/modules/documents/document.get.iframe.php?getDoc='.$at->getId().'&version=email">'.$at->getName()."</a> &nbsp;";
                    } 
                ?>
            </td>
        </tr>
        <tr>
            <td><b><?=$_LANG->get('Datum')?>:</b></td>
            <td><?=date('d.m.Y - H:m:s', $nachricht->getCreated())?></td>
        </tr>
    </table>
</div>
<div class="showmailBody">
<?=$nachricht->getText()?>
</div>