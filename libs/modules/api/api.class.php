<?php
require_once 'libs/modules/api/api.object.class.php';

class API
{

    private $id;
    private $title;
    private $type;
    private $token;
    private $posturl;
    
    const TYPE_ARTICLE = 1;
    
    function __construct($id = 0)
    {
       global $DB;
       global $_USER;
       
       if ($id > 0){
           $sql = "SELECT * FROM apis WHERE id = {$id}";
           if($DB->num_rows($sql)){
               $r = $DB->select($sql);
               $r = $r[0];
               $this->id        = $r["id"];
               $this->title     = $r["title"];
               $this->type      = $r["type"];
               $this->token     = $r["token"];
               $this->posturl   = $r["posturl"];
           }
       }
    }
    
    function save(){
        global $DB;
        global $_USER;
    
        if($this->id > 0){
            $sql = "UPDATE apis SET
                    type 	= {$this->type},
                    title 	= '{$this->title}',
                    token 	= '{$this->token}',
                    posturl	= '{$this->posturl}',
                    WHERE  id = {$this->id}";
            $res = $DB->no_result($sql);
        } else {
            $sql = "INSERT INTO apis
            (type, token, title, posturl)
            VALUES
            ({$this->type}, '{$this->token}', '{$this->title}', '{$this->posturl}')";
            $res = $DB->no_result($sql);
            
		}
		
        if($res){
            $sql = "SELECT max(id) id FROM apis WHERE token = '{$this->token}'";
            $thisid = $DB->select($sql);
            $this->id = $thisid[0]["id"];
            $res = true;
        } else {
            $res = false;
        }
    }
    
	public function delete(){
		global $DB;
		if($this->id > 0){
			$sql = "DELETE FROM apis 
					WHERE id = {$this->id}";
			if($DB->no_result($sql)){
				unset($this);
				return true;
			} else {
				return false;
			}
		}
	}
	
	public static function getAllApis()
	{
	    global $DB;
	    $retval = Array();
	    $sql = "SELECT id FROM apis";
	    if($DB->num_rows($sql))
	    {
			foreach($DB->select($sql) as $r){
				$retval[] = new API($r["id"]);
			}
	    }
	    return $retval;
	}
	
	public static function getAllApisByType($type)
	{
	    global $DB;
	    $retval = Array();
	    $sql = "SELECT id FROM apis WHERE type = {$type}";
	    if($DB->num_rows($sql))
	    {
			foreach($DB->select($sql) as $r){
				$retval[] = new API($r["id"]);
			}
	    }
	    return $retval;
	}
	
	public static function findByToken($token)
	{
	    global $DB;
	    $retval = new API();
	    $token = mysql_escape_string($token);
	    $sql = "SELECT id FROM apis WHERE token = '{$token}'";
	    if($DB->num_rows($sql))
	    {
           $r = $DB->select($sql);
           $r = $r[0];
           $retval = new API($r["id"]);
	    }
	    return $retval;
	}
	
	public static function returnType($type)
	{
	    switch ($type)
	    {
	        case 1:
	            return "Artikel";
	            break;
	    }
	}
	
	public function generateToken()
	{
	    $this->token = md5("contilas".time());
	}
	
	public static function postChangedItem($item,$type)
	{
	    switch ($type)
	    {
	        case self::TYPE_ARTICLE:
	            
	            break;
	        default:
	            break;
	    }
	}
	
	public function returnApiValues($item = 0)
	{
	    $ret = "";
	    switch ($this->type)
	    {
	        case self::TYPE_ARTICLE:
	            if ($item==0)
	            {
	                $all_art_ids = Article::getAllArticleIdsForApi($this->id);
	                $allarts = Array();
	                foreach ($all_art_ids as $artid)
	                {
	                    $allarts[] = Array("id"=>$artid["id"], "uptdate"=>$artid["uptdate"], "url"=>"http://".$_SERVER['SERVER_NAME']."/api.php?token=".$this->token."&item=".$artid["id"]);
	                }
	                $ret = Array("count"=>count($all_art_ids),"items"=>$allarts);
	            } else
	            {
	                $article = new Article($item);
	                $art_pictures = $article->getAllPictures();
	                $artpics = Array();
	                if (count($art_pictures)>0 && $art_pictures != False)
	                {
    	                foreach ($art_pictures as $artpic)
    	                {
    	                    $artpics[] = "http://".$_SERVER['SERVER_NAME']."/images/products/".$artpic["url"];
    	                }
	                }
	                
	                $ret = Array(
	                    "id"=>$article->getId(),
	                    "title"=>nl2br(htmlentities(utf8_encode($article->getTitle()))),
	                    "desc"=>nl2br(htmlentities(utf8_encode($article->getDesc()))),
	                    "tradegroupid"=>$article->getTradegroup()->getId(),
	                    "tradegroup"=>nl2br(htmlentities(utf8_encode($article->getTradegroup()->getTitle()))),
	                    "prices"=>$article->getPrices(),
	                    "pictures"=>$artpics,
	                    "update_date"=>$article->getUpt_date(),
	                    "tax"=>$article->getTax(),
	                    "shop_needs_upload"=>$article->getShop_needs_upload(),
	                    "tags"=>$article->getTags(),
	                    "orderamounts"=>$article->getOrderamounts()
	                    );
	            }
	            
	            break;
	        default:
	            break;
	    }
	    return json_encode($ret);
	}
	
	/**
     * @return the $id
     */
    public function getId()
    {
        return $this->id;
    }

	/**
     * @return the $type
     */
    public function getType()
    {
        return $this->type;
    }

	/**
     * @return the $token
     */
    public function getToken()
    {
        return $this->token;
    }

	/**
     * @param field_type $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

	/**
     * @param field_type $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }
    
	/**
     * @return the $title
     */
    public function getTitle()
    {
        return $this->title;
    }

	/**
     * @param field_type $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }
    
	/**
     * @return the $posturl
     */
    public function getPosturl()
    {
        return $this->posturl;
    }

	/**
     * @param field_type $posturl
     */
    public function setPosturl($posturl)
    {
        $this->posturl = $posturl;
    }
}

?>