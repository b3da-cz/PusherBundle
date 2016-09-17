### b3da\PusherBundle

Symfony bundle for Android and Ios push notifications


#### Installation

* add package to your project
```sh
$ composer require b3da/pusher-bundle "dev-master"
```

* add bundle into `AppKernel`
```php
new b3da\PusherBundle\b3daPusherBundle(),
```

* add configuration for desired services in `config.yml`
```yaml
b3da_pusher:
    fcm:  # Firebase Cloud Messaging
        server_key: 'foobarbaz'
    gcm:  # Google Cloud Messaging (deprecated)
        server_key: 'foobarbaz'
    apn:  # Apple Push Notification Service
        passphrase: 'foobarbaz'
        cert_path: 'cert.pem'  # relative to app root dir
```

* optional - import routes in `routing.yml` and visit `/pusher/api/doc/` for more info
```yaml
b3da_pusher:
    resource: "@b3daPusherBundle/Controller/"
    type:     annotation
    prefix:   "/pusher/"
```


#### Usage

* Android - Firebase Cloud Messaging
```php
$msgSound = 'default';  # optional - can be 'default', 'none', or notification sound name
$msgNotoficationId = 1;  # optional - increment for display multiple notification simultaneously
$fcm = $this->get('b3da_pusher.android.fcm');
$message = new b3da\PusherBundle\Model\Message('title', 'message body', $msgSound, $msgNotificationId);
$fcm->notify($recipient, $message->composeAndroidFcmMessage());
# result:
dump($fcm->getOutputAsObject());
```

* Android - Google Cloud Messaging (deprecated)
```php
$msgSound = 'default';  # optional - can be 'default', 'none', or notification sound name
$msgNotoficationId = 1;  # optional - increment for display multiple notification simultaneously
$gcm = $this->get('b3da_pusher.android.gcm');
$message = new b3da\PusherBundle\Model\Message('title', 'message body', $msgSound, $msgNotificationId);
$gcm->notify($recipient, $message->composeAndroidGcmMessage());
# result:
dump($gcm->getOutputAsObject());
```

* IOS - Apple Push Notification Service
```php
$msgSound = 'default';  # optional - can be 'default', 'none', or notification sound name
$gcm = $this->get('b3da_pusher.ios.apn');
$message = new b3da\PusherBundle\Model\Message('title', 'message body', $msgSound);
$apn->notify($recipient, $message->composeIosMessage());
# result:
dump($apn->getOutputAsObject());
```


#### Full configuration with defaults
```yaml
b3da_pusher:
    fcm:
        server_url: 'https://fcm.googleapis.com/fcm/send'
        server_key: null   # must be defined to use FCM
        proxy: null
    gcm:
        server_url: 'https://android.googleapis.com/gcm/send'
        server_key: null   # must be defined to use GCM
        proxy: null
    apn:
        server_url: 'ssl://gateway.sandbox.push.apple.com:2195'
        passphrase: null   # must be defined to use APN
        cert_path: 'cert.pem'   # must be defined to use APN
```