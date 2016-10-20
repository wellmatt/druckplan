<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */
require_once 'libs/modules/calculation/order.class.php';
require_once 'libs/modules/calculation/calculation.class.php';
require_once 'libs/modules/calculation/calculation.machineentry.class.php';

class OrderService{
    private $order;
    private $calculations = [];
    private $parameters = [];

    /**
     * OrderService constructor.
     * @param array $parameters
     */
    public function __construct($parameters = [])
    {
        $this->order = new Order(0);
        $this->parameters = $parameters;
    }

    /**
     * @return Order
     */
    public function createOrder()
    {
        $order = new Order(0);
        $params = $this->parameters['order'];
        foreach ($params as $method => $value) {
            if (method_exists($order,$method))
            {
                $order->$method($value);
            }
        }
        $this->order = $order;
        return $this->order;
    }

    /**
     * @return Calculation[]
     */
    public function createCalculations()
    {
        $params = $this->parameters['calc'];
        foreach ($params as $param) {
            $paramscalc = $param;
            $calc = new Calculation(0);
            $calc->setOrderId($this->order->getId());
            foreach ($paramscalc as $method => $value) {
                if (method_exists($calc,$method))
                {
                    $calc->$method($value);
                }
            }
            $this->calculations[] = $calc;
        }
        return $this->calculations;
    }

}