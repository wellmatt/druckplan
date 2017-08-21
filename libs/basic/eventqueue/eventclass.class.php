<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */

abstract class EventClass implements EventClassInterface {

    /**
     * Executes class specific event function
     * @param $function
     * @param $args
     * @return bool
     */
    public function fire($function, $args)
    {
        if (get_class_methods(get_called_class()) != null && in_array($function,get_class_methods(get_called_class()))){
            return $this->{$function}($args);
        } else {
            return false;
        }
    }
}