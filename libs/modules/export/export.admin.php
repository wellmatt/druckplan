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


<link rel="stylesheet" type="text/css" href="jscripts/datetimepicker/jquery.datetimepicker.css"/ >
<script src="jscripts/datetimepicker/jquery.datetimepicker.js"></script>

<script language="JavaScript">
    function generateAepos(){
        var datefrom = $('#aepos_from').val();
        var dateto = $('#aepos_to').val();
        window.location.href='libs/modules/export/export.download.php?function=aepos_export&datefrom='+datefrom+'&dateto='+dateto;
    }

    $(function() {
        $('#aepos_from').datetimepicker({
            lang:'de',
            i18n:{
                de:{
                    months:[
                        'Januar','Februar','März','April',
                        'Mai','Juni','Juli','August',
                        'September','Oktober','November','Dezember',
                    ],
                    dayOfWeek:[
                        "So.", "Mo", "Di", "Mi",
                        "Do", "Fr", "Sa.",
                    ]
                }
            },
            timepicker:false,
            format:'d.m.Y'
        });
        $('#aepos_to').datetimepicker({
            lang:'de',
            i18n:{
                de:{
                    months:[
                        'Januar','Februar','März','April',
                        'Mai','Juni','Juli','August',
                        'September','Oktober','November','Dezember',
                    ],
                    dayOfWeek:[
                        "So.", "Mo", "Di", "Mi",
                        "Do", "Fr", "Sa.",
                    ]
                }
            },
            timepicker:false,
            format:'d.m.Y'
        });
    });
</script>


<div class="panel panel-default">
      <div class="panel-heading">
            <h3 class="panel-title">
                Exporte
            </h3>
      </div>
      <div class="panel-body">
          <div class="form-group">
              <label for="" class="col-sm-3 control-label">Kontaktadressen</label>
              <span class="pull-right">
                   <?php
                   if (file_exists("docs/export_asp.csv")) {
                       ?>
                       <a href="docs/export_asp.csv" download="<?php echo date('Y.m.d');?>_Export_ASP.csv">
                           <button class="btn btn-success">Download</button>
                       </a>
                       <?php
                   }
                   ?>
                   <button class="btn btn-success" onclick="window.location.href='index.php?page=<?php echo $_REQUEST["page"];?>&export=asp';">Generieren</button>
              </span>
          </div>
          <hr>
          <div class="form-group">
              <label for="" class="col-sm-2 control-label">Auftr. Daten Aepos</label>
              <label for="" class="col-sm-1 control-label">Von</label>
              <div class="col-sm-2">
                  <input type="text" id="aepos_from" class="text form-control" onfocus="markfield(this,0)" onblur="markfield(this,1)"/>
              </div>
              <label for="" class="col-sm-1 control-label">Bis</label>
              <div class="col-sm-2">
                  <input type="text" id="aepos_to" class="text form-control" onfocus="markfield(this,0)" onblur="markfield(this,1)"/>
              </div>
              <span class="pull-right">
                   <button class="btn btn-success" onclick="generateAepos();">Generieren</button>
              </span>
          </div>
      </div>
</div>