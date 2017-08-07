<?php
/**
 *  Copyright (c) 2017 Teuber Consult + IT GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <alexander.scherer@teuber-consult.de>, 2017
 *
 */
require_once 'TestSuiteCase.php';
require_once 'TestSuiteHelper.php';

class TestSuite{
    private $testdir = './test/tests/';

    public $faker;
    public $helper;

    /**
     * @var TestSuiteCase[]
     */
    public $testClasses = [];
    public $tests = [];
    public $results = [];

    /**
     * TestSuite constructor.
     */
    public function __construct()
    {
        global $DB;
        $this->faker = Faker\Factory::create('de_DE');
        $this->helper = new TestSuiteHelper($DB);

        foreach (glob($this->testdir."*.php") as $filename)
        {
            require_once $filename;
            $name = str_replace('.php','',$filename);
            $name = str_replace($this->testdir,'',$name);
            $class = new $name();
            $this->testClasses[] = $class;
            $this->tests = array_merge($this->tests,$class->tests);
        }
    }

    public function runTests()
    {
        foreach ($this->testClasses as $test) {
            $class = new $test($this);
            $result = $class->run();
            $this->results[get_class($test)] = $result;
        }
    }
}