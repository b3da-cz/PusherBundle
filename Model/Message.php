<?php
/**
 * Created by PhpStorm.
 * User: b3da
 * Date: 17.9.16
 * Time: 10:20
 */

namespace b3da\PusherBundle\Model;


class Message
{
    /**
     * @var string $title
     */
    private $title;

    /**
     * @var string $body
     */
    private $body;

    /**
     * Notification sound
     * defaults to 'default'
     * can be 'none', 'default' or notification sound name
     *
     * @var string $sound
     */
    private $sound;

    /**
     * Android: increment for multiple notifications shown simultaneously
     *
     * @var integer $notificationId
     */
    private $notificationId;

    /**
     * Message constructor
     * @param string $title
     * @param string $body
     * @param string $sound
     * @param int $notificationId
     */
    public function __construct($title, $body, $sound = 'default', $notificationId = 1)
    {
        $this->title = $title;
        $this->body = $body;
        $this->sound = $sound;
        $this->notificationId = $notificationId;
    }

    /**
     * @return array
     */
    public function composeAndroidGcmMessage() {
        $message = [
            'title' => $this->title,
            'message' => $this->body,
            'sound' => $this->sound,
            'notId' => $this->notificationId,
        ];

        return $message;
    }

    /**
     * @return array
     */
    public function composeAndroidFcmMessage() {
        $message = [
            'title' => $this->title,
            'text' => $this->body,
            'sound' => $this->sound,
            'tag' => 'notification_' . $this->notificationId,
        ];

        return $message;
    }

    /**
     * @return array
     */
    public function composeIosMessage() {
        $message = [
            'alert' => [
                'title' => $this->title,
                'body' => $this->body,
            ],
            'sound' => $this->sound,
        ];

        return $message;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param string $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * @return string
     */
    public function getSound()
    {
        return $this->sound;
    }

    /**
     * @param string $sound
     */
    public function setSound($sound)
    {
        $this->sound = $sound;
    }

    /**
     * @return int
     */
    public function getNotificationId()
    {
        return $this->notificationId;
    }

    /**
     * @param int $notificationId
     */
    public function setNotificationId($notificationId)
    {
        $this->notificationId = $notificationId;
    }
}
