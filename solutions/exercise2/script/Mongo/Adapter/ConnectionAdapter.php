<?php

namespace Mongo\Adapter;

use MongoClient;
use MongoDB;
use Exception;

/**
 * Class ConnectionAdapter
 * @package Mongo\Adapter
 */
class ConnectionAdapter
{
    /* Self instance */
    private static $instance;
    /* MongoClient instance */
    private $conn;
    /* Connection properties */
    private $db;

    /**
     * @param string $host
     * @param int $port
     * @param null $db
     */
    public function __construct($host = 'localhost', $port = 27017, $db = null)
    {
        $this->conn = $conn = new MongoClient('mongodb://' . $host . ':' . $port);
        $this->db = $this->conn->selectDB($db);
    }

    /**
     * @return ConnectionAdapter
     */
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new ConnectionAdapter();
        }

        return self::$instance;
    }

    /**
     * @return MongoDB
     * @throws Exception
     */
    public function getDatabase()
    {
        if (!isset($this->db)) {
            throw new Exception('Database is not set in MongoAdapter instance.');
        }

        return $this->db;
    }

    /**
     * @param $db
     */
    public function setDatabase($db)
    {
        $this->db = $this->conn->selectDB($db);
    }
} 