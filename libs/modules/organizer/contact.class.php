<?
class UserContact {
    const ORDER_ID = "id";
    const ORDER_NAME = "name1";
    
    private $id;
    private $name1;
    private $name2;
    private $address1;
    private $address2;
    private $postcode;
    private $city;
    private $phone;
    private $fax;
    private $cellphone;
    private $email;
    private $website;
    private $notes;
    private $public = 0;
    private $user;
    private $country = NULL;
    
    public function __construct($id = 0)
    {
        global $DB;
        if($id > 0)
        {
            $sql = "SELECT * FROM user_contacts WHERE id={$id}";
            if($DB->num_rows($sql))
            {
                $r = $DB->select($sql);
                $r = $r[0];
                $this->id = $r["id"];
                $this->name1 = $r["name1"];
                $this->name2 = $r["name2"];
                $this->address1 = $r["address1"];
                $this->address2 = $r["address2"];
                $this->postcode = $r["postcode"];
                $this->city = $r["city"];
                $this->phone = $r["phone"];
                $this->fax = $r["fax"];
                $this->email = $r["email"];
                $this->website = $r["website"];
                $this->cellphone = $r["cellphone"];
                $this->notes = $r["notes"];
                $this->public = $r["public"];
                $this->user = new User($r["user_id"]);
                $this->country = new Country($r["country"]);
            }
        }
    }
    
    static function getAllUserContacts($order = self::ORDER_NAME, $uid = 0)
    {
        global $DB;
        global $_USER;
        if ($user == 0)
            $user = $_USER;
        
        $retval = Array();
        
        $sql = "SELECT id FROM user_contacts 
                WHERE status = 1
                    AND 
                        (user_id = {$user->getId()}
                         OR public = 1)
                ORDER BY {$order}";
        
        if ($DB->num_rows($sql))
        {
            foreach($DB->select($sql) as $r)
            {
                $retval[] = new UserContact($r["id"]);
            }
        }
        return $retval;
    }
    
    function save()
    {
        global $DB;
        global $_USER;

        if($this->id > 0)
        {
            $sql = "UPDATE user_contacts SET
                        name1 = '{$this->name1}',
                        name2 = '{$this->name2}',
                        address1 = '{$this->address1}',
                        address2 = '{$this->address2}',
                        postcode = '{$this->postcode}',
                        city = '{$this->city}',
                        phone = '{$this->phone}',
                        fax = '{$this->fax}',
                        email = '{$this->email}', 
                        cellphone = '{$this->cellphone}',
                        website = '{$this->website}',
                        notes = '{$this->notes}',
                        public = {$this->public},
                        country = {$this->country->getId()}
                    WHERE id = {$this->id}";

            return $DB->no_result($sql);
        } else
        {
            $sql = "INSERT INTO user_contacts
                        (name1, name2, address1, address2, postcode, city,
                         phone, fax, email, cellphone, website, notes, public,
                         status, user_id, country)
                    VALUES
                        ('{$this->name1}', '{$this->name2}', '{$this->address1}', '{$this->address2}',
                         '{$this->postcode}', '{$this->city}', '{$this->phone}', '{$this->fax}',
                         '{$this->email}', '{$this->cellphone}', '{$this->website}', '{$this->notes}', 
                         {$this->public}, 1, {$_USER->getId()}, {$this->country->getId()})";
            $res = $DB->no_result($sql);
            if($res)
            {
                $sql = "SELECT max(id) id FROM user_contacts WHERE user_id = {$_USER->getId()}";
                $thisid = $DB->select($sql);
                $this->id = $thisid[0]["id"];
                return true;
            } else
                return false;
        }
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName1()
    {
        return $this->name1;
    }

    public function getName2()
    {
        return $this->name2;
    }
    
    public function getNameAsLine()
    {
        $retval = $this->name1;
        if($this->name2 != "")
            $retval .= "\n".$this->name2;
        return $retval;
    }

    public function getAddress1()
    {
        return $this->address1;
    }

    public function getAddress2()
    {
        return $this->address2;
    }
    
    public function getAddressAsLine()
    {
        $retval = $this->address1;
        if($this->address2 != "")
            $retval .= "\n".$this->address2;
        if($this->postcode || $this->city)
            $retval .= "\n".$this->country->getCode()."-".$this->postcode." ".$this->city;
        return $retval;
    }

    public function getPostcode()
    {
        return $this->postcode;
    }

    public function getCity()
    {
        return $this->city;
    }

    public function getPhone()
    {
        return $this->phone;
    }

    public function getFax()
    {
        return $this->fax;
    }

    public function getCellphone()
    {
        return $this->cellphone;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getWebsite()
    {
        return $this->website;
    }

    public function getNotes()
    {
        return $this->notes;
    }

    public function getPublic()
    {
        return $this->public;
    }
    
    public function getCountry()
    {
        return $this->country;
    }

    public function setName1($name1)
    {
        $this->name1 = $name1;
    }

    public function setName2($name2)
    {
        $this->name2 = $name2;
    }

    public function setAddress1($address1)
    {
        $this->address1 = $address1;
    }

    public function setAddress2($address2)
    {
        $this->address2 = $address2;
    }

    public function setPostcode($postcode)
    {
        $this->postcode = $postcode;
    }

    public function setCity($city)
    {
        $this->city = $city;
    }

    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    public function setFax($fax)
    {
        $this->fax = $fax;
    }

    public function setCellphone($cellphone)
    {
        $this->cellphone = $cellphone;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function setWebsite($website)
    {
        $this->website = $website;
    }

    public function setNotes($notes)
    {
        $this->notes = $notes;
    }

    public function setPublic($public)
    {
        $this->public = $public;
    }
    
    public function setCountry($country)
    {
        $this->country = $country;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function delete()
    {
        global $DB;
        global $_USER;
        $sql = "UPDATE user_contacts SET status = 0 WHERE id = {$this->id}";
        $res = $DB->no_result($sql);
        if($res)
        {
            unset($this);
            return true;
        } else
            return false;
    }
    
}
?>