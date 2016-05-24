<?php
require_once 'libs/modules/api/api.class.php';

if ($_REQUEST["exec"] == "save")
{
    $new_api = new API();
    $new_api->generateToken();
    $new_api->setTitle($_REQUEST["new_title"]);
    $new_api->setType($_REQUEST["new_type"]);
    $new_api->setPosturl($_REQUEST["new_posturl"]);
    $savemsg = getSaveMessage($new_api->save()).$DB->getLastError();
}

$apis = API::getAllApis();

?>

<?php // Qickmove generation
$quickmove = new QuickMove();
$quickmove->addItem('Seitenanfang','#top',null,'glyphicon-chevron-up');
$quickmove->addItem('Zurück','index.php?page='.$_REQUEST['page'],null,'glyphicon-step-backward');
echo $quickmove->generate();
// end of Quickmove generation ?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">
            <img src="images/icons/gear.png">
            API-Einstellungen
        </h3>
    </div>
    <div class="panel-body">
        <form action="index.php?page=<?= $_REQUEST['page'] ?>" method="post" enctype="multipart/form-data"
              name="api_form" id="api_form">
            <input type="hidden" name="exec" value="save">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <td class="content_row_header">API-ID:</td>
                        <td class="content_row_header">API-Titel:</td>
                        <td class="content_row_header">API-Typ:</td>
                        <td class="content_row_header">API-Post:</td>
                        <td class="content_row_header">API-Token:</td>
                    </tr>
                    </thead>
                    <?php
                    if (count($apis) > 0) {
                        foreach ($apis as $api) {
                            ?>
                            <tr>
                                <td class="content_row_clear"><?php echo $api->getId(); ?></td>
                                <td class="content_row_clear"><?php echo $api->getTitle(); ?></td>
                                <td class="content_row_clear"><?php echo API::returnType($api->getType()); ?></td>
                                <td class="content_row_clear"><?php echo $api->getPosturl(); ?></td>
                                <td class="content_row_clear"><?php echo $api->getToken(); ?></td>
                            </tr>
                        <?php }
                    } ?>
                    <tr>
                        <td class="content_row_clear">&nbsp;</td>
                        <td class="content_row_clear"><input name="new_title"/></td>
                        <td class="content_row_clear">
                            <select name="new_type">
                                <option value="<?php echo API::TYPE_ARTICLE; ?>">Artikel</option>
                            </select>
                        </td>
                        <td class="content_row_clear"><input name="new_posturl"/></td>
                        <td class="content_row_clear"><a href="#" onclick="$('#api_form').submit();"><img
                                    src="images/icons/plus.png" class="pointer icon-link"> generieren</a></td>
                    </tr>
                </table>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        Info : URL: <?php echo "http://" . $_SERVER['SERVER_NAME']; ?>/api.php?token=HIERTOKEN</br>

                    </h3>
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            </br>
                            <u>Typ Artikel:</u></br>
                            </br>
                            - ID (Unique INT)</br>
                            - Titel (VCHAR)</br>
                            - Desc (HTML/TEXT)</br>
                            - Tradegroupid (Unique INT der Warengruppe)</br>
                            - Tradegroup (VCHAR Name der Warengruppe)</br>
                            - Prices (Array Min-Bestellwert, Max-Bestellwert, Preis)</br>
                            - Pictures (Vollwertige URL zum Abrufen der Bilder – ACHTUNG Array und kann mehrere
                            Zurückgeben</br>
                            - UpdateDate (UNIXTIME Datum/Uhrzeit letzter Aktualisierung)</br>
                            - Tax (INT Umsatzsteuer)</br>
                            - ShopNeedsUpload (Artikel benötigt Dateiupload)</br>
                            - Tags (Array VCHAR Artikeltags)</br>
                            - Orderamounts (Array mögl. Bestellmengen)</br>
                            </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
