<?php
/**
 *  Copyright (c) 2017 Teuber Consult + IT GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <alexander.scherer@teuber-consult.de>, 2017
 *
 */

class TestCollectiveInvoice extends TestSuiteCase{
    public $name = 'CollectiveInvoice';
    /**
     * @var CollectiveInvoice
     */
    private $colinv;

    public $tests = [
        [ 'name' => 'testCreate', 'required' => true ],
        [ 'name' => 'testPositions', 'required' => true ],
        [ 'name' => 'testDocuments', 'required' => true ]
    ];

    public function testDocuments()
    {
        global $DB;
        $collectinv = $this->colinv;

        $types = [Document::TYPE_OFFER, Document::TYPE_OFFERCONFIRM, Document::TYPE_FACTORY, Document::TYPE_DELIVERY, Document::TYPE_INVOICE];
        $random = $this->faker->numberBetween(0,4);

        foreach ($types as $type) {
            $doc = new Document();
            $doc->setRequestId($collectinv->getId());
            $doc->setRequestModule(Document::REQ_MODULE_COLLECTIVEORDER);

            switch ($type){
                case Document::TYPE_OFFER:
                    $doc->setType(Document::TYPE_OFFER);
                    $collectinv->setStatus(2);
                    $hash = $doc->createDoc(Document::VERSION_EMAIL);
                    $hash2 = $doc->createDoc(Document::VERSION_PRINT, $hash);
                    break;
                case Document::TYPE_OFFERCONFIRM:
                    $doc->setType(Document::TYPE_OFFERCONFIRM);
                    $collectinv->setStatus(3);
                    $hash = $doc->createDoc(Document::VERSION_EMAIL);
                    $hash2 = $doc->createDoc(Document::VERSION_PRINT, $hash);
                    break;
                case Document::TYPE_FACTORY:
                    $doc->setType(Document::TYPE_FACTORY);
                    $hash2 = $doc->createDoc(Document::VERSION_PRINT, false, false);
                    break;
                case Document::TYPE_DELIVERY:
                    $doc->setType(Document::TYPE_DELIVERY);
                    $collectinv->setStatus(5);
                    $hash = $doc->createDoc(Document::VERSION_EMAIL);
                    $hash2 = $doc->createDoc(Document::VERSION_PRINT, $hash);
                    break;
                case Document::TYPE_INVOICE:
                    $doc->setType(Document::TYPE_INVOICE);
                    $collectinv->setStatus(7);
                    $hash = $doc->createDoc(Document::VERSION_EMAIL);
                    $hash2 = $doc->createDoc(Document::VERSION_PRINT, $hash);
                    break;

            }
            $res = $doc->save();
            if ($res === false){
                $this->result(__FUNCTION__,'Could not save document for "'.$collectinv->getNumber().'" and doctype "'.$type.'"',$DB->getLastError());
                return false;
            } elseif ($hash === false || $hash2 === false){
                $this->result(__FUNCTION__,'Could not generate PDF document for "'.$collectinv->getNumber().'" and doctype "'.$type.'"');
                return false;
            } else {
                $this->result(__FUNCTION__,'Success in generation of "'.$doc->getName().'" (doctype "'.$type.'") for '.$collectinv->getNumber(),'success');
                if ($type == Document::TYPE_INVOICE) {
                    $invout = InvoiceOut::generate($doc->getName(), $collectinv, $collectinv->getPaymentterm(), $doc->getId());
                    if ($invout->getId() > 0){
                        $receipt = Receipt::getForOrigin($invout);
                        if ($receipt->getId() > 0){
                            $receiptpos = ReceiptPosition::getAllForReceipt($receipt);
                            if (count($receiptpos)>0){
                                $this->result(__FUNCTION__,'Success in generation of InvoiceOut, Receipt and ReceiptPositions for '.$collectinv->getNumber(),'success');
                            } else {
                                $this->result(__FUNCTION__,'Could not generate ReceiptPosition for "'.$collectinv->getNumber().'" and Receipt "'.$receipt->getId().'"');
                            }
                        } else {
                            $this->result(__FUNCTION__,'Could not generate Receipt for "'.$collectinv->getNumber().'" and InvoiceOut "'.$invout->getId().'"');
                        }
                    } else {
                        $this->result(__FUNCTION__,'Could not generate InvoiceOut for "'.$collectinv->getNumber().'"');
                    }
                }
            }
            if ($type == $random)
                break;
        }
        $collectinv->save();

        $this->result(__FUNCTION__,'Success in generation of all choosen documents in '.$collectinv->getNumber(),'success');
        return true;
    }

    public function testPositions()
    {
        global $DB;
        $colinv = $this->colinv;
        $positions = [];
        $count = $this->faker->numberBetween(1,12);

        for($i = 0; $i < 99; $i++){
            $newpos = new Orderposition();
            $aid = $this->helper->ArticleGetRandom();
            if ($aid === false){
                $this->result(__FUNCTION__,'Could not find any Article in DB');
                return false;
            } else {
                $article = new Article($aid);
            }
            if ($article->getId() == 0)
                continue;

            if (count($article->getOrderamounts())>0){
                $quantity = $article->getOrderamounts()[0];
            } else {
                $quantity = $this->faker->numberBetween(1,5000);
            }

            $newpos->setQuantity($quantity);
            $newpos->setPrice($article->getPrice($quantity));
            $newpos->setTaxkey(TaxKey::evaluateTax($colinv, $article));
            $newpos->setComment($article->getDesc());
            $newpos->setCollectiveinvoice($colinv->getId());
            if ($article->getOrderid() > 0)
                $newpos->setType(1);
            else
                $newpos->setType(2);
            $newpos->setObjectid($article->getId());
            $newpos->setSequence(Orderposition::getNextSequence($colinv));

            if ($article->getIsWorkHourArt() || $newpos->getType() == 1){
                $colinv->setNeeds_planning(1);
            }
            $positions[] = $newpos;
            if (count($positions) == $count)
                break;
        }

        $colinv->save();
        $res = Orderposition::saveMultipleOrderpositions($positions);
        if ($res){
            $this->result(__FUNCTION__,'Success in generation of "'.$count.'" positions in '.$colinv->getNumber(),'success');
            $this->colinv = $colinv;
            return true;
        } else {
            $this->result(__FUNCTION__,'Could not save Orderpositions for "'.$colinv->getNumber().'" to DB', 'error', $DB->getLastError());
            return false;
        }
    }

    public function testCreate()
    {
        global $_USER, $DB;
        $bid = $this->helper->BusinessContactGetRandom();
        if ($bid === false){
            $this->result(__FUNCTION__,'Could not find any Businesscontacts in DB');
            return false;
        } else {
            $busicon = new BusinessContact($bid);
            $invaddr = $busicon->getInvoiceAddresses();
            if (count($invaddr) > 0){
                $invoiceaddress = $invaddr[0];
            } else {
                for ($i = 1; $i <= 10; $i++) {
                    $bid = $this->helper->BusinessContactGetRandom();

                    $busicon = new BusinessContact($bid);
                    $invaddr = $busicon->getInvoiceAddresses();
                    if (count($invaddr) > 0) {
                        $invoiceaddress = $invaddr[0];
                        break;
                    }
                }
                if (!isset($invoiceaddress)){
                    $this->result(__FUNCTION__,'No Businesscontact with valid invoiceaddress found');
                    return false;
                }
            }

        }

        $uid = $this->helper->UserGetRandom();
        if ($uid === false){
            $this->result(__FUNCTION__,'Could not find any User in DB');
            return false;
        } else {
            $user = new User($uid);
        }

        $colinv = new CollectiveInvoice();
        $colinv->setStatus(1);
        $colinv->setType(1);
        $colinv->setTitle($this->faker->text(120));
        $colinv->setComment($this->faker->optional($weight = 0.5)->realText(260));
        $colinv->setClient($_USER->getClient());
        $colinv->setBusinesscontact($busicon);
        $colinv->setInvoiceAddress($invoiceaddress);
        $colinv->setCrtdate(time());
        $colinv->setCrtuser($user);
        $colinv->setUptdate(0);
        $colinv->setUptuser($user);
        $colinv->setIntent($this->faker->optional($weight = 0.5,'')->randomNumber());
        $colinv->setInternContact($user);
        $colinv->setCustMessage($this->faker->optional($weight = 0.5)->realText(260));
        $colinv->setCustContactperson($busicon->getMainContactperson());
        $colinv->setNeeds_planning(0);
        $colinv->setDeliverydate($this->faker->dateTimeBetween('+1day','+2 month')->getTimestamp());
        $colinv->setExt_comment($this->faker->optional($weight = 0.5)->realText(260));

        $res = $colinv->save();
        if ($res){
            $this->result(__FUNCTION__,'Success in generation of '.$colinv->getNumber(),'success');
            $this->colinv = $colinv;
            return true;
        } else {
            $this->result(__FUNCTION__,'Could not save CollectiveInvoice to DB', 'error', $DB->getLastError());
            return false;
        }
    }
}