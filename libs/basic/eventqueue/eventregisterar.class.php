<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */



class EventRegisterar {
    /**
     * @var array
     */
    public static $eventClasses = Array();

    /**
     * @param $name
     * @param EventClassInterface $eventclass
     */
    public static function register($name, EventClassInterface $eventclass){
        self::$eventClasses[$name] = $eventclass;
    }

    /**
     * @return array
     */
    public static function getRegisterdFooers(){
        return self::$eventClasses;
    }
}
/*
 * EventRegisterar::register("Fooer",new Fooer());
 * EventRegisterar::register("Special Fooer",new SpecialFooer());
 */