<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */
require_once('vendor/simpleCalDAV/SimpleCalDAVClient.php');

/**
 * Will create CalDav Event Object like:
 *
 *
 *  BEGIN:VCALENDAR
    VERSION:2.0
    BEGIN:VEVENT
    DTSTAMP:{$start}
    DTSTART:{$start}
    DTEND:{$end}
    UID:TKT-1480492296
    DESCRIPTION:Dies ist ein Testeintrag Description
    LOCATION:TestLocation
    SUMMARY:Dies ist ein Testeintrag Summary
    END:VEVENT
    END:VCALENDAR
 *
 *
 * Class CalDavEvent
 */

class CalDavEvent {
    private $created;
    private $start;
    private $end;
    private $descr = '';
    private $location = '';
    private $summary = '';
    private $uid;

    /**
     * CalDavEvent constructor.
     * @param array $array
     */
    public function __construct($array = [])
    {
        foreach ($array as $index => $item) {
            if (property_exists("CalDavEvent",$index)){
                $this->$index = $item;
            }
        }
        $this->created = self::convertTimestamp(time());
    }

    /**
     * @param $unixtime
     * @return bool|string
     */
    public static function convertTimestamp($unixtime)
    {
        // 'Ymd\THis\Z'
        return date('Ymd\THis',$unixtime);
    }

    /**
     * @return CalDAVObject|bool
     * @throws CalDAVException
     * @throws Exception
     */
    public function saveToGlobalCal()
    {
        global $_USER;
        try {
            $client = new SimpleCalDAVClient();
            $client->connect("http://contilas2.mein-druckplan.de/sabre/server.php/calendars/company/company", "company", "contilas");
            $arrayOfCalendars = $client->findCalendars(); // Returns an array of all accessible calendars on the server.
            if (array_key_exists("company",$arrayOfCalendars)){
                $client->setCalendar($arrayOfCalendars["company"]);
                $event = $this->generate();
                if ($event != false){
                    $res = $client->create($event);
                    return $res;
                }
            }
        } catch (Exception $e) {
            echo $e->__toString();
        }
        return false;
    }

    /**
     * @return CalDAVObject|bool
     * @throws CalDAVException
     * @throws Exception
     */
    public function saveToMyCal()
    {
        global $_USER;
        if ($_USER->getId() > 0 && isset($_SESSION["password"])){
            try {
                $client = new SimpleCalDAVClient();
                $client->connect("http://contilas2.mein-druckplan.de/sabre/server.php/calendars/{$_USER->getLogin()}/default", $_USER->getLogin(), $_SESSION["password"]);
                $arrayOfCalendars = $client->findCalendars(); // Returns an array of all accessible calendars on the server.
                if (array_key_exists("default",$arrayOfCalendars)){
                    $client->setCalendar($arrayOfCalendars["default"]);
                    $event = $this->generate();
                    if ($event != false){
                        $res = $client->create($event);
                        return $res;
                    }
                }
            } catch (Exception $e) {
                echo $e->__toString();
            }
        }
        return false;
    }

    /**
     * @return string
     */
    public function generate()
    {
        if (isset($this->start) && isset($this->end) && isset($this->uid)){
            $res = "BEGIN:VCALENDAR
VERSION:2.0
BEGIN:VEVENT
DTSTAMP;TZID=Europe/Berlin:{$this->created}
DTSTART;TZID=Europe/Berlin:{$this->start}
DTEND;TZID=Europe/Berlin:{$this->end}
UID:{$this->uid}
DESCRIPTION:{$this->descr}
LOCATION:{$this->location}
SUMMARY:{$this->summary}
END:VEVENT
END:VCALENDAR";
            return $res;
        }
        return false;
    }


}