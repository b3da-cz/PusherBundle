<?php
/**
 * Created by PhpStorm.
 * User: b3da
 * Date: 17.9.16
 * Time: 6:53
 */

namespace b3da\PusherBundle\Controller;


use b3da\PusherBundle\Model\Message;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * @Route("/api")
 */
class ApiController extends Controller
{
    /**
     * @Route("/doc/", name="b3da_pusher.api.doc")
     */
    public function docAction()
    {
        $config = [
            'fcm' => [
                'url' => $this->getParameter('b3da_pusher.fcm.server_url'),
                'key' => $this->getParameter('b3da_pusher.fcm.server_key'),
                'proxy' => $this->getParameter('b3da_pusher.fcm.proxy'),
            ],
            'gcm' => [
                'url' => $this->getParameter('b3da_pusher.gcm.server_url'),
                'key' => $this->getParameter('b3da_pusher.gcm.server_key'),
                'proxy' => $this->getParameter('b3da_pusher.gcm.proxy'),
            ],
            'apn' => [
                'url' => $this->getParameter('b3da_pusher.apn.server_url'),
                'passphrase' => $this->getParameter('b3da_pusher.apn.passphrase'),
                'cert_path' => $this->getParameter('b3da_pusher.apn.cert_path'),
            ],
        ];

        $androidFcmForm = $this->get('form.factory')->createNamedBuilder('form_android_fcm', FormType::class)
            ->add('recipient', TextType::class)
            ->add('title', TextType::class)
            ->add('message', TextType::class)
            ->add('notification_id', TextType::class, [
                'required' => false,
                'data' => 1,
            ])
            ->add('sound', TextType::class, [
                'required' => false,
                'data' => 'default'
            ])
            ->add('send', SubmitType::class)
            ->getForm()
        ;

        $androidGcmForm = $this->get('form.factory')->createNamedBuilder('form_android_gcm', FormType::class)
            ->add('recipient', TextType::class)
            ->add('title', TextType::class)
            ->add('message', TextType::class)
            ->add('notification_id', TextType::class, [
                'required' => false,
                'data' => 1,
            ])
            ->add('sound', TextType::class, [
                'required' => false,
                'data' => 'default'
            ])
            ->add('send', SubmitType::class)
            ->getForm()
        ;

        $iosApnForm = $this->get('form.factory')->createNamedBuilder('form_ios', FormType::class)
            ->add('recipient', TextType::class)
            ->add('title', TextType::class)
            ->add('message', TextType::class)
            ->add('sound', TextType::class, [
                'required' => false,
                'data' => 'default'
            ])
            ->add('send', SubmitType::class)
            ->getForm()
        ;
        return $this->render('b3daPusherBundle:Api:doc.html.twig', [
            'config' => $config,
            'android_fcm_form' => $androidFcmForm->createView(),
            'android_gcm_form' => $androidGcmForm->createView(),
            'ios_form' => $iosApnForm->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @Route("/send/android-fcm/", name="b3da_pusher.api.send_android_fcm")
     * @return JsonResponse
     */
    public function sendAndroidFcmAction(Request $request) {
        $recipient = $request->request->get('recipient');
        $msgTitle = $request->request->get('title');
        $msgData = $request->request->get('data');
        $msgSound = $request->request->get('sound');
        $msgNotificationId = $request->request->get('notification_id');
        $msgBody = $request->request->get('message');
        if (!$recipient || !$msgTitle || !$msgBody) {
            return new JsonResponse([
                'status' => 'error',
                'details' => 'not enough data',
            ], 400);
        }

        $fcm = $this->get('b3da_pusher.android.fcm');
        $message = new Message($msgTitle, $msgBody, $msgSound, $msgNotificationId);
//        $message->setData($msgData); // set data, if notifying from another method etc
        $fcm->notify($recipient, $message->composeAndroidFcmMessage(), $msgData);

        return new JsonResponse([
            'status' => 'ok',
            'details' => $fcm->getOutputAsObject(),
        ], 200);
    }

    /**
     * @param Request $request
     * @Route("/send/android-gcm/", name="b3da_pusher.api.send_android_gcm")
     * @return JsonResponse
     */
    public function sendAndroidGcmAction(Request $request) {
        $recipient = $request->request->get('recipient');
        $msgTitle = $request->request->get('title');
        $msgSound = $request->request->get('sound');
        $msgNotificationId = $request->request->get('notification_id');
        $msgBody = $request->request->get('message');
        if (!$recipient || !$msgTitle || !$msgBody) {
            return new JsonResponse([
                'status' => 'error',
                'details' => 'not enough data',
            ], 400);
        }

        $gcm = $this->get('b3da_pusher.android.gcm');
        $message = new Message($msgTitle, $msgBody, $msgSound, $msgNotificationId);
        $gcm->notify($recipient, $message->composeAndroidGcmMessage());

        return new JsonResponse([
            'status' => 'ok',
            'details' => $gcm->getOutputAsObject(),
        ], 200);
    }

    /**
     * @param Request $request
     * @Route("/send/ios/", name="b3da_pusher.api.send_ios")
     * @return JsonResponse
     */
    public function sendIosApnAction(Request $request) {
        $recipient = $request->request->get('recipient');
        $msgTitle = $request->request->get('title');
        $msgSound = $request->request->get('sound');
        $msgBody = $request->request->get('message');
        if (!$recipient || !$msgTitle || !$msgBody) {
            return new JsonResponse([
                'status' => 'error',
                'details' => 'not enough data',
            ], 400);
        }

        $apn = $this->get('b3da_pusher.ios.apn');
        $message = new Message($msgTitle, $msgBody, $msgSound);
        $apn->notify($recipient, $message->composeIosMessage());

        return new JsonResponse([
            'status' => 'ok',
            'details' => $apn->getOutputAsObject(),
        ], 200);
    }
}
