<?php

namespace CodeMeme\CacheBundle;

class CacheFactory
{

    public function createCache()
    {
        $adapter = new \Memcache;
        $adapter->connect('127.0.0.1');
        
        return new Cache($adapter);
    }

}