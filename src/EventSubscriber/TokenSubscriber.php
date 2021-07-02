<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Firebase\JWT\JWT;

use Symfony\Component\HttpKernel\Event\RequestEvent;
use App\Services\SendDataController;

class TokenSubscriber implements EventSubscriberInterface
{

    private $send;
 
    public function __construct(SendDataController $send)
    {
        $this->send = $send;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $request= $event->getRequest();
        $uri = $request->getRequestUri();
        if($request->getMethod() !== 'GET' && $uri !== 'api/login' && $uri !== 'api/user/new'){
            $authorization = $request->headers->get('authorization');
            if(!$authorization){
                $event->setResponse($this->send->sendData("", "", 401, 'Require authentification'));
                return;
            }
            $key = "API";
            $token = str_replace("Bearer ", "", $authorization);
            $jwt = JWT::decode($token, $key, array('HS256'));
            if(!$jwt){
                $event->setResponse($this->send->sendData("", "", 403, 'Unauthorized'));
                return;
            }

        }
    }

    public static function getSubscribedEvents()
    {
        return [
            'kernel.request' => 'onKernelRequest',
        ];
    }
}
