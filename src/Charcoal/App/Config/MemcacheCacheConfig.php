<?php

namespace Charcoal\App\Config;

// Local parent namespace dependencies
use \Charcoal\Config\AbstractConfig;

/**
 * Memcache Cache Server Config
 *
 * Defines a memcache server configuration.
 */
class MemcacheCacheConfig extends AbstractConfig
{
    /**
     * @var string $host
     */
    public $host;

    /**
     * @var integer $port
     */
    public $port;

    /**
     * @var boolean $persistent
     */
    public $persistent;

    /**
     * @var integer $weight
     */
    public $weight;

    /**
     * @return array
     */
    public function defaults()
    {
        return [
            'host'       => 'localhost',
            'port'       => 11211,
            'persistent' => true,
            'weight'     => 1
        ];
    }

    /**
     * @param string $host The memcache server host.
     * @return self
     */
    public function setHost($host)
    {
        $this->host = $host;
        return $this;
    }

    /**
     * @return string
     */
    public function host()
    {
        return $this->host;
    }

    /**
     * @param integer $port The memcache server port.
     * @return self
     */
    public function setPort($port)
    {
        $this->port = $port;
        return $this;
    }

    /**
     * @return integer
     */
    public function port()
    {
        return $this->port;
    }

    /**
     * @param boolean $persistent The persistent flag.
     * @return self
     */
    public function setPersistent($persistent)
    {
        $this->persistent = $persistent;
        return $this;
    }

    /**
     * @return boolean
     */
    public function persistent()
    {
        return $this->persistent;
    }

    /**
     * @param integer $weight The weight of this server, relative to other's weight.
     * @return self
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;
        return $this;
    }

    /**
     * @return integer
     */
    public function weight()
    {
        return $this->weight;
    }
}
