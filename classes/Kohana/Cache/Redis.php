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

    public function get($id, $default = null)
	{
	    if ($this->redisClient->exists($id)) {
            $data = $this->redisClient->get($id);
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

	public function delete($id)
	{
	    return $this->redisClient->del((array) $id);
	}

    public function delete_all()
    {
        return $this->redisClient->flushall();
    }
}
