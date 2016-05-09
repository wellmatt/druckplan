<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */

class Cachehandler
{
    private $active = true;

    function __construct()
    {
        
    }

    /**
     * @param int $id
     * @param $object
     * @return bool|string
     */
    public static function genKeyword($object, $id = null)
    {
        global $_CONFIG;
        if (method_exists($object,'getId')){
            $keyword = $_CONFIG->cookieSecret;
            $keyword .= '_'.get_class($object);
            if ($id != null)
                $keyword .= '_'.$id;
            else
                $keyword .= '_'.$object->getId();
            return $keyword;
        }
        return false;
    }

    /**
     * @param $object
     * @param string $keyword
     * @return boolean
     */
    public static function loadOrFail(&$object, $keyword)
    {
        if ($keyword === false)
            return false;
        if (self::exists($keyword)){
            $cached = self::fromCache($keyword);
            if (get_class($cached) == get_class($object)){
                $vars = array_keys(get_class_vars(get_class($object)));
                foreach ($vars as $var)
                {
                    $method = "get".ucfirst($var);
                    $method = str_replace("_", "", $method);
                    if (method_exists($object,$method))
                    {
                        $object->$var = $cached->$method();
                    } else {
                        prettyPrint('Cache Error: Method "'.$method.'" not found in Class "'.get_called_class().'"');
                    }
                }
                return true;
            }
        }
        return false;
    }

    public static function exists($keyword)
    {
        $cache = phpFastCache("memcached");
        $return = $cache->isExisting($keyword);
        return $return;
    }
    
    public static function fromCache($keyword)
    {
        $cache = phpFastCache("memcached");
        $return = $cache->get($keyword);
        return $return;
    }
    
    public static function removeCache($keyword)
    {
        $cache = phpFastCache("memcached");
        $cache->delete($keyword);
    }
    
    public static function toCache($keyword, $object, $time = 86400)
    {
        if (self::exists($keyword)){
            self::removeCache($keyword);
        }
        $cache = phpFastCache("memcached");
        $cache->set($keyword, $object, $time);
    }

    /**
     * @return boolean
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @param boolean $active
     */
    public function setActive($active)
    {
        $this->active = $active;
    }
}

?>