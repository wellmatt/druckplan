<?php
class Translator {
    const ORDER_ID = "id";
    const ORDER_NAME = "language";
    const ORDER_COUNTRY_NAME = "country";

    private $trans = Array();
    private $id;
    private $langName;
    private $langNameInt;
    private $langCode;
    private $loaded = false;
     
    function __construct($lang = 1, $readTranslation = false)
    {
        global $DB;

        $lang = (int)$lang;
        $sql = " SELECT * FROM language WHERE id = {$lang}";

        if ($DB->num_rows($sql) > 0)
        {
            $lang = $DB->select($sql);
            $lang = $lang[0];
            $this->id = $lang["id"];
            $this->langName = $lang["language"];
            $this->langNameInt = $lang["language_int"];
            $this->langCode = $lang["language_code"];

            if($readTranslation)
            {
                global $_CONFIG;
                //datei einlesen
                $filename = $this->langCode.".xml";
                $path = $_CONFIG->pathTranslations.$filename;
                if (file_exists($path))
                {
                    $this->trans = simplexml_load_file($path);
                    if ($this->trans["lang"] != $this->langCode)
                    {
                        unset ($this->trans->dictentry);
                        $this->trans->dictentry = Array();
                        return false;
                    } else
                    {
                        $this->loaded = true;
                        return true;
                    }
                } else
                    return false;
            }
        }
    }
     
    /* Übersetzung für $s ausgeben */
    function get($s)
    {
    	global $_CONFIG;
        $s  = str_replace("&auml;", "ä", $s);
        $s = str_replace("&Auml;", "Ä", $s);
        $s = str_replace("&Ouml;","Ö", $s);
        $s = str_replace("&Uuml;", "Ü", $s);
        $s = str_replace("&ouml;", "ö", $s);
        $s = str_replace("&uuml;", "ü", $s);
        $s = str_replace("&szlig;", "ß", $s);
        $s = utf8_encode($s);
         
        if ($this->loaded)
            // Nach Eintrag suchen
            foreach($this->trans->dictentry as $entry)
            {
                //echo $entry->source."<br>\n";
                if ($entry->source == $s)
                {
                    return $entry->translate;
                }
            }
             
            // Keine Übersetzung gefunden
            if($_CONFIG->logTranslations)
            {
                if (!$handle = fopen('german.txt', "ab")) {
                    print "Kann die Datei $filename nicht &ouml;ffnen";
                    exit;
                }

                if (!fwrite($handle, $s."\n")) {
                    print "Kann in die Datei nicht schreiben";
                    exit;
                }

                fclose($handle);
            }


            return $s;
    }

    function getId() {
        return $this->id;
    }

    function getName() {
        return $this->langName;
    }

    function getNameInt()
    {
        return $this->langNameInt;
    }

    function getCode() {
        return $this->langCode;
    }


    static function getAllLangs($order = self::ORDER_ID)
    {
        global $DB;
        $sql = " SELECT * FROM language WHERE language_active = 1 ORDER BY {$order}";
        $res = $DB->select($sql);

        $temp = Array();
        foreach ($res as $r)
        {
            $temp[] = new Translator($r["id"]);
        }
        return $temp;
    }
}
?>