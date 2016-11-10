<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */
require_once('vendor/simpleCalDAV/SimpleCalDAVClient.php');

class CalDavService {

    public static function createUserPrincipals()
    {
        global $DB;

        $users = User::getAllUser();

        foreach ($users as $user) {
            $sql = "SELECT * FROM sabre_principals WHERE uri LIKE '%{$user->getLogin()}%'";
            $res = $DB->select($sql);

            if (count($res) < 3){
                $removesql = "DELETE * FROM sabre_principals WHERE uri LIKE '%{$user->getLogin()}%'";
                $DB->no_result($removesql);

                $insertsql1 = "INSERT INTO sabre_principals (uri, email, displayname) VALUES ('principals/{$user->getLogin()}','{$user->getEmail()}','{$user->getNameAsLine()}')";
                $insertsql2 = "INSERT INTO sabre_principals (uri, email, displayname) VALUES ('principals/{$user->getLogin()}/calendar-proxy-read', NULL, NULL)";
                $insertsql3 = "INSERT INTO sabre_principals (uri, email, displayname) VALUES ('principals/{$user->getLogin()}/calendar-proxy-write', NULL, NULL)";

                $DB->no_result($insertsql1);
                $DB->no_result($insertsql2);
                $DB->no_result($insertsql3);
            }
        }
    }

    public static function createUserCalendars()
    {
        global $DB;

        $users = User::getAllUser();

        foreach ($users as $user) {
            $sql = "SELECT * FROM sabre_calendarinstances WHERE principaluri LIKE '%{$user->getLogin()}%' AND uri = 'default'";
            $res = $DB->select($sql);

            if (count($res) < 1){
                $createcalsql = "INSERT INTO sabre_calendars (synctoken, components) VALUES (1, 'VEVENT,VTODO')";
                $DB->no_result($createcalsql);
                $calid = $DB->getLastId();

                $insertsql = "INSERT INTO sabre_calendarinstances
                              (calendarid, principaluri, access, displayname, uri, description, calendarorder, calendarcolor, timezone, transparent, share_href, share_displayname, share_invitestatus)
                              VALUES ({$calid},'principals/{$user->getLogin()}',1,'Standard','default',NULL,0,NULL,NULL,0,NULL,NULL,2)";
                $DB->no_result($insertsql);
            }
        }
    }

    public static function createCalEntry()
    {
        $client = new SimpleCalDAVClient();

        try {
            $client->connect($_SERVER["DOCUMENT_ROOT"]."/sabre/server.php/calendars/{$_USER->getLogin()}/default", $_USER->getLogin(), $_SESSION["password"]);


            $arrayOfCalendars = $client->findCalendars(); // Returns an array of all accessible calendars on the server.
            if (array_key_exists("default",$arrayOfCalendars)){
                $client->setCalendar($arrayOfCalendars["default"]);

                $NewEvent = 'BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//ABC Corporation//NONSGML My Product//EN
BEGIN:VEVENT
SUMMARY:Lunchtime meeting
UID:ff808181-1fd7389e-011f-d7389ef9-00000003
DTSTART;TZID=America/New_York:20160420T120000
DURATION:PT1H
END:VEVENT
END:VCALENDAR';

            }
        }
        catch (Exception $e) {
            echo $e->__toString();
        }
    }

}