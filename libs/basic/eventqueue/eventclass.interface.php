<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */
require_once 'libs/basic/eventqueue/eventqueue.class.php';

/**
 * Interface EventClassInterface
 */
interface EventClassInterface {
    /**
     * @param $function
     * @param $args
     * @return mixed
     */
    public function fire($function, $args);
}