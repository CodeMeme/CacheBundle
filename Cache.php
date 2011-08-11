<?php

namespace CodeMeme\CacheBundle;

class Cache
{

    private $adapter;

    public function __construct($adapter = null)
    {
        $this->adapter = $adapter;
    }

    public function getAdapter()
    {
        return $this->adapter;
    }

    public function setAdapter($adapter)
    {
        $this->adapter = $adapter;
        
        return $this;
    }

    public function get($keyOrObject)
    {
        $key = $this->getKey($keyOrObject);

        // Get the expected expiration time
        $expiration = $this->adapter->get($this->getExpireKey($key));

        // If the key doesn't exist, then that data never expires
        // Otherwise, it expires if we're past the expiration date
        $isExpired = ($expiration === false)
                   ? false
                   : $expiration < time();

        return $isExpired ? false : $this->adapter->get($key);
    }

    public function set($keyOrObject, $data, $lifetime = null)
    {
        $key        = $this->getKey($keyOrObject);
        $expireKey  = $this->getExpireKey($key);

        if (null === $lifetime && $keyOrObject instanceof Cacheable) {
            $lifetime = $keyOrObject->getCacheLifetime();
        }

        $this->adapter->set($key, $data);

        if ($lifetime) {
            $this->adapter->set($expireKey, time() + $lifetime);
        }
    }

    public function delete($keyOrObject)
    {
        $key        = $this->getKey($keyOrObject);
        $expireKey  = $this->getExpireKey($key);

        $this->adapter->delete($key);
        $this->adapter->delete($expireKey);
    }

    public function getKey($keyOrObject)
    {
        if ($keyOrObject instanceof Cacheable) {
            $key = md5(get_class($keyOrObject) . serialize($keyOrObject->getCacheId()));
        } else if (is_scalar($keyOrObject)) {
            $key = $keyOrObject;
        } else {
            throw new Exception(get_class($keyOrObject) . ' must implement the "Cacheable" interface');
        }

        return $key;
    }

    public function getExpireKey($keyOrObject)
    {
        return $this->getKey($keyOrObject) . '.expiration';
    }

}