<?
//require_once('libs/basic/translator/translator.class.php');
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
			case 1: return "<font color='FF6E6E'><b>".$_LANG->get('angelegt')."</b></font>"; break;
			case 2: return "<font color='dFFB92C'><b>".$_LANG->get('gesendet')."</b></font>"; break;
			case 3: return "<font color='FFDA3E'><b>".$_LANG->get('angenommen')."</b></font>"; break;
			case 4: return "<font color='FB71FF'><b>".$_LANG->get('In Produktion')."</b></font>"; break;
			case 5: return "<font color='0092FF'><b>".$_LANG->get('Versandbereit')."</b></font>"; break;
			case 6: return "<font color='00E7FF'><b>".$_LANG->get('Ware versand')."</b></font>"; break;
			case 7: return "<font color='71FF9C'><b>".$_LANG->get('Erledigt')."</b></font>"; break;
			default: return "<font color='6e6e6d'><b>".$_LANG->get('Unbekannt')."</b></font>"; break;
		}
    }
    else
    {
        switch((int)$status)
        {
            case 1: return $_LANG->get('angelegt'); break;
            case 2: return $_LANG->get('gesendet'); break;
            case 3: return $_LANG->get('angenommen'); break;
            case 4: return $_LANG->get('In Produktion'); break;
            case 5: return $_LANG->get('Versandbereit'); break;
			case 6: return $_LANG->get('Ware versand'); break;
			case 7: return $_LANG->get('Erledigt'); break;
            default: return $_LANG->get('Unbekannt'); break;
        }
    }
}

function getTicketStatus1($status, $formated = false){ 		// Allgemein
	global $_LANG;
	if($formated){
		switch((int)$status){
			case 0: return "<font color='71FF9C'><b>".$_LANG->get('Aus')."</b></font>"; break;
			case 1: return "<font color='FFDA3E'><b>".$_LANG->get('Offen')."</b></font>"; break;
			case 2: return "<font color='dFFB92C'><b>".$_LANG->get('R&uuml;cksprache')."</b></font>"; break;
			case 3: return "<font color='92CEFF'><b>".$_LANG->get('Chat')."</b></font>"; break;
			case 4: return "<font color='B71FF'><b>".$_LANG->get('TODO')."</b></font>"; break;
			case 11: return "<font color='776358'><b>".$_LANG->get('Archiv')."</b></font>"; break;
			default: return "<font color='6e6e6d'><b>".$_LANG->get('Unbekannt')."</b></font>"; break;
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

function tofloat($num) {
    $dotPos = strrpos($num, '.');
    $commaPos = strrpos($num, ',');
    $sep = (($dotPos > $commaPos) && $dotPos) ? $dotPos :
    ((($commaPos > $dotPos) && $commaPos) ? $commaPos : false);

    if (!$sep) {
        return floatval(preg_replace("/[^0-9]/", "", $num));
    }

    return floatval(
        preg_replace("/[^0-9]/", "", substr($num, 0, $sep)) . '.' .
        preg_replace("/[^0-9]/", "", substr($num, $sep+1, strlen($num)))
    );
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

function percentage($val1, $val2, $precision)
{
    $division = $val1 / $val2;

    $res = $division * 100;

    $res = round($res, $precision);

    return $res;
}

function round_up ( $value, $precision ) { 
    $pow = pow ( 10, $precision ); 
    return ( ceil ( $pow * $value ) + ceil ( $pow * $value - ceil ( $pow * $value ) ) ) / $pow; 
} 

function GetDays($sStartDate, $sEndDate, $format = "d.m.Y"){
    // Firstly, format the provided dates.
    // This function works best with YYYY-MM-DD
    // but other date formats will work thanks
    // to strtotime().
    $sStartDate = date('Y-m-d', strtotime($sStartDate));
    $sEndDate = date('Y-m-d', strtotime($sEndDate));

    // Start the variable off with the start date
    $aDays[] = date($format,strtotime($sStartDate));

    // Set a 'temp' variable, sCurrentDate, with
    // the start date - before beginning the loop
    $sCurrentDate = $sStartDate;

    // While the current date is less than the end date
    while($sCurrentDate < $sEndDate){
        // Add a day to the current date
        $sCurrentDate = date('Y-m-d', strtotime("+1 day", strtotime($sCurrentDate)));

        // Add this new day to the aDays array
        $aDays[] = date($format,strtotime($sCurrentDate));
    }

    // Once the loop has finished, return the
    // array of days.
    return $aDays;
}

function GetMonths($sStartDate, $sEndDate){

	$sStartDate = date('Y-m-d', strtotime($sStartDate));
	$sEndDate = date('Y-m-d', strtotime($sEndDate));
	// dem $aDays Array das erste Datum hinzufügen
	$aDays[] = date('Y-m-d',strtotime($sStartDate));
	// $sCurrentDate auf das Startdatum setzen
	$sCurrentDate = $sStartDate;
	// Schleife die solange läuft bis das $sCurrentDate nicht mehr kleiner als das Enddatum ist
	while($sCurrentDate < $sEndDate){
		// auf $sCurrentDate +1 Monat draufrechnen
		$sCurrentDate = date('Y-m-d', strtotime("+1 month", strtotime($sCurrentDate)));
		// das $sCurrentDate dem $aDays Array hinzufügen
		$aDays[] = date('Y-m-d',strtotime($sCurrentDate));
	}
	return $aDays;
}

function GetYears($sStartDate, $sEndDate){

	$sStartDate = date('Y-m-d', strtotime($sStartDate));
	$sEndDate = date('Y-m-d', strtotime($sEndDate));
	// dem $aDays Array das erste Datum hinzufügen
	$aDays[] = date('Y-m-d',strtotime($sStartDate));
	// $sCurrentDate auf das Startdatum setzen
	$sCurrentDate = $sStartDate;
	// Schleife die solange läuft bis das $sCurrentDate nicht mehr kleiner als das Enddatum ist
	while($sCurrentDate < $sEndDate){
		// auf $sCurrentDate +1 Monat draufrechnen
		$sCurrentDate = date('Y-m-d', strtotime("+1 year", strtotime($sCurrentDate)));
		// das $sCurrentDate dem $aDays Array hinzufügen
		$aDays[] = date('Y-m-d',strtotime($sCurrentDate));
	}
	return $aDays;
}


function break_array($array, $page_size) {

    $arrays = array();
    $i = 0;

    foreach ($array as $index => $item) {
        if ($i++ % $page_size == 0) {
            $arrays[] = array();
            $current = & $arrays[count($arrays)-1];
        }
        $current[] = $item;
    }

    return $arrays;
}

function prettyPrint($a) {
	echo "<pre>";
	print_r($a);
	echo "</pre>";
}

function evalBoolean($val){
    return ($val) ? 'true' : 'false';
}

function generateRandomString($length = 10) {
	return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!$%&/()=?', ceil($length/strlen($x)) )),1,$length);
}

/**
 * Returns $value or 0 if it's not set OR null
 * @param null $value
 * @return int
 */
function setOrZero($value = null){
	if ($value == null || !isset($value)){
		return 0;
	}
	return $value;
}

function json_error_handling(){
	switch(json_last_error()) {
		case JSON_ERROR_NONE:
			echo ' - Keine Fehler';
			break;
		case JSON_ERROR_DEPTH:
			echo ' - Maximale Stacktiefe überschritten';
			break;
		case JSON_ERROR_STATE_MISMATCH:
			echo ' - Unterlauf oder Nichtübereinstimmung der Modi';
			break;
		case JSON_ERROR_CTRL_CHAR:
			echo ' - Unerwartetes Steuerzeichen gefunden';
			break;
		case JSON_ERROR_SYNTAX:
			echo ' - Syntaxfehler, ungültiges JSON';
			break;
		case JSON_ERROR_UTF8:
			echo ' - Missgestaltete UTF-8 Zeichen, möglicherweise fehlerhaft kodiert';
			break;
		default:
			echo ' - Unbekannter Fehler';
			break;
	}
}

/**
 * clear output buffer / prettyprint var / die...
 * @param $var
 */
function dd($var){
	ob_clean();
	prettyPrint($var);
	die();
}


function LogMyError($message){
    $file = "app.log";
    if ($message != null && $message != ""){
        $date = date('Y-m-d H:i:s');
        $message = $date . ': ' . $message . "\r\n";
        if (file_exists($file))
            file_put_contents($file, $message, FILE_APPEND | LOCK_EX);
        else
            file_put_contents($file, $message);
    }
}


/**
 * @param array $data
 * @param SimpleXMLElement $xml_data
 */
function array_to_xml( $data, &$xml_data ) {
	foreach( $data as $key => $value ) {
		if( is_numeric($key) ){
			$key = 'item'.$key; //dealing with <0/>..<n/> issues
		}
		if( is_array($value) ) {
			$subnode = $xml_data->addChild($key);
			array_to_xml($value, $subnode);
		} else {
			$xml_data->addChild("$key",htmlspecialchars("$value"));
		}
	}
}

function roundPrice($val, $decimals = 2){
	return round($val, $decimals);
}

function gcd($a,$b) {
    return ($a % $b) ? gcd($b,$a % $b) : $b;
}