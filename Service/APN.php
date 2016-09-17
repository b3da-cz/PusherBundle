<?php
/**
 * Created by PhpStorm.
 * User: b3da
 * Date: 17.9.16
 * Time: 9:23
 */

namespace b3da\PusherBundle\Service;


class APN
{
    /**
     * Server url
     *
     * @var string $url
     */
    private $url;

    /**
     * Certificate path
     *
     * @var string $certPath
     */
    private $certPath;

    /**
     * Passphrase for APNS
     *
     * @var string $passphrase
     */
    private $passphrase;

    /**
     * cUrl stringified result | null on error
     *
     * @var null|string $output
     */
    private $output;

    /**
     * APN constructor.
     * @param $passphrase
     */
    public function __construct($url, $passphrase, $certPath, $rootDir)
    {
        $this->url = $url;
        $this->passphrase = $passphrase;
        $this->certPath = $rootDir . '/../' . $certPath;
    }

    /**
     * @param string|array $regId
     * @param string $data
     * @throws \Exception
     */
    public function notify($regId, $data)
    {
        $ctx = stream_context_create();

        stream_context_set_option($ctx, 'ssl', 'local_cert', $this->certPath);
        stream_context_set_option($ctx, 'ssl', 'passphrase', $this->passphrase);

        $fp = stream_socket_client($this->url, $err, $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx); // Open a connection to the APNS server
        if (!$fp) {
            throw new \Exception('Failed to connect:' . $err . ' - ' . $errstr);
        }
        $body['aps'] = $data;
        $payload = json_encode($body);

        $msg = chr(0) . pack('n', 32) . pack('H*', $regId) . pack('n', strlen($payload)) . $payload; // Build the binary notification
        $result = fwrite($fp, $msg, strlen($msg)); // Send it to the server
        if (!$result) {
            throw new \Exception('Message not delivered');
        } else {
            $this->output = $result;
        }

        fclose($fp);
    }

    /**
     * @return array
     */
    public function getOutputAsArray()
    {
        return json_decode($this->output, true);
    }

    /**
     * @return object
     */
    public function getOutputAsObject()
    {
        return json_decode($this->output);
    }
}
