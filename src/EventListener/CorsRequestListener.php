<?php

namespace Sokil\CorsBundle\EventListener;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @see https://www.html5rocks.com/static/images/cors_server_flowchart.png CORS Server Flowchart
 */
class CorsRequestListener implements EventSubscriberInterface
{
    /**
     * @var array
     */
    private $allowedOrigins;

    /**
     * @var bool
     */
    private $withCredentials;

    /**
     * @param array $allowedOrigins
     * @param bool $withCredentials
     * @param int|null $maxAge
     */
    public function __construct(
        array $allowedOrigins,
        $withCredentials,
        $maxAge = null
    ) {
        $this->allowedOrigins = $allowedOrigins;

        if (is_bool($withCredentials)) {
            $this->withCredentials = $withCredentials;
        } else {
            throw new \InvalidArgumentException('Paramarer "withCredentials" must be bool');
        }
        
        if (!empty($maxAge)) {
            if (!is_numeric($maxAge)) {
                throw new \InvalidArgumentException('Wrong Max-Age specified');
            }
            $this->maxAge = $maxAge;
        }
        
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        // skip sub requests
        if (!$event->isMasterRequest()) {
            return;
        }

        // get request instance
        $request = $event->getRequest();

        // CORS request MUST have "Origin" header
        if (!$request->headers->has('Origin')) {
            return;
        }

        // get origin
        $origin = $request->headers->get('Origin');

        // Handle preflight request
        if ($request->isMethod(Request::METHOD_OPTIONS)) {
            $response = new Response();
            if ($this->isOriginAcceptable($origin)) {
                $this->applyCorsHeaders($request, $response);
                $this->applyCorsPreflightHeaders($request, $response);
            }
            $event->setResponse($response);
            return;
        }

        // Handling of common requests is in self::onKernelResponse
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        // get request
        $request = $event->getRequest();

        // for preflight request response already defined in self::onKernelRequest
        if ($request->isMethod(Request::METHOD_OPTIONS)) {
            return;
        }

        // get origin
        $origin = $request->headers->get('Origin');

        // check if origin acceptable
        if (!$this->isOriginAcceptable($origin)) {
            return;
        }

        // get response
        $response = $event->getResponse();

        // apply CORS headers
        $this->applyCorsHeaders($request, $response);
    }

    /**
     * @param string $origin
     * @return bool
     */
    private function isOriginAcceptable($origin)
    {
        if (empty($this->allowedOrigins)) {
            return true;
        } else {
            return in_array($origin, $this->allowedOrigins);
        }
    }

    /**
     * @param Response $response
     * @param Response $response
     */
    private function applyCorsHeaders(Request $request, Response $response)
    {
        $origin = $request->headers->get('Origin');

        // allow origin
        $response->headers->set('Access-Control-Allow-Origin', $origin);

        // allow share credentials (send cookies, etc)
        if ($this->withCredentials) {
            $response->headers->set('Access-Control-Allow-Credentials', 'true');
        }
    }

    /**
     * Applicable only for preflight request
     *
     * @param Response $response
     * @param Response $response
     */
    private function applyCorsPreflightHeaders(Request $request, Response $response)
    {
        // method
        if ($request->headers->has('Access-Control-Request-Method')) {
            $response->headers->set(
                'Access-Control-Allow-Methods',
                $request->headers->has('Access-Control-Request-Method')
            );
        }

        // headers
        if ($request->headers->has('Access-Control-Request-Headers')) {
            $response->headers->set(
                'Access-Control-Allow-Headers',
                $request->headers->get('Access-Control-Request-Headers')
            );
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            # Priority 8 has firewall listener
            # priority 32 has router listener
            # This listener must be handled before them
            KernelEvents::REQUEST => array(array('onKernelRequest', 250)),
            KernelEvents::RESPONSE => array('onKernelResponse'),
        ];
    }
}
