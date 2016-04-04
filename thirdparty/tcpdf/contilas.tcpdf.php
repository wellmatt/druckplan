<?php
class TCPDF_BG extends TCPDF {
    //Page header
    public function Header() {
        // get the current page break margin
        $bMargin = $this->getBreakMargin();
        // get current auto-page-break mode
        $auto_page_break = $this->AutoPageBreak;
        // disable auto-page-break
        $this->SetAutoPageBreak(false, 0);
        // set bacground image
        $img_file = 'docs/templates/briefbogen.jpg';
        $tmp_width = $this->fwPt / $this->k;
        $tmp_height = $this->fhPt / $this->k;
        $this->Image($img_file, 0, 0, $tmp_width, $tmp_height, 'JPEG', '', '', false, 300, '', false, false, 0);
        // restore auto-page-break status
        $this->SetAutoPageBreak($auto_page_break, $bMargin);
        // set the starting point for the page content
        $this->setPageMark();
    }
}
?>