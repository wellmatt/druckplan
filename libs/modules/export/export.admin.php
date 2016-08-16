<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Christian Schroeer <cschroeer@ipactor.de>, 2016
 *
 */

if (isset($_REQUEST["export"])){
    switch($_REQUEST["export"])
    {
        case "asp":
            $cps = ContactPerson::getAllContactPersons(null,ContactPerson::ORDER_NAME);
            $cparray = [];
            $cparray[] = Array(
                'ASP_Name',
                'Firmenname',
                'Adresse1',
                'Adresse2',
                'PLZ',
                'Stadt',
                'Land',
                'E-mail',
                'Telefon',
                'Handy',
                'Fax',
                'Geburtsdatum',
            );

            if (count($cps)>0){
                foreach ($cps as $cp) {
                    switch($cp->getActiveAdress()){
                        case 2: // alternativ
                            $cparray[] = Array(
                                $cp->getNameAsLineAlt(),
                                $cp->getBusinessContact()->getNameAsLine(),
                                $cp->getAlt_address1(),
                                $cp->getAlt_address2(),
                                $cp->getAlt_zip(),
                                $cp->getAlt_city(),
                                $cp->getAlt_country()->getName(),
                                $cp->getAlt_email(),
                                $cp->getAlt_phone(),
                                $cp->getAlt_mobil(),
                                $cp->getAlt_fax(),
                                date('d.m.y',$cp->getBirthDate()),
                            );
                            break;
                        case 3: // privat
                            $cparray[] = Array(
                                $cp->getNameAsLinePrivate(),
                                $cp->getBusinessContact()->getNameAsLine(),
                                $cp->getPriv_address1(),
                                $cp->getPriv_address2(),
                                $cp->getPriv_zip(),
                                $cp->getPriv_city(),
                                $cp->getPriv_country()->getName(),
                                $cp->getPriv_email(),
                                $cp->getPriv_phone(),
                                $cp->getPriv_mobil(),
                                $cp->getPriv_fax(),
                                date('d.m.y',$cp->getBirthDate()),
                            );
                            break;
                        default: // default haupt
                            $cparray[] = Array(
                                $cp->getNameAsLine(),
                                $cp->getBusinessContact()->getNameAsLine(),
                                $cp->getAddress1(),
                                $cp->getAddress2(),
                                $cp->getZip(),
                                $cp->getCity(),
                                $cp->getCountry()->getName(),
                                $cp->getEmail(),
                                $cp->getPhone(),
                                $cp->getMobil(),
                                $cp->getFax(),
                                date('d.m.y',$cp->getBirthDate()),
                            );
                            break;
                    }
                }
            }

            if (file_exists("docs/export_asp.csv")){
                unlink("docs/export_asp.csv");
            }
            $f = fopen("docs/export_asp.csv", "w");
            foreach ($cparray as $line) {
                fputcsv($f, $line, chr(9));
            }
            fclose($f);

            break;
        default:
            break;
    }
}

?>

<div class="panel panel-default">
      <div class="panel-heading">
            <h3 class="panel-title">
                Exporte
            </h3>
      </div>
      <div class="panel-body">
          <div class="form-group">
              <label for="" class="col-sm-2 control-label">Kontaktadressen</label>
              <div class="col-sm-2">
                  <button class="btn btn-success" onclick="window.location.href='index.php?page=<?php echo $_REQUEST["page"];?>&export=asp';">Generieren</button>
              </div>
              <?php
              if (file_exists("docs/export_asp.csv")) {
                  ?>
                  <div class="col-sm-2">
                      <a href="docs/export_asp.csv" download="<?php echo date('Y.m.d');?>_Export_ASP.csv">
                        <button class="btn btn-success">Download</button>
                      </a>
                  </div>
                  <?php
              }
              ?>
          </div>
      </div>
</div>