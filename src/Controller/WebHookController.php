<?php
/**
 * Created by PhpStorm.
 * User: oskargunther
 * Date: 26.08.2018
 * Time: 14:55
 */

namespace OG\SendGridBundle\Controller;


use OG\SendGridBundle\Event\WebHookEvent;
use OG\SendGridBundle\Model\WebHook;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class WebHookController extends Controller
{
    public function dispatchAction(Request $request): Response
    {
        $events = json_decode($request->getContent(), true);

        foreach ($events as $event) {
            $this->get('event_dispatcher')->dispatch('sendgrid.webhook.' . $event['event'], new WebHookEvent(new WebHook($event)));
        }

        return new Response('', Response::HTTP_ACCEPTED);
    }
}