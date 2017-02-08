<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Christian Schroeer <cschroeer@ipactor.de>, 2016
 *
 */
chdir("../../../");
require_once 'libs/basic/basic.importer.php';
require_once 'libs/modules/accounting/invoiceout.class.php';

if ($_REQUEST["exec"] == "doStorno"){
    $invoiceout = new InvoiceOut((int)$_REQUEST["id"]);
    $invoiceout->setStatus(3);
    $res = $invoiceout->save();
    if ($res){
        $colinv = $invoiceout->getColinv();
        $colinv->setLocked(0);
        $colinv->save();
        $doc = new Document($invoiceout->getDoc());
        $doc->setStornoDate(time());
        $doc->save();
    }
}
if ($_REQUEST["exec"] == "doDelete"){
    $invoiceout = new InvoiceOut((int)$_REQUEST["id"]);
    $res = $invoiceout->delete();
    if ($res){
        $colinv = $invoiceout->getColinv();
        $colinv->setLocked(0);
        $colinv->save();
        $doc = new Document($invoiceout->getDoc());
        $doc->delete();
    }
}
