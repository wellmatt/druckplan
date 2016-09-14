<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */

// just require TCPDF instead of FPDF
require_once 'thirdparty/tcpdf/tcpdf.php';
require_once 'thirdparty/fpdi/fpdi.php';

class FPDIPdf extends FPDI
{
    /**
     * "Remembers" the template id of the imported page
     */
    var $_tplIdx;
    var $_headerfile = '';

    public function setHeaderfile($file)
    {
        $this->_headerfile = $file;
    }

    /**
     * Draw an imported PDF on every page
     */
    function Header()
    {
        $this->setSourceFile($this->_headerfile);
        $this->_tplIdx = $this->importPage(1);
        $this->useTemplate($this->_tplIdx, 0, 0);
    }

    function Footer()
    {
        // emtpy method body
    }
}