<?php

namespace CodeMeme\CacheBundle;

interface Cacheable
{

    public function getCacheId();
    public function getCacheLifetime();

}