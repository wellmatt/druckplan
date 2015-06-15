<? // ------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			29.11.2013
// Copyright:		2012-13 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------

class Shoppingbasketitem{
	
	const TYPE_PRODUCTS 		= 1;
	const TYPE_ARTICLE 			= 2;
	const TYPE_PERSONALIZATION 	= 3;

	private $id;
	private $title; 	// Titel des Eintrags	
	private $amount;	// Menge des Artikels/Produkts/Personalisierung
	private $price;		// Einzelpreis pro Stueck
	private $type;		// Typ des Eintrags  -> siehe Konstanten
	private $entryid;   // Entry ID
	private $file;      // Artikel Dateiupload
	
	private $delivery_address_id;
	private $invoice_address_id;

	function __construct($attribute){
		if ($attribute["id"]>0){
			$this->id 		= (int)$attribute["id"];
			$this->title 	= $attribute["title"];
			$this->amount 	= $attribute["amount"];
			$this->price	= $attribute["price"];
			$this->type 	= (int)$attribute["type"];
			$this->entryid 	= (int)$attribute["entryid"];
		}
	}
	
	/**
	 * Liefert die textuelle Bezeichnung des Typs 
	 * 
	 * @return string
	 */
	public function getTypeLabel(){
		$ret = " ";
		switch ($this->type){
			case 0 : $ret = $_LANG->get('Artikel'); break;
			case 1 : $ret = $_LANG->get('Produkt'); break;
			case 2 : $ret = $_LANG->get('Personalisierung'); break;
			default: $ret = "n.A.";
		}
		return $ret;
	}

	public function getId()
	{
		return $this->id;
	}

	public function setId($id)
	{
		$this->id = $id;
	}

	public function getTitle()
	{
		return $this->title;
	}

	public function setTitle($title)
	{
		$this->title = $title;
	}

	public function getAmount()
	{
		return $this->amount;
	}

	public function setAmount($amount)
	{
		$this->amount = $amount;
	}

	public function getPrice()
	{
		return $this->price;
	}

	public function setPrice($price)
	{
		$this->price = $price;
	}

	public function getType()
	{
		return $this->type;
	}

	public function setType($type)
	{
		$this->type = $type;
	}
	
	public function getInvoiceAdressID()
	{
		return $this->invoice_address_id;
	}
	
	public function setInvoiceAdressID($invoice_address_id)
	{
		$this->invoice_address_id = $invoice_address_id;
	}
	
	public function getDeliveryAdressID()
	{
		return $this->delivery_address_id;
	}
	
	public function setDeliveryAdressID($delivery_address_id)
	{
		$this->delivery_address_id = $delivery_address_id;
	}
	
	/**
     * @return the $entryid
     */
    public function getEntryid()
    {
        return $this->entryid;
    }

	/**
     * @param number $entryid
     */
    public function setEntryid($entryid)
    {
        $this->entryid = $entryid;
    }
    
	/**
     * @return the $file
     */
    public function getFile()
    {
        return $this->file;
    }

	/**
     * @param field_type $file
     */
    public function setFile($file)
    {
        $this->file = $file;
    }
}
?>