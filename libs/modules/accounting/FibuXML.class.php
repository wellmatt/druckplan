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

    /**
     * FibuXML constructor.
     * @param Receipt[] $receipts
     */
    public function __construct(array $receipts)
    {
        $this->receipts = $receipts;
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

    public function getXML1()
    {
        $array = $this->generateArray();
        $xml = $this->array_to_xml($array);
        return $xml;
    }

    public function getXML2()
    {
        $array = $this->generateArray();
        $xml = new \GreenCape\Xml\Converter($array);
        return $xml;
    }

    public function generateXML1()
    {
        $xml_data = new SimpleXMLElement('<?xml version="1.0" encoding="ISO-8859-1"?><FibuBelege firmaNr="3000" Anzahlobjekte="'.count($this->receipts).'"></FibuBelege>', LIBXML_NOEMPTYTAG);

        foreach ($this->receipts as $receipt) {
            $beleg = $xml_data->addChild('FibuBeleg');

            $Belegkopf = $beleg->addChild('Belegkopf');
            $Belegkopf->addChild('belegart','RA');
            $Belegkopf->addChild('belegnummer',$receipt->getNumber());
            $Belegkopf->addChild('belegdatum',date('d.m.Y',$receipt->getDate()));
            $Belegkopf->addChild('belegperiode',date('Y',$receipt->getDate()).'/'.date('m',$receipt->getDate()));
            $Belegkopf->addChild('belegwaehrung',$receipt->getCurrency());
            $Belegkopf->addChild('bruttoErfassung','j');
            $Belegkopf->addChild('buchungstext',$receipt->getDescription());

            $FibuBelegpositionen = $beleg->addChild('FibuBelegpositionen');

            foreach ($receipt->getReceiptpositions() as $receiptposition) {
                $FibuBelegposition = $FibuBelegpositionen->addChild('FibuBelegposition');
                $FibuBelegposition->addChild('buchungsschluessel',$receiptposition->getPostingkey());
                $FibuBelegposition->addChild('kontonummer',$receiptposition->getAccountnumber());
                $FibuBelegposition->addChild('betrag',$receiptposition->getAmount());

                if ($receiptposition->getType() == ReceiptPosition::TYPE_CREDIT){
                    $FibuBelegposition->addChild('steuerschluessel','');
                    $FibuBelegposition->addChild('steuerbetrag','');

                    $OpInfos = $FibuBelegposition->addChild('OpInfos');
                    $OpInfos->addChild('opNr',$receipt->getNumber());
                    $OpInfos->addChild('ziel1','');
                    $OpInfos->addChild('skonto1Betrag','');
                    $OpInfos->addChild('skonto1Prozent','');
                    $OpInfos->addChild('faelligAm','');
                } else {
                    $FibuBelegposition->addChild('steuerschluessel',$receiptposition->getTaxKey());
                    $FibuBelegposition->addChild('steuerbetrag',$receiptposition->getTaxAmount());

                    $FibuKoreBelegposition = $FibuBelegposition->addChild('FibuKoreBelegposition');
                    $FibuKoreBelegposition->addChild('kostentraeger',$receiptposition->getRevenueaccount());
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

    public function generateArray()
    {
        $array = [];

        foreach ($this->receipts as $receipt) {
            $beleg = ['FibuBeleg' => []];
            $Belegkopf = [
                'belegart' => 'RA',
                'belegnummer' => $receipt->getNumber(),
                'belegdatum' => date('d.m.Y',$receipt->getDate()),
                'belegperiode' => date('Y',$receipt->getDate()).'/'.date('m',$receipt->getDate()),
                'belegwaehrung' => $receipt->getCurrency(),
                'bruttoErfassung' => 'j',
                'buchungstext' => $receipt->getDescription()
            ];
            $beleg['FibuBeleg']['Belegkopf'] = $Belegkopf;

            $position_array = [];
            foreach ($receipt->getReceiptpositions() as $receiptposition) {
                $posarray = ['FibuBelegposition' => [
                    'buchungsschluessel' => $receiptposition->getPostingkey(),
                    'kontonummer' => $receiptposition->getAccountnumber(),
                    'betrag' => $receiptposition->getAmount(),
                ]];
                if ($receiptposition->getType() == ReceiptPosition::TYPE_CREDIT){
                    $posarray['FibuBelegposition']['steuerschluessel'] = '';
                    $posarray['FibuBelegposition']['steuerbetrag'] = '';
                    $posarray['FibuBelegposition']['OpInfos'] = [
                        'OpAngaben' => [
                            'opNr' => $receipt->getNumber(),
                            'ziel1' => '',
                            'skonto1Betrag' => '',
                            'skonto1Prozent' => '',
                            'faelligAm'
                        ]
                    ];
                } else {
                    $posarray['FibuBelegposition']['steuerschluessel'] = $receiptposition->getTaxKey();
                    $posarray['FibuBelegposition']['steuerbetrag'] = $receiptposition->getTaxAmount();
                    $posarray['FibuBelegposition']['FibuKoreBelegposition'] = [
                        'kostentraeger' => $receiptposition->getRevenueaccount(),
                        'nettobetrag' => $receiptposition->getAmount()
                    ];
                }
                $position_array[] = $posarray;
            }
            $beleg['FibuBeleg']['FibuBelegpositionen'] = $position_array;

            $taxposition_array = [];

            foreach ($receipt->getReceipttaxpositions() as $receipttaxposition) {
                $taxarray = [
                    'FibuSteuerposition' => [
                        'stposSchluessel' => $receipttaxposition->getKey(),
                        'stposBetrag' => $receipttaxposition->getAmount(),
                        'stposProzent' => $receipttaxposition->getPercent(),
                    ]
                ];
                $taxposition_array[] = $taxarray;
            }
            $beleg['FibuBeleg']['FibuSteuerpositionen'] = $taxposition_array;

            $array[] = $beleg;
        }
        return $array;
    }
}