<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */


class CalDavEntry{
    public $summary;
    public $uid;
    public $dtstart;
    public $duration;

    /**
     * CalDavEntry constructor.
     */
    public function __construct()
    {

    }

    public function generate()
    {
        $entry = "BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//Contilas//EN
BEGIN:VTIMEZONE
TZID:Europe/Berlin
X-LIC-LOCATION:Europe/Berlin

BEGIN:VEVENT
CREATED:20140403T091024Z
LAST-MODIFIED:20140403T091044Z
DTSTAMP:20140416T091044Z
UID:{$this->uid}
SUMMARY:{$this->summary}
DTSTART;TZID=Europe/Berlin:20140418T090000
DTEND;TZID=Europe/Berlin:20140418T100000
LOCATION:ExamplePlace1
DESCRIPTION:ExampleDescription1
END:VEVENT

BEGIN:VEVENT
SUMMARY:
UID:{$this->uid}
DTSTART;TZID=Europe/Berlin:{$this->dtstart}
DURATION:PT1H
END:VEVENT
END:VCALENDAR";
    }

}