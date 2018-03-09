<?php
/**
 *  Copyright (c) 2017 Teuber Consult + IT GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <alexander.scherer@teuber-consult.de>, 2017
 *
 */
require_once 'vendor/autoload.php';
use \Curl\Curl;

require_once 'saxoprint.address.class.php';
require_once 'saxoprint.freeformat.class.php';
require_once 'saxoprint.order.class.php';
require_once 'saxoprint.productcharacteristic.class.php';
require_once 'saxoprint.specialcolor.class.php';
require_once 'saxoprint.workingstate.class.php';
require_once 'saxoprint.productdetails.class.php';
require_once 'saxoprint.collectiveinvoice.info.class.php';
require_once 'libs/modules/shipment/shipment.class.php';

class Saxoprint{
    public $api_key = '';
    public $baseurl = 'https://saxoscout1.staging.saxoprint.com/';
    public $curl;
    public $error = '';

    const Invalid = 0;
    const Assigned = 1;
    const Received = 2;
    const BeingProcessed = 3;
    const Finished = 4;
    const Dispatched = 5;
    const Returned = 6;
    const Cancelled = 7;
    const Rejected = 8;
    const ProductionCancelled = 9;
    const AwaitingInstructions = 10;
    const Registered = 11;
    const TrackingCodeSubmitted = 12;
    const PostponedCancellation = 13;
    const CancellationConfirmed = 15;
    const Changed = 16;
    const AwaitingCheckOut = 17;
    const AwaitingCheckIn = 18;
    const DailyClosingFinished = 19;

    /**
     * Saxoprint constructor.
     */
    public function __construct()
    {
        $perf = new Perferences();
        $this->api_key = $perf->getSaxoapikey();
        $curl = new Curl();
        $curl->setBasicAuthentication($this->api_key, '');
        $curl->setUserAgent("Contilas-Druckplan API Webservice");
        $curl->setHeader("Content-Type", "application/json");
        $this->curl = $curl;
    }

    /**
     * Fetches state of order from saxoprint api
     * @param $ordernumber
     * @return string
     */
    public function getOrderState($ordernumber)
    {
        $this->curl->get($this->baseurl.'api/v3/printjobs/'.$ordernumber.'/workingstate');

        if ($this->curl->error) {
            $this->error = 'Error: ' . $this->curl->errorCode . ': ' . $this->curl->errorMessage . "\n";
            prettyPrint($this->error);
            return false;
        } else {
            return $this->curl->response->WorkingStateText;
        }
    }

    /**
     * Fetches all remote orders from saxoprint api
     * @param $workingstate
     * @return bool|SaxoprintOrder[]
     */
    public function getRemoteOrders($workingstate = -1)
    {
        if ($workingstate >= 0){
            $this->curl->get($this->baseurl.'api/v3/printjobs/workingstate/'.$workingstate);
        } else {
            $this->curl->get($this->baseurl.'api/v3/printjobs');
        }

        if ($this->curl->error) {
            $this->error = 'Error: ' . $this->curl->errorCode . ': ' . $this->curl->errorMessage . "\n";
            prettyPrint($this->error);
            return false;
        } else {
            $orders = self::parseOrders($this->curl->response);
            return $orders;
        }
    }

    /**
     * Changes a remote order state
     * @param $order SaxoprintOrder
     * @param $workingstate int
     * @return bool
     */
    public function postOrderStatus($order, $workingstate)
    {
        $post = [
            "WorkingState" => $workingstate
        ];
        $post = json_encode($post);

        $this->curl->post($this->baseurl.'api/v3/printjobs/'.$order->getOrderNumber().'/workingstate', $post);


        if ($this->curl->error) {
            $this->error = 'Error: ' . $this->curl->errorCode . ': ' . $this->curl->errorMessage . "\n";
            prettyPrint($this->error);
            return false;
        } else {
            return true;
        }
    }

    /**
     * Changes a remote order state
     * @param $postarray array
     * @return bool
     *
     * $postarray needs to be of format:
     *
     *   [
     *      {
     *          "OrderNumber": 1,
     *          "WorkingState": 0
     *      },
     *      {
     *          "OrderNumber": 1,
     *          "WorkingState": 0
     *      }
     *  ]
     *
     */
    public function postOrderStatusMultiple($postarray)
    {
        $post = json_encode($postarray);

        $this->curl->post($this->baseurl.'api/v3/printjobs/workingstate', $post);

        if ($this->curl->error) {
            $this->error = 'Error: ' . $this->curl->errorCode . ': ' . $this->curl->errorMessage . "\n";
            prettyPrint($this->error);
            return false;
        } else {
            return true;
        }
    }

    /**
     * @param CollectiveInvoice $colinv
     */
    public function sendShippingCodes(CollectiveInvoice $colinv)
    {
        $shipments = Shipment::getAllForColinv($colinv);
        $this->postTrackingCodes($shipments);
    }

    /**
     * @param $shipments Shipment[]
     * @return boolean
     *
    {
        "TrackingInformationDto": [
            {
                "DeliveryAddress": {
                    "Address": {
                        "Salutation": 0,
                        "CompanyName": "sample string 1",
                        "FirstName": "sample string 2",
                        "LastName": "sample string 3",
                        "Street": "sample string 4",
                        "Zipcode": "sample string 5",
                        "City": "sample string 6",
                        "TelephoneNumber": "sample string 7",
                        "CountryCodeISO": "sample string 8"
                    },
                    "Circulation": 1
                },
                "ShipperProduct": 0,
                "PackageType": 2,
                "PackageInformation": [
                    {
                        "Weight": 1.0,
                        "TrackingCode": "sample string 2"
                    }
                ]
            }
        ]
    }
     */
    public function postTrackingCodes($shipments)
    {
        $postarray = [];
        $innerarray = [];
        $ordernumber = null;

        if (count($shipments) > 0){

            foreach ($shipments as $shipment) {
                $colinv = $shipment->getColinv();
                $ordernumber = $colinv->getSaxoid();

                $salutation = ($colinv->getCustContactperson()->getTitle() == 'Herr') ? 1 : 2;

                $innerarray[] = [
                    "DeliveryAddress" => [
                        "Address" => [
                            "Salutation" => $salutation,
                            "CompanyName" => $colinv->getBusinesscontact()->getNameAsLine(),
                            "FirstName" => $colinv->getCustContactperson()->getName2(),
                            "LastName" => $colinv->getCustContactperson()->getName1(),
                            "Street" => $colinv->getDeliveryaddress()->getStreet(),
                            "Zipcode" => $colinv->getDeliveryaddress()->getZip(),
                            "City" => $colinv->getDeliveryaddress()->getCity(),
                            "TelephoneNumber" => 0,
                            "CountryCodeISO" => $colinv->getDeliveryaddress()->getCountry()->getCode(),
                        ],
                        "Circulation" => 1
                    ],
                    "ShipperProduct" =>  6,
                    "PackageType" => 2,
                    "PackageInformation" => [
                        [
                            "Weight" => $shipment->getWeight(),
                            "TrackingCode" => $shipment->getParcelNumber()
                        ]
                    ]
                ];
            }

            $postarray[] = [ "TrackingInformationDto" => [ $innerarray ] ];

            $post = json_encode($postarray);

            $this->curl->post($this->baseurl.'api/v3/printjobs/'.$ordernumber.'/trackingcode', $post);

            if ($this->curl->error) {
                $this->error = 'Error: ' . $this->curl->errorCode . ': ' . $this->curl->errorMessage . "\n";
                prettyPrint($this->error);
                return false;
            } else {
                return true;
            }
        }
        return false;
    }

    /**
     * Fetches a remote order from saxoprint api
     * @param $ordernumber
     * @return bool|SaxoprintOrder[]
     */
    public function getRemoteOrder($ordernumber)
    {
        $this->curl->get($this->baseurl.'api/v3/printjobs/'.$ordernumber);

        if ($this->curl->error) {
            $this->error = 'Error: ' . $this->curl->errorCode . ': ' . $this->curl->errorMessage . "\n";
            prettyPrint($this->error);
            return false;
        } else {
            $orders = self::parseOrders($this->curl->response);
            return $orders;
        }
    }

    private function parseOrders($orders){
        if (is_a($orders,stdClass::class)){
            $orders = [$orders];
        }
        $ret = [];
        if (count($orders)>0){
            foreach ($orders as $order) {
                $delivadr = [];
                foreach ($order->DeliveryAddresses as $deliveryAddress) {
                    $delivadr[] = new SaxoprintAddress(
                        $deliveryAddress->Address->Salutation,
                        $deliveryAddress->Address->CompanyName,
                        $deliveryAddress->Address->FirstName,
                        $deliveryAddress->Address->LastName,
                        $deliveryAddress->Address->Street,
                        $deliveryAddress->Address->Zipcode,
                        $deliveryAddress->Address->City,
                        $deliveryAddress->Address->TelephoneNumber,
                        $deliveryAddress->Address->CountryCodeISO
                    );
                }
                $senderadr = new SaxoprintAddress(
                    $order->SenderAddress->Salutation,
                    $order->SenderAddress->CompanyName,
                    $order->SenderAddress->FirstName,
                    $order->SenderAddress->LastName,
                    $order->SenderAddress->Street,
                    $order->SenderAddress->Zipcode,
                    $order->SenderAddress->City,
                    $order->SenderAddress->TelephoneNumber,
                    $order->SenderAddress->CountryCodeISO
                );
                
                $prodcharacts = [];
                foreach ($order->ProductDetails->ProductCharacteristics as $productCharacteristic) {
                    $prodcharacts[] = new SaxoprintProductCharacteristic(
                        $productCharacteristic->PropertyId,
                        $productCharacteristic->PropertyName,
                        $productCharacteristic->PropertyValueId,
                        $productCharacteristic->PropertyValueName
                    );
                }

                $speccolors = [];
                foreach ($order->ProductDetails->SpecialColors as $SpecialColors) {
                    $speccolors[] = new SaxoprintSpecialColor(
                        $SpecialColors->Chromaticity,
                        $SpecialColors->SpecialColor
                    );
                }

                $freeforms = [];
                foreach ($order->ProductDetails->FreeFormats as $FreeFormats) {
                    $freeforms[] = new SaxoprintFreeFormat(
                        $FreeFormats->PropertyId,
                        $FreeFormats->PropertyName,
                        $FreeFormats->Value
                    );
                }

                $proddetails = new SaxoprintProductDetails(
                    $order->ProductDetails->Circulation,
                    $prodcharacts,
                    $speccolors,
                    $freeforms
                );

                $wrkstates = [];
                foreach ($order->WorkingStates as $WorkingState) {
                    $wrkstates[] = new SaxoprintWorkingState(
                        $WorkingState->WorkingState,
                        $WorkingState->WorkingStateText,
                        $WorkingState->Timestamp
                    );
                }

                $compdate = new DateTime($order->CompletionDate);
                $compdate = $compdate->format('U');

                $ret[] = new SaxoprintOrder(
                    $order->OrderNumber,
                    $order->ReferenceNumber,
                    $order->PortalId,
                    $compdate,
                    $delivadr,
                    $senderadr,
                    $proddetails,
                    $wrkstates
                );
            }
        }
        return $ret;
    }
}