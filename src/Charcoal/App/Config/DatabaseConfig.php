<?php

namespace Charcoal\App\Config;

use \InvalidArgumentException;

use \Charcoal\Config\AbstractConfig;

/**
 * Database Config
 */
class DatabaseConfig extends AbstractConfig
{
    /**
     * @var string $type
     */
    private $type;

    /**
     * @var string $hostname
     */
    private $hostname;

    /**
     * @var string $username
     */
    private $username;

    /**
     * @var string $password
     */
    private $password;

    /**
     * @var string $database
     */
    private $database;

    /**
     * @var boolean $disableUtf8
     */
    private $disableUtf8;

    /**
     * @return array
     */
    public function defaults()
    {
        return [
            'type'              => 'mysql',
            'hostname'          => 'localhost',
            'username'          => '',
            'password'          => '',
            'database'          => '',
            'disable_utf8'      => false
        ];
    }

    /**
     * @param string $type The database type.
     * @throws InvalidArgumentException If parameter is not a string.
     * @return self
     */
    public function setType($type)
    {
        if (!is_string($type)) {
            throw new InvalidArgumentException(
                'Source type must be a string.'
            );
        }
        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function type()
    {
        return $this->type;
    }

    /**
     * Set database hostname
     *
     * @param string $hostname The database server hostname.
     * @throws InvalidArgumentException If hostname is not a string.
     * @return self
     */
    public function setHostname($hostname)
    {
        if (!is_string($hostname)) {
            throw new InvalidArgumentException(
                'Hostname must be a string.'
            );
        }
        $this->hostname = $hostname;
        return $this;
    }

    /**
     * Get database hostname.
     *
     * @return string
     */
    public function hostname()
    {
        return $this->hostname;
    }

    /**
     * Set database username
     *
     * @param string $username The database authentication user.
     * @throws InvalidArgumentException If username is not a string.
     * @return self
     */
    public function setUsername($username)
    {
        if (!is_string($username)) {
            throw new InvalidArgumentException(
                'Username must be a string.'
            );
        }
        $this->username = $username;
        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function username()
    {
        return $this->username;
    }

    /**
     * Set password
     *
     * @param string $password The database authentication password.
     * @throws InvalidArgumentException If password is not a string.
     * @return self
     */
    public function setPassword($password)
    {
        if (!is_string($password)) {
            throw new InvalidArgumentException(
                'Password must be a string.'
            );
        }
        $this->password = $password;
        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function password()
    {
        return $this->password;
    }

    /**
     * Set database
     *
     * @param string $database The database name.
     * @throws InvalidArgumentException If database is not a string.
     * @return self
     */
    public function setDatabase($database)
    {
        if (!is_string($database)) {
            throw new InvalidArgumentException(
                'Database must be a string.'
            );
        }
        $this->database = $database;
        return $this;
    }

    /**
     * Get database
     *
     * @return string
     */
    public function database()
    {
        return $this->database;
    }

    /**
     * @param boolean $disableUtf8 The "disable utf8" flag.
     * @return self
     */
    public function setDisableUtf8($disableUtf8)
    {
        $this->disableUtf8 = !!$disableUtf8;
        return $this;
    }

    /**
     * @return boolean
     */
    public function disableUtf8()
    {
        return $this->disableUtf8;
    }
}
