<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */

class QuickMove{
    private $items = [];

    public function addItem($label, $link, $onclick = null, $icon = 'glyphicon-minus', $danger = false)
    {
        $item = '<a href="'.$link.'" ';
        if ($danger == true)
            $item .= ' class="quickmove_item_danger" ';
        else
            $item .= ' class="quickmove_item" ';
        if ($onclick != null)
            $item .= ' onclick="'.$onclick.'" ';
        $item .= ' ><span class="glyphicon '.$icon.'" aria-hidden="true"></span>'.$label.'</a>';
        $this->items[] = $item;
    }

    public function generate()
    {
        $qm = '<div id="quickmove"><div class="quickmove">'; // <div class="label">Quick Move</div>
        foreach ($this->items as $item) {
            $qm .= $item;
        }
        $qm .= '</div></div>';
        return $qm;
    }
}