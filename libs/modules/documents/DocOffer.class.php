<?php
/**
 *  Copyright (c) 2017 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2017
 *
 */
require_once 'doc.class.php';

class DocOffer extends Doc{
    protected $template = 'docs/templates/coloffer.tmpl.php';
    protected $docfolder = 'coloffer/';

    protected function bootClasses()
    {
        $this->order = new CollectiveInvoice($this->requestId);
    }

    /**
     * @param int $version
     * @param bool $oldhash
     * @return bool|string
     */
    public function createDoc($version, $oldhash = false)
    {
        $ret = parent::createDoc($version, $oldhash);
        return $ret;
    }
}