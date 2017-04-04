<?php


/**
 * Class Consumers_Consumer
 *
 * Abstract class representing different types of consumers to process data
 * produced by various logging statements/producers.
 * It should be noted that the name Consumer here is a slight misnomer, because
 * it isn't working independently/asynchronously, and async implementations will
 * probably have to maintain/manage their own FIFO structures.
 */
abstract class Consumers_AbstractConsumer
{
    abstract public function send($data);
}


/**
 * Class Consumers_HttpConsumer
 * Takes the provided data and sends it over to the specified HTTP endpoint.
 * This implementation is blocking.
 */
class Consumers_BlockingHttpConsumer extends Consumers_AbstractConsumer
{
    private $url;
    private $timeout;
    private $connect_timeout;

    /**
     * Consumers_BlockingHttpConsumer constructor.
     * @param string $url the URL to send to
     * @param array $options additional options
     * @throws Exception
     */
    public function __construct($url, $options = array())
    {
        $this->url = $url;
        $this->connect_timeout = array_key_exists('connect_timeout', $options) ? $options['connect_timeout'] : 5;
        $this->timeout = array_key_exists('connect_timeout', $options) ? $options['connect_timeout'] : 15;

        // ensure the environment is workable for the given settings
        if (!function_exists('curl_init')) {
            throw new Exception('The cURL PHP extension is required to use the cURL consumer.');
        }
        return;
    }

    /**
     * @param $data array: LIST (sequential array) of key value pairs (associative array)
     * Example: [{"a": 1, "b": "hello"}, {"a": 1, "b": "hello"}]
     */
    public function send($data)
    {
        if (!is_array($data)) {
            return;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->connect_timeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        curl_exec($ch);
        curl_close($ch);
    }
}


/**
 * Class Consumers_SocketConsumer
 * Takes the provided data and sends it over to the specified HTTP endpoint.
 * This implementation is blocking.
 */
class Consumers_SocketConsumer extends Consumers_AbstractConsumer
{
    private $ip;
    private $port;
    private $socket;

    /**
     * Consumers_SocketConsumer constructor.
     * @param $ip string: the IPv4 address of Adapter
     * @param $port string: the port to connect to
     * @param array $options additional options
     * @throws Exception
     * @internal param string $url the URL to send to
     */
    public function __construct($ip, $port, $options = array())
    {
        $this->ip = $ip;
        $this->port = $port;
        $this->socket = socket_create(AF_INET, SOCK_DGRAM, 0);

        if (!$this->socket) {
            $error_code = socket_last_error();
            $error_msg = socket_strerror($error_code);
            throw new Exception("Could not create socket: [$error_code] $error_msg \n");
        }

        return;
    }

    /**
     * @param $data string message to send
     */
    public function send($data)
    {
        if (!is_string($data)) {
            return;
        }

        $result = socket_sendto($this->socket, $data, strlen($data), 0, $this->ip, $this->port);
        if(!$result)
        {
            $error_code = socket_last_error();
            $error_msg = socket_strerror($error_code);
            error_log("Could not send data: [$error_code] $error_msg \n");
        }

    }
}
