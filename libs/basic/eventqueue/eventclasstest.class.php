<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */

class EventClassTest extends EventClass {
    /**
     * @param $args
     * @return bool
     */
    public function sendMail($args){
        $empfaenger = 'ascherer@ipactor.de';
        $betreff = 'test event queue: '.$args["key"];
        $nachricht = 'dies ist ein test mit argumenten: '.json_encode($args)."\r\n".'Class: '.get_called_class().' // Function: sendMail';
        $header = 'From: ascherer@ipactor.de' . "\r\n" .
            'Reply-To: ascherer@ipactor.de' . "\r\n" .
            'CC: mwelland@ipactor.de' . "\r\n" .
            'X-Mailer: PHP/' . phpversion();

        $result = mail($empfaenger, $betreff, $nachricht, $header);
        return $result;
    }
}