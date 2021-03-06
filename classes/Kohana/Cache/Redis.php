<?php

use Predis\Client;

class Kohana_Cache_Redis extends Cache
{
    protected $redisClient;

    protected function __construct(array $config)
    {
        parent::__construct($config);

        $connectionParams = Arr::get($config, 'connection');
        $this->redisClient = new Client($connectionParams);
    }
    
        
    public function exists($key)
    {
        return $this->redisClient->exists($key);
    }

    public function get($id, $default = null)
    {
        if ($this->redisClient->exists($id)) {
            $data = $this->redisClient->get($id);
            return unserialize($data);
        }
        return $default;
    }

    public function hget($key, $field, $default = null)
    {
        if ($this->redisClient->hexists($key, $field)) {
            $data = $this->redisClient->hget($key, $field);
            return unserialize($data);
        }
        return $default;
    }

    public function set($id, $data, $lifetime = null)
    {
        if (null === $lifetime) {
            $lifetime = $this->config('default_expire');
        }
        return $this->redisClient->setex($id, $lifetime ?: Cache::DEFAULT_EXPIRE, serialize($data));
    }

    public function hset($key, $field, $data, $lifetime = null)
    {
        $exists = $this->redisClient->exists($key);
        if (null === $lifetime) {
            $lifetime = $this->config('default_expire');
        }
        $hsetResult = $this->redisClient->hset($key, $field, serialize($data));
        if (!$exists) {
            $this->redisClient->expire($key, $lifetime ?: Cache::DEFAULT_EXPIRE);
        }
        return $hsetResult;
    }

    public function sadd($key, $data, $lifetime = null)
    {
        $exists = $this->redisClient->exists($key);
        if (null === $lifetime) {
            $lifetime = $this->config('default_expire');
        }
        $saddResult = $this->redisClient->sadd($key, (array) $data);
        if (!$exists) {
            $this->redisClient->expire($key, $lifetime ?: Cache::DEFAULT_EXPIRE);
        }
        return $saddResult;
    }

    public function srem($key, $value) {
        return $this->redisClient->srem($key, $value);
    }

    public function smembers($key)
    {
        return $this->redisClient->smembers($key);
    }

    public function sismember($key, $value)
    {
        return $this->redisClient->sismember($key, $value);
    }

    public function rename($oldKey, $newKey)
    {
        return $this->redisClient->rename($oldKey, $newKey);
    }

    public function delete($id)
    {
        return $this->redisClient->del((array) $id);
    }

    public function delete_all()
    {
        return $this->redisClient->flushall();
    }
}
