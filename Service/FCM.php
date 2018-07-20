<?php
/**
 * Created by PhpStorm.
 * User: b3da
 * Date: 17.9.16
 * Time: 8:22
 */

namespace b3da\PusherBundle\Service;


class FCM
{
    /**
     * Server url
     *
     * @var string $url
     */
    private $url;

    /**
     * Server Key from Firebase console
     *
     * @var string $apiKey
     */
    private $apiKey;

    /**
     * Optional proxy url
     * Defaults to null
     *
     * @var null|string $proxy
     */
    private $proxy;

    /**
     * cUrl stringified result | null on error
     *
     * @var null|string $output
     */
    private $output;

    /**
     * FCM constructor.
     * @param string $url
     * @param string $apiKey
     * @param null|string $proxy
     */
    public function __construct($url, $apiKey, $proxy = null)
    {
        $this->url = $url;
        $this->apiKey = $apiKey;
        $this->proxy = $proxy;
    }

    /**
     * @param string|array $regIds
     * @param string|array|null $notification
     * @param string|array|null $data
     * @throws \Exception
     */
    public function notify($regIds, $notification = null, $data = null)
    {
        if (!$notification && !$data) {
            throw new \Exception('FCM: nothing to send!');
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url);
        if (!is_null($this->proxy)) {
            curl_setopt($ch, CURLOPT_PROXY, $this->proxy);
        }
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->getHeaders());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->getPostFields($regIds, $notification, $data));

        $result = curl_exec($ch);
        if ($result === false) {
            throw new \Exception(curl_error($ch));
        }

        curl_close($ch);

        $this->output = $result;
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

    /**
     * @return array
     */
    private function getHeaders()
    {
        return [
            'Authorization: key=' . $this->apiKey,
            'Content-Type: application/json',
        ];
    }

    /**
     * @param string|array $regIds
     * @param string|array|null $notification
     * @param string|array|null $data
     * @return string
     */
    private function getPostFields($regIds, $notification = null, $data = null)
    {
        is_string($regIds) ? $recipientKey = 'to' : $recipientKey = 'registration_ids';
        $fields = [$recipientKey => $regIds];
        if ($notification) {
            $fields['notification'] = is_string($notification) ? ['text' => $notification] : $notification;
        }
        if ($data) {
            $fields['data'] = is_string($data) ? ['value' => $data] : $data;
        }

        return json_encode($fields, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
    }
}
