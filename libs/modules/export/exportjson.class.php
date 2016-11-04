<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *  
 */
require_once 'libs/modules/attachment/attachment.class.php';


class ExportJson{
    private $data;
    private $datefrom;
    private $dateto;

    /**
     * ExportJson constructor.
     * @param $datefrom
     * @param $dateto
     */
    public function __construct($datefrom, $dateto)
    {
        $this->data = null;
        $this->datefrom = $datefrom;
        $this->dateto = $dateto;
    }

    /**
     * @param CollectiveInvoice $colinv
     * @return string
     */
    public function aepos_export(CollectiveInvoice $colinv = null)
    {
        $res = [];

        if ($colinv == null){

            $filter = "";
            if ($this->datefrom > 0 && $this->dateto > 0){
                $filter = " AND crtdate >= {$this->datefrom} && crtdate <= {$this->dateto} ";
            }
            $colinvs = CollectiveInvoice::getAllCollectiveInvoice(CollectiveInvoice::ORDER_CRTDATE,$filter);

        } else {

            $colinvs = [$colinv];

        }

        foreach ($colinvs as $colinv) {
            $project = [
                "name" => $colinv->getNumber(),
                "account_manager" => $colinv->getInternContact()->getNameAsLine(),
                "description" => $colinv->getComment(),
                "customer" => [
                    "name" => $colinv->getCustomer()->getNameAsLine(),
                    "number" => $colinv->getCustomer()->getCustomernumber(),
                    "street" => $colinv->getCustomer()->getAddress1(),
                    "zipcode" => $colinv->getCustomer()->getZip(),
                    "city" => $colinv->getCustomer()->getCity()
                ],
                "products" => []
            ];
            $positions = Orderposition::getAllOrderposition($colinv->getId());
            foreach ($positions as $position) {
                $parts = [];
                $article = new Article($position->getObjectid());
                if ($article->getOrderid() > 0) {
                    $order = new Order($article->getOrderid());
                    if ($order->getId() > 0 && $order->getProduct() != null && $order->getProduct()->getId() > 0){
                        $calcs = Calculation::getAllCalculations($order);
                        foreach ($calcs as $calc) {
                            if ($calc->getState() == 1 && $calc->getAmount() == $position->getAmount()) {

                                $details = $calc->getDetails();

                                foreach ($details as $detail) {
                                    $machinename = '';
                                    $labelsperroll = $calc->getAmount();
                                    $labelsradius = 0;
                                    $maschines = Machineentry::getMachineForPapertype($detail['paper'],$calc->getId());
                                    if ($maschines){
                                        foreach ($maschines as $maschine) {
                                            if ($maschine->getMachine()->getType() == Machine::TYPE_DRUCKMASCHINE_DIGITAL){
                                                $machinename = $maschine->getMachine()->getName();
                                                if ($maschine->getLabelcount()>0) {
                                                    $labelsperroll = $maschine->getLabelcount();
                                                    $labelsradius = $maschine->getLabelradius();
                                                }
                                            }
                                        }
                                    }

                                    for ($i=1;$i<=$calc->getSorts();$i++){
                                        $data = [];
                                        $contentpdfs = ContentPdf::getAllForOpPartSort($position,$detail['paper'],$i);
                                        foreach ($contentpdfs as $contentpdf) {
                                            $data[] = ["position"=>(int)$contentpdf->getPagina(),"name"=>$contentpdf->getFile()->getFilename(),"url"=>$contentpdf->getFile()->getFileUrl()];
                                        }

                                        $parts[] = [
                                            "name" => $detail['name']." Sorte ".$i,
                                            "pages" => (int)$detail['umfang'],
                                            "paper_class" => [
                                                "name" => $detail['papername'],
                                            ],
                                            "production_type" => (int)3,
                                            "width" => (int)$calc->getProductFormatWidth(),
                                            "height" => (int)$calc->getProductFormatHeight(),
                                            "printing_press" => [
                                                "name" => $machinename
                                            ],
                                            "run" => (int)$calc->getAmount(),
                                            "roll_width" => tofloat($detail['materialbreite']),
                                            "rapport" => tofloat(0),
                                            "margin" => tofloat($calc->getAnschnitt($detail['paper'])),
                                            "labels_per_roll" => (int)$labelsperroll,
                                            "corner_radius" => tofloat($labelsradius),
                                            "data" => $data,
                                        ];
                                    }
                                }
                            }
                        }
                        $project["products"][] = [
                            "name" => $position->getId().' - '.$position->getName(),
                            "parts" => $parts
                        ];
                    }
                }
            }
            $res[] = Array("project"=>$project);
        }

        if ($colinv != null && $colinv->getId()>0 && count($res) == 1){
            $res = $res[0];
        }

        return json_encode($res, JSON_UNESCAPED_SLASHES);
    }
}