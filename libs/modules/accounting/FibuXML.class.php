<?php
/**
 *  Copyright (c) 2017 Teuber Consult + IT GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <alexander.scherer@teuber-consult.de>, 2017
 *
 */
require_once 'thirdparty/GreenCape/XML/Converter.php';


class FibuXML{

    public $receipts = [];
    public $busicons = [];

    /**
     * FibuXML constructor.
     * @param Receipt[] $receipts
     * @param BusinessContact[] $busicons
     */
    public function __construct(array $receipts, array $busicons)
    {
        $this->receipts = $receipts;
        $this->busicons = $busicons;
    }

    private function array_to_xml( $data ) {
        $xml_data = new SimpleXMLElement('<?xml version="1.0" encoding="ISO-8859-1"?><FibuBelege firmaNr="3000" Anzahlobjekte="'.count($this->receipts).'"></FibuBelege>');
        foreach( $data as $key => $value ) {
            if( is_numeric($key) ){
                $key = 'item'.$key; //dealing with <0/>..<n/> issues
            }
            if( is_array($value) ) {
                $subnode = $xml_data->addChild($key);
                array_to_xml($value, $subnode);
            } else {
                $xml_data->addChild("$key",htmlspecialchars("$value"));
            }
        }
        return $xml_data;
    }

    public function generateBusiconXML()
    {
        $xml_data = new SimpleXMLElement('<?xml version="1.0" encoding="ISO-8859-1"?><EGeckoPersonenkonten objectgroupNr="3000" datumFormat="dd.MM.yyyy" splittenHausnummer="j" anzahlObjekte="'.count($this->busicons).'"></EGeckoPersonenkonten>', LIBXML_NOEMPTYTAG);

        foreach ($this->busicons as $busicon) {
            $Personenkonto = $xml_data->addChild('Personenkonto');
            $Personenkonto->addChild('kontenart','D');
            $Personenkonto->addChild('kontonummer',$busicon->getCustomernumber());
            $Personenkonto->addChild('bezeichnung',$busicon->getName1().' '.$busicon->getName2());
            $Personenkonto->addChild('umsatzsteuerIdentNummer',$busicon->getVatidentnumber());
            $Personenkonto->addChild('steuernummer',$busicon->getVatnumber());
            $Personenkonto->addChild('waehrungsschluessel','EUR');

            $Geschaeftspartner = $Personenkonto->addChild('Geschaeftspartner');
            $Geschaeftspartner->addChild('nummer',$busicon->getCustomernumber());

            $Personendaten = $Geschaeftspartner->addChild('Personendaten');
            $Personendaten->addChild('name1',$busicon->getName1());
            $Personendaten->addChild('name2',$busicon->getName2());

            $Anschrift = $Geschaeftspartner->addChild('Anschrift');
            $Anschrift->addChild('name3','');
            $Anschrift->addChild('strasse',$busicon->getAddress1() . " " .$busicon->getAddress2());
            $Anschrift->addChild('plz',$busicon->getZip());
            $Anschrift->addChild('ort',$busicon->getCity());
            $Anschrift->addChild('postfach','');
            $Anschrift->addChild('postfachPlz','');
            $Anschrift->addChild('postfachOrt','');
            $Anschrift->addChild('landkennzeichen','');

            $TeleKommunikationen = $Geschaeftspartner->addChild('TeleKommunikationen');
            $Tele1 = $TeleKommunikationen->addChild('TeleKommunikation');
            $Tele1->addChild('qualifier','update');
            $Tele1->addChild('art','geschäftlich');
            $Tele1->addChild('rufnummer',$busicon->getPhone());

            $Tele2 = $TeleKommunikationen->addChild('TeleKommunikation');
            $Tele2->addChild('qualifier','update');
            $Tele2->addChild('art','Fax');
            $Tele2->addChild('rufnummer',$busicon->getFax());

            $OnlineKommunikationen = $Geschaeftspartner->addChild('OnlineKommunikationen');
            $OnlineKommunikation = $OnlineKommunikationen->addChild('OnlineKommunikation');
            $OnlineKommunikation->addChild('qualifier','update');
            $OnlineKommunikation->addChild('art','geschäftlich');
            $OnlineKommunikation->addChild('email',$busicon->getEmail());

        }
        return $xml_data;
    }

    public function generateReceiptXML()
    {
        $xml_data = new SimpleXMLElement('<?xml version="1.0" encoding="ISO-8859-1"?><FibuBelege firmaNr="3000" Anzahlobjekte="'.count($this->receipts).'"></FibuBelege>', LIBXML_NOEMPTYTAG);

        foreach ($this->receipts as $receipt) {
            $beleg = $xml_data->addChild('FibuBeleg');

            $Belegkopf = $beleg->addChild('Belegkopf');
            $Belegkopf->addChild('belegart','RA');
            $Belegkopf->addChild('belegnummer',$receipt->getNumber());
            $Belegkopf->addChild('belegdatum',date('d.m.Y',$receipt->getDate()));
            if ($receipt->getOriginType() == Receipt::ORIGIN_INVOICE){
                if ($receipt->getOrigin()->getColinv()->getDeliverydate()>0)
                    $Belegkopf->addChild('belegperiode',date('Y',$receipt->getOrigin()->getColinv()->getDeliverydate()).'/'.date('m',$receipt->getOrigin()->getColinv()->getDeliverydate()));
                else
                    $Belegkopf->addChild('belegperiode',date('Y',$receipt->getDate()).'/'.date('m',$receipt->getDate()));
            } else
                $Belegkopf->addChild('belegperiode',date('Y',$receipt->getDate()).'/'.date('m',$receipt->getDate()));
            $Belegkopf->addChild('belegwaehrung',$receipt->getCurrency());
            $Belegkopf->addChild('bruttoErfassung','j');
            $Belegkopf->addChild('buchungstext',$receipt->getDescription());

            $FibuBelegpositionen = $beleg->addChild('FibuBelegpositionen');

            foreach ($receipt->getReceiptpositions() as $receiptposition) {
                $FibuBelegposition = $FibuBelegpositionen->addChild('FibuBelegposition');
                $FibuBelegposition->addChild('buchungsschluessel',$receiptposition->getPostingkey());

                if ($receiptposition->getType() == ReceiptPosition::TYPE_CREDIT)
                    $FibuBelegposition->addChild('kontonummer',$receipt->getOrigin()->getColinv()->getBusinesscontact()->getCustomernumber());
                else
                    $FibuBelegposition->addChild('kontonummer',$receiptposition->getRevenueaccount());

                $FibuBelegposition->addChild('betrag',$receiptposition->getAmount());

                if ($receiptposition->getType() == ReceiptPosition::TYPE_CREDIT){
                    $sk1date = '';
                    $sk1percent = '';
                    $sk1betrag = '';
                    if ($receipt->getOriginType() == Receipt::ORIGIN_INVOICE) {
                        if ($receipt->getOrigin()->getDuedatesk1())
                            $sk1date = date('d.m.Y', $receipt->getOrigin()->getDuedatesk1());
                        if ($receipt->getOrigin()->getSk1Percent()) {
                            $sk1percent = $receipt->getOrigin()->getSk1Percent();
                            $sk1betrag = round($receiptposition->getAmount() / 100 * $sk1percent,2);
                        }
                    }

                    $FibuBelegposition->addChild('steuerschluessel','');
                    $FibuBelegposition->addChild('steuerbetrag','');

                    $OpInfos = $FibuBelegposition->addChild('OpInfos');
                    $OpInfos->addChild('opNr',$receipt->getNumber());
                    $OpInfos->addChild('ziel1',$sk1date);
                    $OpInfos->addChild('skonto1Betrag',$sk1betrag);
                    $OpInfos->addChild('skonto1Prozent',$sk1percent);
                    $OpInfos->addChild('faelligAm',date('d.m.Y',$receipt->getOrigin()->getDuedate()));
                } else {
                    $FibuBelegposition->addChild('steuerschluessel',$receiptposition->getTaxKey());
                    $FibuBelegposition->addChild('steuerbetrag',$receiptposition->getTaxAmount());

                    $FibuKoreBelegposition = $FibuBelegposition->addChild('FibuKoreBelegposition');
                    $FibuKoreBelegposition->addChild('kostentraeger',$receiptposition->getAccountnumber());

                    $FibuKoreBelegposition->addChild('nettobetrag',$receiptposition->getAmount());
                }
            }

            $FibuSteuerpositionen = $beleg->addChild('FibuSteuerpositionen');

            foreach ($receipt->getReceipttaxpositions() as $receipttaxposition) {
                $FibuSteuerposition = $FibuSteuerpositionen->addChild('FibuSteuerposition');
                $FibuSteuerposition->addChild('stposSchluessel',$receipttaxposition->getKey());

                $FibuSteuerposition->addChild('stposBetrag',$receipttaxposition->getAmount());

                $FibuSteuerposition->addChild('stposProzent',$receipttaxposition->getPercent());
            }
        }
        return $xml_data;
    }
    
    

    public static function listExports()
    {
        $path    = 'docs/fibuexports';
        $files = array_diff(scandir($path, 1), array('.', '..','.gitkeep'));
        return $files;
    }
}