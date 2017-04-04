<?php


class DnifLogger
{
    /**
     * An instance of the Consumers_Consumer class
     * @var Consumers_AbstractConsumer The consumer implementation to use
     */
    private $consumer;

    /**
     * An instance of the DnifLogger class (for singleton use)
     * @var DnifLogger
     */
    private static $instance;

    /**
     * DnifLogger constructor.
     * @param $consumer
     */
    public function __construct($consumer)
    {
        $this->consumer = $consumer;
    }

    public static function getInstance($consumer)
    {
        if (!isset(self::$instance)) {
            self::$instance = new DnifLogger($consumer);
        }
        return self::$instance;
    }

    /**
     * Log statement to DNIF.
     * @param array $data
     */
    public function log($data = array())
    {
        $this->consumer->send($data);
    }
}
