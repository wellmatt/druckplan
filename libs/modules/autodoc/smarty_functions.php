<?php
// ----------------------------------------------------------------------------------
// Author: Klein Druck+Medien GmbH
// Updated: 23.12.2014
// Copyright: Klein Druck+Medien GmbH - All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------

// Vorbearbeitungsfunktion für Smarty

function ckeditor_to_smarty($file)
{
    $datei = '';
    $datei = implode("", file($file));
    $datei = str_replace(array('<!--','-->'),array('',''),$datei);
    $datei = str_replace('&gt;','>',$datei);
    return $datei;
}

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     smarty_functions.php
 * Type:     function
 * Name:     printPrice
 * Purpose:  formatted price
 * -------------------------------------------------------------
 */
function smarty_function_printPrice($params, Smarty_Internal_Template $template)
{    
    if (empty($params['var'])) {
        trigger_error("missing 'var' parameter");
        return "";
    }
    return printPrice($params['var']);
}

function smarty_function_replace_ln($params, Smarty_Internal_Template $template)
{
    if (empty($params['var'])) {
        trigger_error("missing 'var' parameter");
        return "";
    }
    return str_replace("\n","<br />",$params['var']);
}

function smarty_function_trim($params, Smarty_Internal_Template $template)
{
    if (empty($params['var'])) {
        trigger_error("missing 'var' parameter");
        return "";
    }
    return trim($params['var']);
}

?>