<?
require_once('libs/basic/translator/translator.class.php');
function getSaveMessage($r)
{
  global $_LANG;
   if ($r === true)
      $msg = '<span class="ok">'.$_LANG->get('Daten wurden gespeichert').'</span>';
   else
      $msg = '<span class="error">'.$_LANG->get('Ein Fehler ist aufgetreten').'</span>';
   return $msg;
}

function getBaseDir() {
    if ($_SERVER["SERVER_PORT"] == 443)
        $str = "https://";
    else 
        $str = "http://";
    
    $str .= $_SERVER["SERVER_NAME"];
    $str .= $_SERVER["SCRIPT_NAME"];
    $str = substr($str, 0, strrpos($str, "/"));
    return $str;
}

function getDayNameForDayOfWeek($day = -1){
	global $_LANG;
    $daynames[0] = $_LANG->get('So');
    $daynames[1] = $_LANG->get('Mo');
    $daynames[2] = $_LANG->get('Di');
    $daynames[3] = $_LANG->get('Mi');
    $daynames[4] = $_LANG->get('Do');
    $daynames[5] = $_LANG->get('Fr');
    $daynames[6] = $_LANG->get('Sa');
    $daynames[7] = $_LANG->get('So');

    if ($day >= 0)
        return $daynames[$day];
    else
        return $daynames[date('n',$this->date)];
}

function isWeekend($day = -1)
{
    if ($day == 0 || $day == 6 || $day == 7)
        return true;
}

function convertDate($date)
{
    if($date != "" && $date != "0000-00-00")
    {
        $date = explode("-", $date);
        return $date[2].".".$date[1].".".$date[0];
    } else 
        return "";
}

function getRowColor($x)
{
    if($x % 2 == 0)
        return "color1";
    else
        return "color2";
}

function printPrice($val, $nkst = 2) {
    global $_USER;
    return(number_format($val, $nkst, $_USER->getClient()->getDecimal(), $_USER->getClient()->getThousand()));
}

function printBigInt($val)
{
    global $_USER;
    return(number_format($val, 0, '', $_USER->getClient()->getThousand()));
}

function getOrderStatus($status, $formated = false)
{
	global $_LANG;
    if($formated)
    {
        switch((int)$status)
        {
            case 1: return "<font color='red'><b>".$_LANG->get('Vorgang angelegt')."</b></font>"; break;
            case 2: return "<font color='darkorange'><b>".$_LANG->get('Vorgang gesendet')."</b></font>"; break;
            case 3: return "<font color='gray'><b>".$_LANG->get('Vorgang angenommen')."</b></font>"; break;
            case 4: return "<font color='blue'><b>".$_LANG->get('In Produktion')."</b></font>"; break;
            case 5: return "<font color='green'><b>".$_LANG->get('Erledigt')."</b></font>"; break;
            default: return "<font color='red'><b>".$_LANG->get('Unbekannt')."</b></font>"; break;
        }
    }
    else
    {
        switch((int)$status)
        {
            case 1: return $_LANG->get('Vorgang angelegt'); break;
            case 2: return $_LANG->get('Vorgang gesendet'); break;
            case 3: return $_LANG->get('Vorgang angenommen'); break;
            case 4: return $_LANG->get('In Produktion'); break;
            case 5: return $_LANG->get('Erledigt'); break;
            default: return $_LANG->get('Unbekannt'); break;
        }
    }
}

function getTicketStatus1($status, $formated = false){ 		// Allgemein
	global $_LANG;
	if($formated){
		switch((int)$status){
			case 0: return "<font color='black'><b>".$_LANG->get('Aus')."</b></font>"; break;
			case 1: return "<font color='green'><b>".$_LANG->get('Offen')."</b></font>"; break;
			case 2: return "<font color='orange'><b>".$_LANG->get('R&uuml;cksprache')."</b></font>"; break;
			case 3: return "<font color='blue'><b>".$_LANG->get('Chat')."</b></font>"; break;
			case 4: return "<font color='blue'><b>".$_LANG->get('TODO')."</b></font>"; break;
			case 11: return "<font color='gray'><b>".$_LANG->get('Archiv')."</b></font>"; break;
			default: return "<font color='black'><b>".$_LANG->get('Unbekannt')."</b></font>"; break;
		}
	} else {
		switch((int)$status){
			case 0: return $_LANG->get('Aus'); break;
			case 1: return $_LANG->get('Offen'); break;
			case 2: return $_LANG->get('R&uuml;cksprache'); break;
			case 3: return $_LANG->get('Chat'); break;
			case 4: return $_LANG->get('TODO'); break;
			case 11: return $_LANG->get('Archiv'); break;
			default: return $_LANG->get('Unbekannt'); break;
		}
	}
}

function getTicketStatus2($status, $formated = false){	// Produktion
	global $_LANG;
	if($formated){
		switch((int)$status){
			case 0: return "<font color='black'><b>".$_LANG->get('Aus')."</b></font>"; break;
			case 1: return "<font color='green'><b>".$_LANG->get('Erledigt')."</b></font>"; break;
			case 2: return "<font color='orange'><b>".$_LANG->get('Job')."</b></font>"; break;
			case 3: return "<font color='purple'><b>".$_LANG->get('R&uuml;ckspr.-Kunde')."</b></font>"; break;
			case 4: return "<font color='purple'><b>".$_LANG->get('R&uuml;ckspr.-Intern')."</b></font>"; break;
			case 5: return "<font color='purple'><b>".$_LANG->get('In Arbeit')."</b></font>"; break;
			case 6: return "<font color='purple'><b>".$_LANG->get('Wartend')."</b></font>"; break;
			case 7: return "<font color='purple'><b>".$_LANG->get('TODO')."</b></font>"; break;
			default: return "<font color='purple'><b>".$_LANG->get('Unbekannt')."</b></font>"; break;
		}
	} else {
		switch((int)$status){
			case 0: return $_LANG->get('Aus'); break;
			case 1: return $_LANG->get('Erledigt'); break;
			case 2: return $_LANG->get('Job'); break;
			case 3: return $_LANG->get('R&uuml;ckspr.-Kunde'); break;
			case 4: return $_LANG->get('R&uuml;ckspr.-Intern'); break;
			case 5: return $_LANG->get('In Arbeit'); break;
			case 6: return $_LANG->get('Wartend'); break;
			case 7: return $_LANG->get('TODO'); break;
			default: return $_LANG->get('Unbekannt'); break;
		}
	}
}

function getTicketStatus3($status, $formated = false){		// Vertrieb
	global $_LANG;
	if($formated){
		switch((int)$status){
			case 0: return "<font color='black'><b>".$_LANG->get('Aus')."</b></font>"; break;
			case 1: return "<font color='green'><b>".$_LANG->get('Erledigt')."</b></font>"; break;
			case 3: return "<font color='peru'><b>".$_LANG->get('Aquise')."</b></font>"; break;
			case 4: return "<font color='peru'><b>".$_LANG->get('Wiedervorlage')."</b></font>"; break;
			case 5: return "<font color='peru'><b>".$_LANG->get('Kunden anrufen')."</b></font>"; break;
			case 6: return "<font color='peru'><b>".$_LANG->get('R&uuml;ckspr.-Kunde')."</b></font>"; break;
			case 7: return "<font color='peru'><b>".$_LANG->get('R&uuml;ckspr.-Intern')."</b></font>"; break;
			case 8: return "<font color='peru'><b>".$_LANG->get('Termin beim Kunden')."</b></font>"; break;
			case 9: return "<font color='peru'><b>".$_LANG->get('Termin bei KDM')."</b></font>"; break;
			case 10: return "<font color='peru'><b>".$_LANG->get('TODO')."</b></font>"; break;
			case 11: return "<font color='peru'><b>".$_LANG->get('Zur&uuml;ckgestellt')."</b></font>"; break;
			default: return "<font color='peru'><b>".$_LANG->get('Unbekannt')."</b></font>"; break;
		}
	} else {
		switch((int)$status){
			case 0: return $_LANG->get('Aus'); break;
			case 1: return $_LANG->get('Erledigt'); break;
			case 3: return $_LANG->get('Aquise'); break;
			case 4: return $_LANG->get('Wiedervorlage'); break;
			case 5: return $_LANG->get('Kunden anrufen'); break;
			case 6: return $_LANG->get('R&uuml;ckspr.-Kunde'); break;
			case 7: return $_LANG->get('R&uuml;ckspr.-Intern'); break;
			case 8: return $_LANG->get('Termin beim Kunden'); break;
			case 9: return $_LANG->get('Termin bei KDM'); break;
			case 10: return $_LANG->get('TODO'); break;
			case 11: return $_LANG->get('Zur&uuml;ckgestellt'); break;
			default: return $_LANG->get('Unbekannt'); break;
		}
	}
}

function getTicketStatus4($status, $formated = false){		// Kunde 
	global $_LANG;
	if($formated){
		switch((int)$status){
			case 0: return "<font color='black'><b>".$_LANG->get('Aus')."</b></font>"; break;
			case 1: return "<font color='green'><b>".$_LANG->get('Erledigt')."</b></font>"; break;
			case 2: return "<font color='orange'><b>".$_LANG->get('R&uuml;cksprache')."</b></font>"; break;
			case 3: return "<font color='saddlebrown'><b>".$_LANG->get('In Arbeit')."</b></font>"; break;
			case 4: return "<font color='saddlebrown'><b>".$_LANG->get('Vor Kunde verbergen')."</b></font>"; break;
			default: return "<font color='saddlebrown'><b>".$_LANG->get('Unbekannt')."</b></font>"; break;
		}
	} else {
		switch((int)$status){
			case 0: return $_LANG->get('Aus'); break;
			case 1: return $_LANG->get('Erledigt'); break;
			case 2: return $_LANG->get('R&uuml;cksprache'); break;
			case 3: return $_LANG->get('In Arbeit'); break;
			case 4: return $_LANG->get('Vor Kunde verbergen'); break;
			default: return $_LANG->get('...'); break;
		}
	}
}

function getTicketcommentStatus($status, $formated = false){
	global $_LANG;
	if($formated){
		switch((int)$status){
			case 1: return "<font color='red'><b>".$_LANG->get('Neu')."</b></font>"; break;
			case 2: return "<font color='darkorange'><b>".$_LANG->get('In Arbeit')."</b></font>"; break;
			case 3: return "<font color='blue'><b>".$_LANG->get('Erledigt')."</b></font>"; break;
			default: return "<font color='black'><b>".$_LANG->get('...')."</b></font>"; break;
		}
	} else {
		switch((int)$status){
			case 1: return $_LANG->get('Neu'); break;
			case 2: return $_LANG->get('In Arbeit'); break;
			case 3: return $_LANG->get('Erledigt'); break;
			default: return $_LANG->get('...'); break;
		}
	}
}

/**
 * @param 	string $name the of the request variable to fetch.
 * @param 	string $default the value that is returned if the
 * 			requested variable is not set
 * @return 	the value of the requested variable or,
 * 			if it is not set,the specified default value
 */
function getRequestVarOr($name, $default) {
	if(isset($_POST[$name])) {
		return $_POST[$name];
	}

	if(isset($_GET[$name])) {
		return $_GET[$name];
	}

	return $default;
}

/**
 * @param string $str the string to decode
 * @return the decoded string or false
 */
function utf7_decode($str){
	if(!$str){
		return false;
	}
	$str = str_replace(
			array("&AOQ-", "&APY-", "&APw-", "&AMQ-", "&ARN-", "&ANw-", "&AN8-"),
			array("ä", "ö", "ü", "Ä", "Ö", "Ü", "ß"),
			$str
	);

	return $str;
}

/**
 * Formatierung fuer die Ausgabe eines Strings in XML-Dokumenten (Umlaute und Sonderzeichen)
 *
 * @param String $string
 * @return string
 */
function formatStringForXML($string){

	//$string = iconv('UTF-8', 'ISO-8859-1', $string);

	$string = str_replace("'", "&apos;", $string);
	$string = str_replace("<", "&lt;", $string);
	$string = str_replace(">", "&gt;", $string);
	$string = str_replace('"', "&quot", $string);
	$string = str_replace("&", "&amp;", $string);
	$string = str_replace("Ä", "Ae", $string);			// "&#196;", $string);
	$string = str_replace("Ö", "Oe", $string);			// "&#214;", $string);
	$string = str_replace("Ü", "Ue", $string);			// "&#220;", $string);
	$string = str_replace("ä", "ae", $string);			// "&#228;", $string);
	$string = str_replace("ö", "oe", $string);			// "&#246;", $string);
	$string = str_replace("ü", "ue", $string);			// "&#252;", $string);
	$string = str_replace("ß", "ss", $string);

	return $string;
}

?>