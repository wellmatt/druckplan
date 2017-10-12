<?php
/**
 *  Copyright (c) 2017 Teuber Consult + IT GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <alexander.scherer@teuber-consult.de>, 2017
 *
 */
require_once 'material.class.php';

if ($_FILES['paperfile_igepa']){
    $setting_tradegroup = new Tradegroup($_REQUEST['article_tradegroup']);
    $setting_supplier = $_REQUEST['article_suppier'];
    if ($_REQUEST['article_storage'])
        $setting_storage = 1;
    else
        $setting_storage = 0;

    $updated = 0;
    $imported = 0;
    $skipped = 0;
    $art_updated = 0;
    $art_imported = 0;
    $art_skipped = 0;

    $fileName = $_FILES['paperfile_igepa']['name'];
    $tmpName = $_FILES['paperfile_igepa']['tmp_name'];
    $fileSize = $_FILES['paperfile_igepa']['size'];
    $fileType = $_FILES['paperfile_igepa']['type'];
    $res = array_map(function($v){return str_getcsv($v, "\t");}, file($tmpName));

    // check if it's valid
    if (!count($res)>0){
        die('Fehler beim Parsen der Datei!');
    }
    if (!count($res[0]) == 36){
        die('Fehler beim Parsen der Datei!');
    }
    if (!$res[0][0] == 'Artikelnummer'){
        die('Fehler beim Parsen der Datei!');
    }
    if (!$res[0][35] == 'Papierart'){
        die('Fehler beim Parsen der Datei!');
    }

    for ($i = 1; $i < count($res); $i++) {
        $row = $res[$i];
        // Parse row into igepa class
        $igepa = new MaterialPaperIgepa($row[0],$row[1],$row[2],$row[3],$row[4],$row[5],$row[6],$row[7],$row[8],$row[9],$row[10],$row[11],$row[12],$row[13],$row[14],$row[15],$row[16],$row[17],$row[18],$row[19],$row[20],$row[21],$row[22],$row[23],$row[24],$row[25],$row[26],$row[27],$row[28],$row[29],$row[30],$row[31],$row[32],$row[33],$row[34],$row[35]);
        if ($igepa->checkValid() || $igepa->checkValidRoll()){ // if all required fields are filled
            if ($igepa->checkValid()){
                $type = 1; // Papier
            } else {
                $type = 2; // Rolle
            }

            $existing = MaterialPaper::getForNumber($igepa->artikelnummer);
            if ($existing->getId() == 0){
                $existing = MaterialRoll::getForNumber($igepa->artikelnummer);
            }
            if ($existing->getId() > 0){ // paper already in db
                // does paper need updating?
                if ($type == 1)
                    $paperupdate = $igepa->checkUpdate($existing);
                else
                    $paperupdate = $igepa->checkUpdateRoll($existing);
                if ($paperupdate){
                    $existing->updateFromIgepa($igepa);
                    $updated++;
                } else {
                    $skipped++;
                }
                // does article need updateing?
                $artupt = false;
                $article = $existing->getArticle();

                $tmpupt = $article->updateFromIgepa($igepa, $type);

                // check if prices need update
                $artupt_prices = false;
                $prices = $igepa->getPrices();
                foreach ($prices as $price) {
                    $curr = $article->getCost($price['from']);
                    if ($curr != $price['price'])
                        $artupt_prices = true;
                }
                if ($artupt_prices){
                    $article->delteCostSeperations();
                    foreach ($prices as $price) {
                        $article->saveCost($price['from'], $price['to'], $price['price'], $setting_supplier, $igepa->artikelnummer);
                    }
                }
                if ($tmpupt || $artupt_prices)
                    $art_updated++;

            } else { // hey, it's new -> lets get it imported
                $arr = [
                    'name' => utf8_encode($igepa->bezeichnung),
                    'info' => utf8_encode($igepa->zusatzbezeichnung),
                    'number' => $igepa->artikelnummer,
                    'weight' => $igepa->grammatur,
                    'width' => $igepa->papierbreite,
                    'direction' => $igepa->direction(),
                    'color' => utf8_encode($igepa->farbe),
                ];
                if ($type == 1) {
                    $arr['height'] = $igepa->papierhoehe;
                    $paper = new MaterialPaper(0, $arr);
                } else {
                    $arr['length'] = $igepa->rollenlaenge;
                    $paper = new MaterialRoll(0, $arr);
                }

                // check if there has been an article that we can map
                $exarticle = Article::getArticleByNumber($igepa->artikelnummer);
                if ($exarticle->getId() > 0){

                    // does article need updateing?
                    $artupt = false;
                    $article = $exarticle;

                    $tmpupt = $article->updateFromIgepa($igepa, $type);

                    // check if prices need update
                    $artupt_prices = false;
                    $prices = $igepa->getPrices();
                    foreach ($prices as $price) {
                        $curr = $article->getCost($price['from']);
                        if ($curr != $price['price'])
                            $artupt_prices = true;
                    }
                    if ($artupt_prices){
                        $article->delteCostSeperations();
                        foreach ($prices as $price) {
                            $article->saveCost($price['from'], $price['to'], $price['price'], $setting_supplier, $igepa->artikelnummer);
                        }
                    }
                    if ($tmpupt || $artupt_prices)
                        $art_updated++;
                    $paper->setArticle($article);
                } else {
                    // now let's create it's article
                    if ($type == 1)
                        $title = utf8_encode($igepa->bezeichnung).' '.utf8_encode($igepa->farbe).' '.$igepa->grammatur.'g '.$igepa->papierbreite.'x'.$igepa->papierhoehe.'mm '.$igepa->laufrichtung;
                    else
                        $title = utf8_encode($igepa->bezeichnung).' '.utf8_encode($igepa->farbe).' '.$igepa->grammatur.'g '.$igepa->papierbreite.'mm '.$igepa->laufrichtung;
                    $paperart = new Article();
                    $paperart->setTitle($title);
                    $paperart->setDesc(utf8_encode($igepa->zusatzbezeichnung));
                    $paperart->setNumber($igepa->artikelnummer);
                    $paperart->setTaxkey(TaxKey::getDefaultTaxKey());
                    $paperart->setOrderunit($igepa->abpapiermenge1);
                    $paperart->setMatchcode($igepa->artikelnummer);
                    $paperart->setUsesstorage($setting_storage);
                    $orderamounts = [$igepa->abpapiermenge1,$igepa->abpapiermenge2,$igepa->abpapiermenge3,$igepa->abpapiermenge4,$igepa->abpapiermenge5,$igepa->abpapiermenge6,$igepa->abpapiermenge7,$igepa->abpapiermenge8];
                    $orderamounts = array_unique($orderamounts);
                    $paperart->setOrderamounts($orderamounts);
                    $paperart->setTradegroup($setting_tradegroup);
                    $paperart->save();

                    // allright, article is here now let's add prices
                    $prices = $igepa->getPrices();
                    foreach ($prices as $price) {
                        $paperart->saveCost($price['from'], $price['to'], $price['price'], $setting_supplier, $igepa->artikelnummer);
                    }
                    $paper->setArticle($paperart);
                    $art_imported++;
                }
                $paper->save();
                $imported++;
            }
        } else { // skipping because of missing fields
            $skipped++; $art_skipped++;
        }
    }
}

?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Import Resultat</h3>
    </div>
    <div class="panel-body">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">IGEPA</h3>
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label for="" class="col-sm-2 control-label">Importiert</label>
                    <div class="col-sm-10 form-text"><?php echo $imported;?></div>
                </div>
                <div class="form-group">
                    <label for="" class="col-sm-2 control-label">Aktualisiert</label>
                    <div class="col-sm-10 form-text"><?php echo $updated;?></div>
                </div>
                <div class="form-group">
                    <label for="" class="col-sm-2 control-label">Übersprungen</label>
                    <div class="col-sm-10 form-text"><?php echo $skipped;?></div>
                </div>
            </div>
        </div>
        ** Übersprungene Einträge gehören zu einer anderen Materialgruppe als Papier oder benötigen keine Aktualisierung
    </div>
</div>