<? // -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			09.07.2013
// Copyright:		2013 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------
$perso = new Personalization($order->getId());
// Elemente fuer Vorder- oder Reuckseite holen
if ($this->getReverse() == 1){
	$all_items = Personalizationitem::getAllPersonalizationitems($perso->getId(), Personalizationitem::ORDER_YPOS, Personalizationitem::SITE_BACK);
} else {
	$all_items = Personalizationitem::getAllPersonalizationitems($perso->getId(), Personalizationitem::ORDER_YPOS, Personalizationitem::SITE_FRONT);
}

$pdf->SetMargins(0, 0, 0, true);
$pdf->SetCellPadding(0);
$pdf->setCellPaddings(0,0,0,0);
$pdf->setCellMargins(0,0.4,0,0);
$page_width = $pdf->getPageWidth();
$page_height = $pdf->getPageHeight();

$pdf->SetAutoPageBreak(false, 0);

if ($perso->getLineByLine() == 0) {

	foreach ($all_items as $item){
		
		$xpos 	= $item->getXposAbsolute();		    // x-Pos der Ecke links unten		cm * 72 dpi / 2,54 = Pixel (bei 72 dpi)
		$ypos 	= $item->getYposAbsolute();		    // y-Pos der Ecke links unten
		$width	= $item->getWidth();		// Breite
		$height	= $item->getHeight();		// Hoehe
		$size	= $item->getTextsize();		// Schriftgroesse
		$pdf->SetTextColor($item->getColor_c(),$item->getColor_m(),$item->getColor_y(),$item->getColor_k());
		$spacing = $item->getSpacing();
		
		switch ($item->getJustification()){
			case 0 : $justification = "L"; break;
			case 1 : $justification = "C"; break;
			case 2 : $justification = "R"; break;
			default: $justification = "L";
		};

		$tmp_font = new PersoFont($item->getFont());
		$font = $tmp_font->getFilename();
		$pdf->SetFont($font, '', $size, $font,false);

        if ($item->getBoxtype() == 1){
            $pdf->SetXY ($xpos, $ypos, true);
            $tabin = strpos($item->getTitle(), "\\t");
            if ($tabin === false){
                $pdf->Cell($width, $height, $item->getTitle(), 0, 0, $justification, false , '', 0, true, 'T', 'B');
            } else {
                $tmp_title_arr = explode("\\t",$item->getTitle());
                $tmp_tab_space = $item->getTab();
                $pdf->Cell($width, $height, $tmp_title_arr[0], 0, 0, $justification, false , '', 0, true, 'T', 'B');
                $pdf->SetXY ($xpos+$tmp_tab_space, $ypos, true);
                $pdf->Cell($width, $height, $tmp_title_arr[1], 0, 0, $justification, false , '', 0, true, 'T', 'B');
            }
        } else {
            $pdf->SetXY ($xpos, $ypos, true);
            $tabin = strpos($item->getTitle(), "\\t");
            if ($tabin === false){
                $pdf->Cell($width, $height, $item->getTitle(), 0, 0, $justification, false , '', 0, true, 'T', 'B');
            } else {
                $tmp_title_arr = explode("\\t",$item->getTitle());
                $tmp_tab_space = $item->getTab();
                $pdf->Cell($width, $height, $tmp_title_arr[0], 0, 0, $justification, false , '', 0, true, 'T', 'B');
                $pdf->SetXY ($xpos+$tmp_tab_space, $ypos, true);
                $pdf->Cell($width, $height, $tmp_title_arr[1], 0, 0, $justification, false , '', 0, true, 'T', 'B');
            }
        }
		
		// Text einsetzen, der abgeschnitten wird
//		if ($item->getBoxtype() == 1){
//		    $pdf->SetXY ($xpos, $ypos, true);
//		    $pdf->Cell($width, $height, $item->getTitle(), 0, 0, $justification, false , '', 0, true, 'T', 'B');
//		} else {
//		    $pdf->SetXY ($xpos, $ypos, true);
//		    $pdf->Cell($width, $height, $item->getTitle(), 0, 0, $justification, false , '', 0, true, 'T', 'B');
//		}
	}

} else if ($perso->getLineByLine() == 1) {
    if ($this->getReverse() == 1){
        $all_items = Personalizationitem::getAllPersonalizationitems($perso->getId(), "id", Personalizationitem::SITE_BACK);
    } else {
        $all_items = Personalizationitem::getAllPersonalizationitems($perso->getId(), "id", Personalizationitem::SITE_FRONT);
    }
    
    foreach ($all_items as $item){ // linke tabelle faellen
        foreach(Array(0,1,2,3,4,5,6,7,8,9,10,11,12,13,14) as $group){
            if ($item->getGroup() == $group) {
                if ($item->getDependencyID() == 0) {    // erstes fix object links
                    $xpos 	= $item->getXposAbsolute();		    // x-Pos der Ecke links unten		cm * 72 dpi / 2,54 = Pixel (bei 72 dpi)
                    $ypos 	= $item->getYposAbsolute();		    // y-Pos der Ecke links unten
                    $width	= $item->getWidth();		// Breite
                    $height	= $item->getHeight();		// Hoehe
                    $size	= $item->getTextsize();		// Schriftgroesse
                    $pdf->SetTextColor($item->getColor_c(),$item->getColor_m(),$item->getColor_y(),$item->getColor_k());
                    $spacing = $item->getSpacing();
                    $tmp_font = new PersoFont($item->getFont());
                    $font = $tmp_font->getFilename();
                    $pdf->SetFont($font, '', $size, $font,false);
                     
                    switch ($item->getJustification()){
                        case 0 : $justification = "L"; break;
                        case 1 : $justification = "C"; break;
                        case 2 : $justification = "R"; break;
                        default: $justification = "L";
                    };
                     
                    if ($item->getBoxtype() == 1){
                        $pdf->SetXY ($xpos, $ypos, true);
                        $tabin = strpos($item->getTitle(), "\\t");
                        if ($tabin === false){
                            $pdf->Cell($width, $height, $item->getTitle(), 0, 0, $justification, false , '', 0, true, 'T', 'B');
                        } else {
                            $tmp_title_arr = explode("\\t",$item->getTitle());
                            $tmp_tab_space = $item->getTab();
                            $pdf->Cell($width, $height, $tmp_title_arr[0], 0, 0, $justification, false , '', 0, true, 'T', 'B');
                            $pdf->SetXY ($xpos+$tmp_tab_space, $ypos, true);
                            $pdf->Cell($width, $height, $tmp_title_arr[1], 0, 0, $justification, false , '', 0, true, 'T', 'B');
                        }
                    } else {
                        $pdf->SetXY ($xpos, $ypos, true);
                        $tabin = strpos($item->getTitle(), "\\t");
                        if ($tabin === false){
                            $pdf->Cell($width, $height, $item->getTitle(), 0, 0, $justification, false , '', 0, true, 'T', 'B');
                        } else {
                            $tmp_title_arr = explode("\\t",$item->getTitle());
                            $tmp_tab_space = $item->getTab();
                            $pdf->Cell($width, $height, $tmp_title_arr[0], 0, 0, $justification, false , '', 0, true, 'T', 'B');
                            $pdf->SetXY ($xpos+$tmp_tab_space, $ypos, true);
                            $pdf->Cell($width, $height, $tmp_title_arr[1], 0, 0, $justification, false , '', 0, true, 'T', 'B');
                        }
                    }
                    
                    foreach ($all_items as $subitem){
                        if ($subitem->getGroup() == $group) {
                            if ($subitem->getDependencyID() == $item->getId()) { // alle nicht fix objebte dieser Abhaenigkeit
                                $subwidth	= $subitem->getWidth();
                                $subheight	= $subitem->getHeight();
                                $subsize	= $subitem->getTextsize();
                                $subxpos = $subitem->getXposAbsolute();
                                $subypos = $subitem->getYposAbsolute();
                                $pdf->SetTextColor($subitem->getColor_c(),$subitem->getColor_m(),$subitem->getColor_y(),$subitem->getColor_k());
                                $spacing = $subitem->getSpacing();
                                $tmp_font = new PersoFont($subitem->getFont());
                                $font = $tmp_font->getFilename();
                                $pdf->SetFont($font, '', $subsize, $font,false);
    
                                if ($subitem->getTitle() != "spacer") {
                                    if ($subitem->getBoxtype() == 1){
                                        $pdf->SetXY ($subxpos, $subypos, true);
                                        $tabin = strpos($subitem->getTitle(), "\\t");
                                        if ($tabin === false){
                                            $pdf->Cell($subwidth, $subheight, $subitem->getTitle(), 0, 0, $justification, false , '', 0, true, 'T', 'B');
                                        } else {
                                            $tmp_title_arr = explode("\\t",$subitem->getTitle());
                                            $tmp_tab_space = $subitem->getTab();
                                            $pdf->Cell($subwidth, $subheight, $tmp_title_arr[0], 0, 0, $justification, false , '', 0, true, 'T', 'B');
                                            $pdf->SetXY ($subxpos+$tmp_tab_space, $subypos, true);
                                            $pdf->Cell($subwidth, $subheight, $tmp_title_arr[1], 0, 0, $justification, false , '', 0, true, 'T', 'B');
                                        }
                                    } else {
                                        $pdf->SetXY ($subxpos, $subypos, true);
                                        $tabin = strpos($subitem->getTitle(), "\\t");
                                        if ($tabin === false){
                                            $pdf->Cell($subwidth, $subheight, $subitem->getTitle(), 0, 0, $justification, false , '', 0, true, 'T', 'B');
                                        } else {
                                            $tmp_title_arr = explode("\\t",$subitem->getTitle());
                                            $tmp_tab_space = $subitem->getTab();
                                            $pdf->Cell($subwidth, $subheight, $tmp_title_arr[0], 0, 0, $justification, false , '', 0, true, 'T', 'B');
                                            $pdf->SetXY ($subxpos+$tmp_tab_space, $subypos, true);
                                            $pdf->Cell($subwidth, $subheight, $tmp_title_arr[1], 0, 0, $justification, false , '', 0, true, 'T', 'B');
                                        }
                                    }
                                } else {
                                    if ($spacing != 0) {
                                        $ypos 	= $ypos + $spacing;
                                    }
                                    else {
                                        $ypos 	= $ypos + $height;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
} else if ($perso->getLineByLine() == 2) {
    if ($this->getReverse() == 1){
        $all_items = Personalizationitem::getAllPersonalizationitems($perso->getId(), "id", Personalizationitem::SITE_BACK);
    } else {
        $all_items = Personalizationitem::getAllPersonalizationitems($perso->getId(), "id", Personalizationitem::SITE_FRONT);
    }
    $all_items_reverse = array_reverse ($all_items);
    
    foreach ($all_items as $item){ // linke tabelle faellen
        foreach(Array(0,1,2,3,4,5,6,7,8,9,10,11,12,13,14) as $group){
            if ($item->getGroup() == $group) {
                if ($item->getDependencyID() == 0) {    // erstes fix object links
                    $xpos 	= $item->getXposAbsolute();		    // x-Pos der Ecke links unten		cm * 72 dpi / 2,54 = Pixel (bei 72 dpi)
                    $ypos 	= $item->getYposAbsolute();		    // y-Pos der Ecke links unten
                    $width	= $item->getWidth();		// Breite
                    $height	= $item->getHeight();		// Hoehe
                    $size	= $item->getTextsize();		// Schriftgroesse
                    $pdf->SetTextColor($item->getColor_c(),$item->getColor_m(),$item->getColor_y(),$item->getColor_k());
                    $spacing = $item->getSpacing();
                    $tmp_font = new PersoFont($item->getFont());
                    $font = $tmp_font->getFilename();
                    $pdf->SetFont($font, '', $size, $font,false);
                     
                    switch ($item->getJustification()){
                        case 0 : $justification = "L"; break;
                        case 1 : $justification = "C"; break;
                        case 2 : $justification = "R"; break;
                        default: $justification = "L";
                    };
                     
                    if ($item->getBoxtype() == 1){
                        $pdf->SetXY ($xpos, $ypos, true);
                        $tabin = strpos($item->getTitle(), "\\t");
                        if ($tabin === false){
                            $pdf->Cell($width, $height, $item->getTitle(), 0, 0, $justification, false , '', 0, true, 'T', 'B');
                        } else {
                            $tmp_title_arr = explode("\\t",$item->getTitle());
                            $tmp_tab_space = $item->getTab();
                            $pdf->Cell($width, $height, $tmp_title_arr[0], 0, 0, $justification, false , '', 0, true, 'T', 'B');
                            $pdf->SetXY ($xpos+$tmp_tab_space, $ypos, true);
                            $pdf->Cell($width, $height, $tmp_title_arr[1], 0, 0, $justification, false , '', 0, true, 'T', 'B');
                        }
                    } else {
                        $pdf->SetXY ($xpos, $ypos, true);
                        $tabin = strpos($item->getTitle(), "\\t");
                        if ($tabin === false){
                            $pdf->Cell($width, $height, $item->getTitle(), 0, 0, $justification, false , '', 0, true, 'T', 'B');
                        } else {
                            $tmp_title_arr = explode("\\t",$item->getTitle());
                            $tmp_tab_space = $item->getTab();
                            $pdf->Cell($width, $height, $tmp_title_arr[0], 0, 0, $justification, false , '', 0, true, 'T', 'B');
                            $pdf->SetXY ($xpos+$tmp_tab_space, $ypos, true);
                            $pdf->Cell($width, $height, $tmp_title_arr[1], 0, 0, $justification, false , '', 0, true, 'T', 'B');
                        }
                    }
                    
                    foreach ($all_items_reverse as $subitem){ 
                        if ($subitem->getGroup() == $group) {
                            if ($subitem->getDependencyID() == $item->getId()) { // alle nicht fix objebte dieser Abhaenigkeit
                                $subwidth	= $subitem->getWidth();
                                $subheight	= $subitem->getHeight();
                                $subsize	= $subitem->getTextsize();
                                $subxpos = $subitem->getXposAbsolute();
                                $subypos = $subitem->getYposAbsolute();
                                $pdf->SetTextColor($subitem->getColor_c(),$subitem->getColor_m(),$subitem->getColor_y(),$subitem->getColor_k());
                                $spacing = $subitem->getSpacing();
                                $tmp_font = new PersoFont($subitem->getFont());
                                $font = $tmp_font->getFilename();
                                $pdf->SetFont($font, '', $subsize, $font,false);
    
                                if ($subitem->getTitle() != "spacer") {
                                    if ($subitem->getBoxtype() == 1){
                                        $pdf->SetXY ($subxpos, $subypos, true);
                                        $tabin = strpos($subitem->getTitle(), "\\t");
                                        if ($tabin === false){
                                            $pdf->Cell($subwidth, $subheight, $subitem->getTitle(), 0, 0, $justification, false , '', 0, true, 'T', 'B');
                                        } else {
                                            $tmp_title_arr = explode("\\t",$subitem->getTitle());
                                            $tmp_tab_space = $subitem->getTab();
                                            $pdf->Cell($subwidth, $subheight, $tmp_title_arr[0], 0, 0, $justification, false , '', 0, true, 'T', 'B');
                                            $pdf->SetXY ($subxpos+$tmp_tab_space, $subypos, true);
                                            $pdf->Cell($subwidth, $subheight, $tmp_title_arr[1], 0, 0, $justification, false , '', 0, true, 'T', 'B');
                                        }
                                    } else {
                                        $pdf->SetXY ($subxpos, $subypos, true);
                                        $tabin = strpos($subitem->getTitle(), "\\t");
                                        if ($tabin === false){
                                            $pdf->Cell($subwidth, $subheight, $subitem->getTitle(), 0, 0, $justification, false , '', 0, true, 'T', 'B');
                                        } else {
                                            $tmp_title_arr = explode("\\t",$subitem->getTitle());
                                            $tmp_tab_space = $subitem->getTab();
                                            $pdf->Cell($subwidth, $subheight, $tmp_title_arr[0], 0, 0, $justification, false , '', 0, true, 'T', 'B');
                                            $pdf->SetXY ($subxpos+$tmp_tab_space, $subypos, true);
                                            $pdf->Cell($subwidth, $subheight, $tmp_title_arr[1], 0, 0, $justification, false , '', 0, true, 'T', 'B');
                                        }
                                    }
                                } else {
                                    if ($spacing != 0) {
                                        $ypos 	= $ypos - $spacing;
                                    }
                                    else {
                                        $ypos 	= $ypos - $height;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}
?>