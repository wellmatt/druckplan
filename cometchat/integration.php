<?php
$mainpath = str_replace("cometchat", "", __DIR__);
require_once($mainpath."/config.php");

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

/* ADVANCED */

define('SET_SESSION_NAME','');			// Session name
define('SWITCH_ENABLED','1');
define('INCLUDE_JQUERY','1');
define('FORCE_MAGIC_QUOTES','0');

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

/* DATABASE */

// DO NOT EDIT DATABASE VALUES BELOW
// DO NOT EDIT DATABASE VALUES BELOW
// DO NOT EDIT DATABASE VALUES BELOW

define('DB_SERVER',				$_CONFIG->db->host				);
define('DB_PORT',				"3306"					);
define('DB_USERNAME',			$_CONFIG->db->user					);
define('DB_PASSWORD',			$_CONFIG->db->pass				);
define('DB_NAME',				$_CONFIG->db->name				);
if(defined('USE_CCAUTH') && USE_CCAUTH == '0'){
define('TABLE_PREFIX',			""					);
define('DB_USERTABLE',			"user"					);
define('DB_USERTABLE_USERID',		"id"				);
define('DB_USERTABLE_NAME',		"login"				);
define('DB_AVATARTABLE',		" "					);
define('DB_AVATARFIELD',		" ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_USERID." ");

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

/* FUNCTIONS */


	function getUserID() {
		$userid = 0;
		if (!empty($_SESSION['basedata']) && $_SESSION['basedata'] != 'null') {
			$_REQUEST['basedata'] = $_SESSION['basedata'];
		}

		if (!empty($_REQUEST['basedata'])) {

			if (function_exists('mcrypt_encrypt') && defined('ENCRYPT_USERID') && ENCRYPT_USERID == '1') {
				$key = "";
				if( defined('KEY_A') && defined('KEY_B') && defined('KEY_C') ){
					$key = KEY_A.KEY_B.KEY_C;
				}
				$uid = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key), base64_decode(rawurldecode($_REQUEST['basedata'])), MCRYPT_MODE_CBC, md5(md5($key))), "\0");
				if (intval($uid) > 0) {
					$userid = $uid;
				}
			} else {
				$userid = $_REQUEST['basedata'];
			}
		}
		if (!empty($_SESSION['userid'])) {
			$userid = $_SESSION['userid'];
		}

		$userid = intval($userid);
		return $userid;
	}

	function chatLogin($userName,$userPass) {

		$userid = 0;
		global $guestsMode;
		if(!empty($_REQUEST['guest_login']) && $userPass == "CC^CONTROL_GUEST" && $guestsMode == 1) {
			$sql = ("INSERT INTO `cometchat_guests` (`name`, `lastactivity`) VALUES('".mysqli_real_escape_string($GLOBALS['dbh'], $userName)."','".getTimeStamp()."')");
			$query = mysqli_query($GLOBALS['dbh'], $sql);
			$userid = mysqli_insert_id($GLOBALS['dbh']);

			if (isset($_REQUEST['callbackfn']) && $_REQUEST['callbackfn'] == 'mobileapp') {
                $sql = ("insert into cometchat_status (userid,isdevice) values ('".mysqli_real_escape_string($GLOBALS['dbh'],$userid)."','1') on duplicate key update isdevice = '1'");
                mysqli_query($GLOBALS['dbh'], $sql);
            }
		} else {
			if (filter_var($userName, FILTER_VALIDATE_EMAIL)) {
				$sql = ("SELECT * FROM `".TABLE_PREFIX.DB_USERTABLE."` WHERE email = '".mysqli_real_escape_string($GLOBALS['dbh'],$userName)."'");
			} else {
				$sql = ("SELECT * FROM `".TABLE_PREFIX.DB_USERTABLE."` WHERE login = '".mysqli_real_escape_string($GLOBALS['dbh'],$userName)."'");
			}
			$result = mysqli_query($GLOBALS['dbh'],$sql);
			$row = mysqli_fetch_assoc($result);
// 			$salted_password = md5($row1['value'].$userPass.$row['salt']);
	        $salted_password = md5($userPass);
			if($row['password'] == $salted_password) {
				$userid = $row['id'];
                if (isset($_REQUEST['callbackfn']) && $_REQUEST['callbackfn'] == 'mobileapp') {
                    $sql = ("insert into cometchat_status (userid,isdevice) values ('".mysqli_real_escape_string($GLOBALS['dbh'],$userid)."','1') on duplicate key update isdevice = '1'");
                    mysqli_query($GLOBALS['dbh'], $sql);
                }
			}
		}
		if($userid && function_exists('mcrypt_encrypt') && defined('ENCRYPT_USERID') && ENCRYPT_USERID == '1') {
			$key = "";
				if( defined('KEY_A') && defined('KEY_B') && defined('KEY_C') ){
					$key = KEY_A.KEY_B.KEY_C;
				}
			$userid = rawurlencode(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $userid, MCRYPT_MODE_CBC, md5(md5($key)))));
		}

		return $userid;
	}

	function getFriendsList($userid,$time) {
		global $hideOffline;
		$offlinecondition = '';
// 		$sql = ("select DISTINCT ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_USERID." userid, ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_NAME." username, ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_USERID." link, ".DB_AVATARFIELD." avatar, cometchat_status.lastactivity lastactivity, cometchat_status.status, cometchat_status.message, cometchat_status.isdevice from ".TABLE_PREFIX."friends join ".TABLE_PREFIX.DB_USERTABLE." on  ".TABLE_PREFIX."friends.toid = ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_USERID." left join cometchat_status on ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_USERID." = cometchat_status.userid ".DB_AVATARTABLE." where ".TABLE_PREFIX."friends.fromid = '".mysqli_real_escape_string($GLOBALS['dbh'],$userid)."' order by username asc");
	    $sql = ("select DISTINCT ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_USERID." userid, concat(".TABLE_PREFIX.DB_USERTABLE.".user_firstname,' ',".TABLE_PREFIX.DB_USERTABLE.".user_lastname) username, ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_USERID." link, ".DB_AVATARFIELD." avatar, cometchat_status.lastactivity lastactivity, cometchat_status.status, cometchat_status.message, cometchat_status.isdevice from ".TABLE_PREFIX."friends join ".TABLE_PREFIX.DB_USERTABLE." on  ".TABLE_PREFIX."friends.toid = ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_USERID." left join cometchat_status on ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_USERID." = cometchat_status.userid ".DB_AVATARTABLE." where ".TABLE_PREFIX."friends.fromid = '".mysqli_real_escape_string($GLOBALS['dbh'],$userid)."' AND ".TABLE_PREFIX.DB_USERTABLE.".user_level > 0  order by username asc");
		if ((defined('MEMCACHE') && MEMCACHE <> 0) || DISPLAY_ALL_USERS == 1) {
			if ($hideOffline) {
				$offlinecondition = "where ((cometchat_status.lastactivity > (".mysqli_real_escape_string($GLOBALS['dbh'],$time)."-".((ONLINE_TIMEOUT)*2).")) OR cometchat_status.isdevice = 1) and (cometchat_status.status IS NULL OR cometchat_status.status <> 'invisible' OR cometchat_status.status <> 'offline')";
			}
// 			$sql = ("select ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_USERID." userid, ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_NAME." username, ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_USERID." link, ".DB_AVATARFIELD." avatar, cometchat_status.lastactivity lastactivity, cometchat_status.status, cometchat_status.message, cometchat_status.isdevice from  ".TABLE_PREFIX.DB_USERTABLE."   left join cometchat_status on ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_USERID." = cometchat_status.userid ".DB_AVATARTABLE." ".$offlinecondition ." order by username asc");
		    $sql = ("select ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_USERID." userid, concat(".TABLE_PREFIX.DB_USERTABLE.".user_firstname,' ',".TABLE_PREFIX.DB_USERTABLE.".user_lastname) username, ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_USERID." link, ".DB_AVATARFIELD." avatar, cometchat_status.lastactivity lastactivity, cometchat_status.status, cometchat_status.message, cometchat_status.isdevice from  ".TABLE_PREFIX.DB_USERTABLE."   left join cometchat_status on ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_USERID." = cometchat_status.userid ".DB_AVATARTABLE." ".$offlinecondition ." WHERE ".TABLE_PREFIX.DB_USERTABLE.".user_level > 0 order by username asc");
		}

		return $sql;
	}

	function getFriendsIds($userid) {

		$sql = ("SELECT toid as friendid FROM `friends` WHERE status =1 and fromid = '".mysqli_real_escape_string($GLOBALS['dbh'],$userid)."' union SELECT fromid as myfrndids FROM `friends` WHERE status = 1 and toid = '".mysqli_real_escape_string($GLOBALS['dbh'],$userid)."'");

		return $sql;
	}

	function getUserDetails($userid) {
// 		$sql = ("select ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_USERID." userid, ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_NAME." username, ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_USERID." link, ".DB_AVATARFIELD." avatar, cometchat_status.lastactivity lastactivity, cometchat_status.status, cometchat_status.message, cometchat_status.isdevice from ".TABLE_PREFIX.DB_USERTABLE." left join cometchat_status on ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_USERID." = cometchat_status.userid ".DB_AVATARTABLE." where ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_USERID." = '".mysqli_real_escape_string($GLOBALS['dbh'],$userid)."'");
	    $sql = ("select ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_USERID." userid, concat(".TABLE_PREFIX.DB_USERTABLE.".user_firstname,' ',".TABLE_PREFIX.DB_USERTABLE.".user_lastname) username, ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_USERID." link, ".DB_AVATARFIELD." avatar, cometchat_status.lastactivity lastactivity, cometchat_status.status, cometchat_status.message, cometchat_status.isdevice from ".TABLE_PREFIX.DB_USERTABLE." left join cometchat_status on ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_USERID." = cometchat_status.userid ".DB_AVATARTABLE." where ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_USERID." = '".mysqli_real_escape_string($GLOBALS['dbh'],$userid)."'");

		return $sql;
	}

	function updateLastActivity($userid) {
		$sql = ("insert into cometchat_status (userid,lastactivity) values ('".mysqli_real_escape_string($GLOBALS['dbh'],$userid)."','".getTimeStamp()."') on duplicate key update lastactivity = '".getTimeStamp()."'");
		return $sql;
	}

	function getUserStatus($userid) {
		 $sql = ("select cometchat_status.message, cometchat_status.status from cometchat_status where userid = '".mysqli_real_escape_string($GLOBALS['dbh'],$userid)."'");

		 return $sql;
	}

	function fetchLink($link) {
	        return '';
	}

	function getAvatar($image) {
	        return BASE_URL.'images/noavatar.png';
	}

	function getTimeStamp() {
		return time();
	}

	function processTime($time) {
		return $time;
	}

	if (!function_exists('getLink')) {
	  	function getLink($userid) { return fetchLink($userid); }
	}

	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	/* HOOKS */

	function hooks_statusupdate($userid,$statusmessage) {

	}

	function hooks_forcefriends() {

	}

	function hooks_activityupdate($userid,$status) {

	}

	function hooks_message($userid,$to,$unsanitizedmessage) {

	}

	function hooks_updateLastActivity($userid) {

	}
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

/* LICENSE */

include_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'license.php');
$x = "\x62a\x73\x656\x34\x5fd\x65c\157\144\x65";
eval($x('JHI9ZXhwbG9kZSgnLScsJGxpY2Vuc2VrZXkpOyRwXz0wO2lmKCFlbXB0eSgkclsyXSkpJHBfPWludHZhbChwcmVnX3JlcGxhY2UoIi9bXjAtOV0vIiwnJywkclsyXSkpOw'));

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
