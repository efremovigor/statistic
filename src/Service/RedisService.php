<?php


namespace App\Service;

use Redis;
use Symfony\Component\Cache\Adapter\RedisAdapter;

class RedisService
{

    private Redis $connection;

    /**
     * todo::вынести в конфиги
     * RedisService constructor.
     */
    public function __construct()
    {
        $this->connection = RedisAdapter::createConnection('redis://localhost');
    }

    /**
     * @return Redis
     */
    public function getConnection(): Redis
    {
        return $this->connection;
    }
}