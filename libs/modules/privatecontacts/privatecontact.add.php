<?php
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       08.03.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
require_once 'libs/modules/privatecontacts/privatecontact.class.php';

$_REQUEST["id"] = (int)$_REQUEST["id"];

$privatecontact = new PrivateContact($_REQUEST["id"]);

if ($_REQUEST["exec"] == "delete") {
    $privatecontact->delete();
    echo '<script language="JavaScript">parent.location.href="index.php?page=libs/modules/privatecontacts/privatecontact.overview.php";</script>';
}

if ($_REQUEST["exec"] == "save") {
//prettyPrint($_REQUEST);
    $privatecontact->setActive(1);
    $privatecontact->setTitle(trim(addslashes($_REQUEST["title"])));
    $privatecontact->setName1(trim(addslashes($_REQUEST["name1"])));
    $privatecontact->setName2(trim(addslashes($_REQUEST["name2"])));
    $privatecontact->setAddress1(trim(addslashes($_REQUEST["address1"])));
    $privatecontact->setAddress2(trim(addslashes($_REQUEST["address2"])));
    $privatecontact->setZip(trim(addslashes($_REQUEST["zip"])));
    $privatecontact->setCity(trim(addslashes($_REQUEST["city"])));
    $privatecontact->setCountry(new Country (trim(addslashes($_REQUEST["country"]))));
    $privatecontact->setEmail(trim(addslashes($_REQUEST["email"])));
    $privatecontact->setPhone(trim(addslashes($_REQUEST["phone"])));
    $privatecontact->setMobil(trim(addslashes($_REQUEST["mobil"])));
    $privatecontact->setFax(trim(addslashes($_REQUEST["fax"])));
    $privatecontact->setWeb(trim(addslashes($_REQUEST["web"])));

    $privatecontact->setAltTitle(trim(addslashes($_REQUEST["alt_title"])));
    $privatecontact->setAltName1(trim(addslashes($_REQUEST["alt_name1"])));
    $privatecontact->setAltName2(trim(addslashes($_REQUEST["alt_name2"])));
    $privatecontact->setAltAddress1(trim(addslashes($_REQUEST["alt_address1"])));
    $privatecontact->setAltAddress2(trim(addslashes($_REQUEST["alt_address2"])));
    $privatecontact->setAltZip(trim(addslashes($_REQUEST["alt_zip"])));
    $privatecontact->setAltCity(trim(addslashes($_REQUEST["alt_city"])));
    $privatecontact->setAltCountry(new Country (trim(addslashes($_REQUEST["alt_country"]))));
    $privatecontact->setAltEmail(trim(addslashes($_REQUEST["alt_email"])));
    $privatecontact->setAltPhone(trim(addslashes($_REQUEST["alt_phone"])));
    $privatecontact->setAltMobil(trim(addslashes($_REQUEST["alt_mobil"])));
    $privatecontact->setAltFax(trim(addslashes($_REQUEST["alt_fax"])));
    $privatecontact->setAltWeb(trim(addslashes($_REQUEST["alt_web"])));

    $privatecontact->setComment(trim(addslashes($_REQUEST["comment"])));
    $tmp_busi = new BusinessContact((int)$_REQUEST["customer"]);
    $privatecontact->setBusinessContact($tmp_busi);

    if ($_REQUEST["birthdate"] != "") {
        $tmp_date = explode('.', trim(addslashes($_REQUEST["birthdate"])));
        $tmp_date = mktime(2, 0, 0, $tmp_date[1], $tmp_date[0], $tmp_date[2]);
    } else {
        $tmp_date = 0;
    }
    $privatecontact->setBirthDate($tmp_date);

    $user_list = Array();
    if ($_REQUEST["access"]) {
        foreach ($_REQUEST["access"] as $qusr) {
            $user_list[] = new User((int)$qusr);
        }
    }
    $privatecontact->setAccess($user_list);

    $savemsg = getSaveMessage($privatecontact->save());
    $_REQUEST["id"] = $privatecontact->getId();

    $privatecontact = new PrivateContact($_REQUEST["id"]);
}
$countries = Country::getAllCountries();
?>
<script language="javascript">
    $(function () {
        $.datepicker.setDefaults($.datepicker.regional['<?=$_LANG->getCode()?>']);
        $('#birthdate').datepicker(
            {
                showOtherMonths: true,
                selectOtherMonths: true,
                dateFormat: 'dd.mm.yy',
//			showOn: "button",
//			buttonImage: "images/icons/calendar-blue.png",
//			buttonImageOnly: true,
                onSelect: function (selectedDate) {
                    checkDate(selectedDate);
                }
            });
    });
</script>
<script>
    $(function () {
        $("#customer_search").autocomplete({
            delay: 0,
            source: 'libs/modules/tickets/ticket.ajax.php?ajax_action=search_customer',
            minLength: 2,
            dataType: "json",
            select: function (event, ui) {
                $('#customer').val(ui.item.value);
                $('#customer_search').val(ui.item.label);
                return false;
            }
        });
    });
</script>

<?php // Qickmove generation
$quickmove = new QuickMove();
$quickmove->addItem('Seitenanfang', '#top', null, 'glyphicon-chevron-up');
$quickmove->addItem('Zurück', 'index.php?page=libs/modules/privatecontacts/privatecontact.overview.php', null, 'glyphicon-step-backward');
$quickmove->addItem('Speichern', '#', "$('#user_form').submit();", 'glyphicon-floppy-disk');
if (($privatecontact->getId() > 0 && $privatecontact->getCrtuser()->getId() == $_USER->getId()) || $_USER->isAdmin()) {
    if ($_REQUEST["exec"] != "new") {
        $quickmove->addItem('Löschen', '#', "askDel('index.php?page=" . $_REQUEST['page'] . "&exec=delete&id=" . $privatecontact->getId() . "');", 'glyphicon-trash', true);
    }
}
echo $quickmove->generate();
// end of Quickmove generation ?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">
            <? if ($privatecontact->getId()) echo $_LANG->get('Kontakt &auml;ndern'); else echo $_LANG->get('Kontakt hinzuf&uuml;gen'); ?>
        </h3>
    </div>
    <div class="panel-body">
        <form action="index.php?page=<?= $_REQUEST['page'] ?>" method="post" name="user_form" id="user_form"
              class="form-horizontal" role="form">
            <input type="hidden" name="exec" value="save">
            <input type="hidden" name="id" value="<?= $privatecontact->getId() ?>">
             <div class="row">
                 <div class="col-md-6">
                     <div class="panel panel-default">
                         <div class="panel-heading">
                             <h3 class="panel-title">
                                 Kontaktdaten
                             </h3>
                         </div>
                         <div class="panel-body">
                             <div class="form-group">
                                 <label for="" class="col-sm-4 control-label">Anrede</label>
                                 <div class="col-sm-8">
                                     <select name="title" type="text" class="form-control" onfocus="markfield(this,0)"
                                             onblur="markfield(this,1)">
                                         <option value="">Bitte w&auml;hlen</option>
                                         <?php $titles = array("Herr", "Frau", "Dr.", "Prof.");
                                         foreach ($titles as $title) {
                                             echo '<option value="' . $title . '"';
                                             if ($privatecontact->getTitle() == $title) echo ' selected ="selected"';
                                             echo '>' . $title . '</option>';
                                         }
                                         ?>
                                     </select>
                                 </div>
                             </div>
                             <div class="form-group">
                                 <label for="" class="col-sm-4 control-label">Nachname</label>
                                 <div class="col-sm-8">
                                     <input name="name1" type="text" class="form-control"
                                            value="<?= $privatecontact->getName1() ?>" onfocus="markfield(this,0)"
                                            onblur="markfield(this,1)">
                                 </div>
                             </div>
                             <div class="form-group">
                                 <label for="" class="col-sm-4 control-label">Vorname</label>
                                 <div class="col-sm-8">
                                     <input name="name2" type="text" class="form-control"
                                            value="<?= $privatecontact->getName2() ?>" onfocus="markfield(this,0)"
                                            onblur="markfield(this,1)">
                                 </div>
                             </div>
                             <div class="form-group">
                                 <label for="" class="col-sm-4 control-label">Adressfeld 1</label>
                                 <div class="col-sm-8">
                                     <input name="address1" type="text" class="form-control"
                                            value="<?= $privatecontact->getAddress1() ?>" onfocus="markfield(this,0)"
                                            onblur="markfield(this,1)">
                                 </div>
                             </div>
                             <div class="form-group">
                                 <label for="" class="col-sm-4 control-label">Adressfeld 2</label>
                                 <div class="col-sm-8">
                                     <input name="address2" type="text" class="form-control"
                                            value="<?= $privatecontact->getAddress2() ?>" onfocus="markfield(this,0)"
                                            onblur="markfield(this,1)">
                                 </div>
                             </div>
                             <div class="form-group">
                                 <label for="" class="col-sm-4 control-label">Postleitzahl</label>
                                 <div class="col-sm-8">
                                     <input name="zip" type="text" class="form-control" value="<?= $privatecontact->getZip() ?>"
                                            onfocus="markfield(this,0)" onblur="markfield(this,1)">
                                 </div>
                             </div>
                             <div class="form-group">
                                 <label for="" class="col-sm-4 control-label">Stadt</label>
                                 <div class="col-sm-8">
                                     <input name="city" type="text" class="form-control"
                                            value="<?= $privatecontact->getCity() ?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
                                 </div>
                             </div>
                             <div class="form-group">
                                 <label for="" class="col-sm-4 control-label">Land</label>
                                 <div class="col-sm-8">
                                     <select name="country" type="text" class="form-control" onfocus="markfield(this,0)"
                                             onblur="markfield(this,1)">
                                         <? foreach ($countries as $c) { ?>
                                             <option value="<?= $c->getId() ?>"
                                                 <? if ($privatecontact->getCountry()->getId() == $c->getId()) echo "selected"; ?>>
                                                 <?= $c->getName() ?>
                                             </option>
                                         <? } ?>
                                     </select>
                                 </div>
                             </div>
                             <div class="form-group">
                                 <label for="" class="col-sm-4 control-label">Telefon</label>
                                 <div class="col-sm-8">
                                     <input name="phone" type="text" class="form-control"
                                            value="<?= $privatecontact->getPhone() ?>" onfocus="markfield(this,0)"
                                            onblur="markfield(this,1)">
                                 </div>
                             </div>
                             <div class="form-group">
                                 <label for="" class="col-sm-4 control-label">Fax</label>
                                 <div class="col-sm-8">
                                     <input name="fax" type="text" class="form-control"
                                            value="<?= $privatecontact->getFax() ?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
                                 </div>
                             </div>
                             <div class="form-group">
                                 <label for="" class="col-sm-4 control-label">Mobil</label>
                                 <div class="col-sm-8">
                                     <input name="mobil" type="text" class="form-control"
                                            value="<?= $privatecontact->getMobil() ?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
                                 </div>
                             </div>
                             <div class="form-group">
                                 <label for="" class="col-sm-4 control-label">Email</label>
                                 <div class="col-sm-8">
                                     <input name="email" type="text" class="form-control"
                                            value="<?= $privatecontact->getEmail() ?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
                                 </div>
                             </div>
                             <div class="form-group">
                                 <label for="" class="col-sm-4 control-label">Web</label>
                                 <div class="col-sm-8">
                                     <input name="web" type="text" class="form-control"
                                            value="<?= $privatecontact->getWeb() ?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
                                 </div>
                             </div>
                             <div class="form-group">
                                 <label for="" class="col-sm-4 control-label">Geburtstag</label>
                                 <div class="col-sm-8">
                                     <input name="birthdate" id="birthdate" type="text" class="form-control"
                                            onfocus="markfield(this,0)" onblur="markfield(this,1)"
                                         <? if ($privatecontact->getBirthDate() != 0) echo 'value="' . date("d.m.Y", $privatecontact->getBirthDate()) . '"'; ?> title="<?= $_LANG->get('Geburtstag'); ?>">
                                 </div>
                             </div>
                             <div class="form-group">
                                 <label for="" class="col-sm-4 control-label">Kunde</label>
                                 <div class="col-sm-8">
                                     <input name="customer_search" class="form-control" id="customer_search" type="text"
                                         <?php if ($privatecontact->getBusinessContactId()>0) echo ' value="'.$privatecontact->getBusinessContact()->getNameAsLine().'" ';?> class="text"/>
                                     <input name="customer" id="customer"
                                         <?php if ($privatecontact->getBusinessContactId()>0) echo ' value="'.$privatecontact->getBusinessContact()->getId().'" ';?>
                                            type="hidden"/>
                                 </div>
                             </div>
                             <div class="form-group">
                                 <label for="" class="col-sm-4 control-label">Kommentar</label>
                                 <div class="col-sm-8">
							<textarea name="comment" id="notify_mail_adr" type="text" class="form-control"
                                      onfocus="markfield(this,0)"
                                      onblur="markfield(this,1)"><?= $privatecontact->getComment() ?></textarea>
                                 </div>
                             </div>
                         </div>
                     </div>
                 </div>
                 <div class="col-md-6">
                     <div class="panel panel-default">
                         <div class="panel-heading">
                             <h3 class="panel-title">
                                 Alternative
                             </h3>
                         </div>
                         <div class="panel-body">
                             <div class="form-group">
                                 <label for="" class="col-sm-4 control-label">Anrede</label>
                                 <div class="col-sm-8">
                                     <select name="alt_title" type="text" class="form-control" onfocus="markfield(this,0)"
                                             onblur="markfield(this,1)">
                                         <option value="">Bitte w&auml;hlen</option>
                                         <?php $titles = array("Herr", "Frau", "Dr.", "Prof.");
                                         foreach ($titles as $title) {
                                             echo '<option value="' . $title . '"';
                                             if ($privatecontact->getAltTitle()== $title) echo ' selected ="selected"';
                                             echo '>' . $title . '</option>';
                                         }
                                         ?>
                                     </select>
                                 </div>
                             </div>
                             <div class="form-group">
                                 <label for="" class="col-sm-4 control-label">Nachname</label>
                                 <div class="col-sm-8">
                                     <input name="alt_name1" type="text" class="form-control"
                                            value="<?= $privatecontact->getAltName1() ?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
                                 </div>
                             </div>
                             <div class="form-group">
                                 <label for="" class="col-sm-4 control-label">Vorname</label>
                                 <div class="col-sm-8">
                                     <input name="alt_name2" type="text" class="form-control"
                                            value="<?= $privatecontact->getAltName2() ?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
                                 </div>
                             </div>
                             <div class="form-group">
                                 <label for="" class="col-sm-4 control-label">Adressfeld 1</label>
                                 <div class="col-sm-8">
                                     <input name="alt_address1" type="text" class="form-control"
                                            value="<?= $privatecontact->getAltAddress1() ?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
                                 </div>
                             </div>
                             <div class="form-group">
                                 <label for="" class="col-sm-4 control-label">Adressfeld 2</label>
                                 <div class="col-sm-8">
                                     <input name="alt_address2" type="text" class="form-control"
                                            value="<?= $privatecontact->getAltAddress2() ?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
                                 </div>
                             </div>
                             <div class="form-group">
                                 <label for="" class="col-sm-4 control-label">Postleitzahl</label>
                                 <div class="col-sm-8">
                                     <input name="alt_zip" type="text" class="form-control"
                                            value="<?= $privatecontact->getAltZip() ?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
                                 </div>
                             </div>
                             <div class="form-group">
                                 <label for="" class="col-sm-4 control-label">Stadt</label>
                                 <div class="col-sm-8">
                                     <input name="alt_city" type="text" class="form-control"
                                            value="<?= $privatecontact->getAltCity() ?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
                                 </div>
                             </div>
                             <div class="form-group">
                                 <label for="" class="col-sm-4 control-label">Land</label>
                                 <div class="col-sm-8">
                                     <select name="alt_country" type="text" class="form-control" onfocus="markfield(this,0)"
                                             onblur="markfield(this,1)">
                                         <? foreach ($countries as $c) { ?>
                                             <option value="<?= $c->getId() ?>"
                                                 <? if ($privatecontact->getAltCountry()->getId() == $c->getId()) echo "selected"; ?>>
                                                 <?= $c->getName() ?>
                                             </option>
                                         <? } ?>
                                     </select>
                                 </div>
                             </div>
                             <div class="form-group">
                                 <label for="" class="col-sm-4 control-label">Telefon</label>
                                 <div class="col-sm-8">
                                     <input name="alt_phone" type="text" class="form-control"
                                            value="<?= $privatecontact->getAltPhone() ?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
                                 </div>
                             </div>
                             <div class="form-group">
                                 <label for="" class="col-sm-4 control-label">Fax</label>
                                 <div class="col-sm-8">
                                     <input name="alt_fax" type="text" class="form-control"
                                            value="<?= $privatecontact->getAltFax() ?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
                                 </div>
                             </div>
                             <div class="form-group">
                                 <label for="" class="col-sm-4 control-label">Mobil</label>
                                 <div class="col-sm-8">
                                     <input name="alt_mobil" type="text" class="form-control"
                                            value="<?= $privatecontact->getAltMobil() ?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
                                 </div>
                             </div>
                             <div class="form-group">
                                 <label for="" class="col-sm-4 control-label">Email</label>
                                 <div class="col-sm-8">
                                     <input name="alt_email" type="text" class="form-control"
                                            value="<?= $privatecontact->getAltEmail() ?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
                                 </div>
                             </div>
                             <div class="form-group">
                                 <label for="" class="col-sm-4 control-label">Web</label>
                                 <div class="col-sm-8">
                                     <input name="alt_web" type="text" class="form-control"
                                            value="<?= $privatecontact->getAltWeb() ?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
                                 </div>
                             </div>
                         </div>
                     </div>
                 </div>
             </div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        Freigabe
                    </h3>
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <?php
                            $all_users = User::getAllUser();
                            $qid_arr = Array();
                            foreach ($privatecontact->getAccess() as $qid) {
                                $qid_arr[] = $qid->getId();
                            }
                            $qi = 0;
                            foreach ($all_users as $qusr) {
                                if ($qi == 0) echo '<tr>';
                                ?>
                                <td class="content_row_header" valign="top" width="20%">
                                    <input type="checkbox" name="access[]" <?php if (in_array($qusr->getId(), $qid_arr)) echo ' checked '; ?> value="<?php echo $qusr->getId(); ?>"/>
                                    <?php echo $qusr->getNameAsLine(); ?></td>
                                <?php if ($qi == 4) {
                                    echo '</tr>';
                                    $qi = -1;
                                } ?>
                                <?php $qi++;
                            } ?>
                        </table>
                    </div>
                </div>
            </div>
        <form/>
    </div>
</div>