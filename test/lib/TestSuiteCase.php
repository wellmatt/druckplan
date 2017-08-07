<?php
/**
 *  Copyright (c) 2017 Teuber Consult + IT GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <alexander.scherer@teuber-consult.de>, 2017
 *
 */


class TestSuiteCase{
    public $suite;
    public $faker;
    public $helper;

    public $results = [ 'error' => [], 'success' => [] ];
    public $name = '';
    /**
     * @var array
     * Structure needs to be
     * [ [ 'name' => FuntionName, 'required' => false ] ]
     */
    public $tests = [];

    /**
     * TestSuiteCase constructor.
     * @param $suite TestSuite
     */
    public function __construct($suite)
    {
        $this->suite = $suite;
        $this->faker = $suite->faker;
        $this->helper = $suite->helper;
//        $this->tests = preg_grep('/^test/', get_class_methods(get_called_class()));
        $this->bootUp();
    }

    private function bootUp(){}

    public function run()
    {
        foreach ($this->tests as $test) {
            $function = (string)$test['name'];
            $res = $this->$function();
            if ($res === false && $test['required'] === true){
                $this->results['error'][] = $this->helper->generateError(get_called_class(),__FUNCTION__,'FATAL: Required test "'.$function.'" failed. Following tests aborted!');
                break;
            }
        }
        return $this->results;
    }

    public function result($function, $message, $type = 'error', $details = '')
    {
        if ($type == 'error')
            $this->results['error'][] = $this->helper->generateError(get_called_class(),$function,$message,$details);
        else
            $this->results['success'][] = $this->helper->generateError(get_called_class(),$function,$message,$details);

    }
}