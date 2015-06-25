<?php

class Cachehandler
{

    function __construct()
    {
        
    }
    
    public static function fromCache($keyword)
    {
        $cache = phpFastCache("memcache");
        $return = $cache->get($keyword);
        return $return;
    }
    
    public static function removeCache($keyword)
    {
        $cache = phpFastCache("memcache");
        $cache->delete($keyword);
    }
    
    public static function toCache($keyword, $object, $time = 86400)
    {
        $cache = phpFastCache("memcache");
        $cache->set($keyword, $object, $time);
    }
}

?>