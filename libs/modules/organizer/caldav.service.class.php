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

    public static function generalCalendar()
    {
        global $DB;
        // check if company wide calendar exists (general)
        // this calender is always created for the first user found (lowest id)

        $sql = "SELECT * FROM sabre_calendarinstances WHERE principaluri LIKE '%company%' AND uri = 'company'";
        $res = $DB->select($sql);
        if (count($res)<1){
            $createcalsql = "INSERT INTO sabre_calendars (synctoken, components) VALUES (1, 'VEVENT,VTODO')";
            $DB->no_result($createcalsql);
            $calid = $DB->getLastId();

            $insertsql = "INSERT INTO sabre_calendarinstances
                          (calendarid, principaluri, access, displayname, uri, description, calendarorder, calendarcolor, timezone, transparent, share_href, share_displayname, share_invitestatus)
                          VALUES ({$calid},'principals/company',1,'Allgemein','company',NULL,2,NULL,NULL,0,NULL,NULL,2)";
            $DB->no_result($insertsql);

            $insertsql1 = "INSERT INTO sabre_principals (uri, email, displayname) VALUES ('principals/company','mail@company.com','general company principal')";
            $insertsql2 = "INSERT INTO sabre_principals (uri, email, displayname) VALUES ('principals/company/calendar-proxy-read', NULL, NULL)";
            $insertsql3 = "INSERT INTO sabre_principals (uri, email, displayname) VALUES ('principals/company/calendar-proxy-write', NULL, NULL)";
            $DB->no_result($insertsql1);
            $DB->no_result($insertsql2);
            $DB->no_result($insertsql3);
        }

        $sql = "SELECT id FROM sabre_principals WHERE uri = 'principals/company/calendar-proxy-read'";
        $ppid_read = $DB->first($sql, "id");
        $sql = "SELECT id FROM sabre_principals WHERE uri = 'principals/company/calendar-proxy-write'";
        $ppid_write = $DB->first($sql, "id");

        if ($ppid_read && $ppid_write){
            $users = User::getAllUser();
            foreach ($users as $user) {
                $sql = "SELECT id FROM sabre_principals WHERE uri = 'principals/{$user->getLogin()}'";
                $principal = $DB->first($sql, "id");
                if ($principal){
                    $sql = "SELECT id FROM sabre_groupmembers WHERE principal_id = {$ppid_read} AND member_id = {$principal}";
                    $ingroup = $DB->first($sql, "id");
                    if (!$ingroup){
                        $addmembersql = "INSERT INTO sabre_groupmembers (id, principal_id, member_id) VALUES (NULL, {$ppid_read}, {$principal})";
                        $DB->no_result($addmembersql);
                    }
                    if ($user->isAdmin()){
                        $sql = "SELECT id FROM sabre_groupmembers WHERE principal_id = {$ppid_write} AND member_id = {$principal}";
                        $ingroup = $DB->first($sql, "id");
                        if (!$ingroup){
                            $addmembersql = "INSERT INTO sabre_groupmembers (id, principal_id, member_id) VALUES (NULL, {$ppid_write}, {$principal})";
                            $DB->no_result($addmembersql);
                        }
                    }
                }
            }
        }
    }

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
                              VALUES ({$calid},'principals/{$user->getLogin()}',1,'Standard','default',NULL,1,NULL,NULL,0,NULL,NULL,2)";
                $DB->no_result($insertsql);
            }
        }
    }

}