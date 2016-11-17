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
    $invoiceout->save();
}