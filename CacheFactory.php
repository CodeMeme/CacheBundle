<?php

namespace CodeMeme\CacheBundle;

class CacheFactory
{

    public function createCache($debugKeys = array(), $debug = false)
    {
        $adapter = new \Memcache;
        $adapter->connect('127.0.0.1');
        
        return new Cache($adapter, $debugKeys, $debug);
    }

}