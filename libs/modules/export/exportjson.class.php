<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *  
 */


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

    public function aepos_export()
    {
        /*
        {
           "project":{
              "name":"AU16-12345",
              "account_manager":"Hans Wurst",
              "description":"Sample Description",
              "customer":{
                 "name":"Lewald und Partner",
                 "number":12332,
                 "street":"Steinriede 13",
                 "zipcode":"30827",
                 "city":"Garbsen"
              },
              "products":[
                 {
                    "name":"Katalog 2017",
                    "parts":[
                       {
                          "name":"Mantel 4 Seiten",
                          "pages":4,
                          "paper_class":{
                             "name":"Matt gestrichen Bilderdruck",
                             "fogra_name":"FOGRA39"
                          },
                          "data":"http://example.org/mantel.pdf"
                       },
                       {
                          "name":"Inhalt 12 Seiten",
                          "pages":12,
                          "paper_class":{
                             "name":"LWC",
                             "fogra_name":"FOGRA46"
                          },
                          "data":"http://example.org/inhalt.pdf"
                       }
                    ]
                 }
              ]
           }
        }
         */
        $res = [];

        $filter = "";
        if ($this->datefrom > 0 && $this->dateto > 0){
            $filter = " AND crtdate >= {$this->datefrom} && crtdate <= {$this->dateto} ";
        }
        $colinvs = CollectiveInvoice::getAllCollectiveInvoice(CollectiveInvoice::ORDER_CRTDATE,$filter);

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
            $products = [];
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
                                    $parts[] = [
                                        "name" => $detail['name'],
                                        "pages" => $detail['umfang'],
                                        "paper_class" => [
                                            "name" => $detail['papername']
                                        ],
                                        "data" => $position->getFileattach()
                                    ];
                                }
                            }
                        }
                        $project["products"][] = [
                            "name" => $order->getProduct()->getName(),
                            "parts" => $parts
                        ];
                    }
                }
            }
            $res[] = Array("project"=>$project);
        }
        return json_encode($res);
    }
}