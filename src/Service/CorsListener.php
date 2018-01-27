<?php
namespace App\Service;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpFoundation\Response;

// Based on http://stackoverflow.com/a/21720357 & 
// https://www.hydrant.co.uk/journal/cors-pre-flight-requests-and-headers-symfony-httpkernel-component
class CorsListener implements EventSubscriberInterface
{
	public static function getSubscribedEvents()
	{
	    return array(
	        KernelEvents::REQUEST  => array('onKernelRequest', 9999),
	        KernelEvents::RESPONSE => array('onKernelResponse', 9999),
	    );
	}

	public function onKernelRequest(GetResponseEvent $event) {
		// Don't do anything if it's not the master request.
	    if (!$event->isMasterRequest()) {
	        return;
	    }

	    $request = $event->getRequest();
	    $method  = $request->getRealMethod();
	    if ($method == 'OPTIONS') {
	        $response = new Response();
	        $event->setResponse($response);
	    }
	}

	public function onKernelResponse(FilterResponseEvent $event) {
		// Don't do anything if it's not the master request.
	    if (!$event->isMasterRequest()) {
	        return;
	    }

		$responseHeaders = $event->getResponse()->headers;
	    $responseHeaders->set('Access-Control-Allow-Headers', 'origin, content-type, accept, credentials');
	    $responseHeaders->set('Access-Control-Allow-Origin', 'http://localhost:8080');
	    $responseHeaders->set('Access-Control-Allow-Credentials', 'true');
	    $responseHeaders->set('Access-Control-Allow-Methods', 'POST, GET, PUT, DELETE, PATCH, OPTIONS');
	}
}