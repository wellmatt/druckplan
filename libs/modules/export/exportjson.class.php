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
        /**
         * Result Array Structure
         *
         * id // int // colinv id
         * number // string // colinv number
         * title // string // colinv title
         * crtdate // int // colinv create date
         * positions // array
         *      articleid // int // position article id
         *      product_width // product format width
         *      product_height // product format height
         *      chromaticity // product chromaticity
         *      amount // position amount
         *      delivdate // delivery date
         *      data // optional print data (pdf/image)
         */
        $res = [];

        $filter = "";
        if ($this->datefrom > 0 && $this->dateto > 0){
            $filter = " AND crtdate >= {$this->datefrom} && crtdate <= {$this->dateto} ";
        }
        $colinvs = CollectiveInvoice::getAllCollectiveInvoice(CollectiveInvoice::ORDER_CRTDATE,$filter);

        foreach ($colinvs as $colinv) {

            $posarray = [];
            $positions = Orderposition::getAllOrderposition($colinv->getId());
            foreach ($positions as $position) {
                $article = new Article($position->getObjectid());
                if ($article->getOrderid()>0){
                    $order = new Order($article->getOrderid());
                    $calcs = Calculation::getAllCalculations($order);
                    foreach ($calcs as $calc) {
                        if ($calc->getState() == 1 && $calc->getAmount() == $position->getAmount()){
                            $posarray[] = Array (
                                'articleid'=> $article->getId(),
                                'product_width'=> $calc->getProductFormatWidth(),
                                'product_height'=> $calc->getProductFormatHeight(),
                                'chromaticity'=> $calc->getChromaticitiesContent(),
                                'amount'=> $position->getAmount(),
                                'delivdate'=> $colinv->getDeliverydate(),
                                'data'=> $position->getFileattach()
                            );
                        }
                    }
                }
            }

            $res[] = Array(
                'id'=> $colinv->getId(),
                'number'=> $colinv->getNumber(),
                'title'=> $colinv->getTitle(),
                'crtdate'=> $colinv->getCrtdate(),
                'positions'=> $posarray,
            );
        }

        return json_encode($res);
    }


}