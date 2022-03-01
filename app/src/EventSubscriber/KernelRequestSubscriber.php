<?php

namespace App\EventSubscriber;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * KernelRequestSubscriber
 */
class KernelRequestSubscriber implements EventSubscriberInterface
{
    const ALLOWED_ROUTES_WITHOUT_TOKEN = [
        [
            'method' => 'GET',
            'uri' => '/ping',
        ],
        [
            'method' => 'POST',
            'uri' => '/v1/game',       
        ],
    ];
    const TOKEN_FILTER_PARAMETER = 'token_filter';
    const HEADER_TOKEN_PARAMETER = 'access-token';

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * __construct
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            RequestEvent::class => 'onKernelRequest',
        ];
    }

    /**
     * onKernelRequest
     *
     * @param RequestEvent $event
     * @return void
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        if (!$this->isRouteRequiringValidation($request->getMethod(), $request->getRequestUri())) {
            $this->em->getFilters()->disable(self::TOKEN_FILTER_PARAMETER);

            return;
        }

        $token = $request->headers->get(seLf::HEADER_TOKEN_PARAMETER);
        if (empty($token)) {
            throw new AccessDeniedHttpException('This action needs a valid token!');
        }

        $filter = $this->em->getFilters()->enable(self::TOKEN_FILTER_PARAMETER);
        $filter->setParameter('token', $token);
    }

    /**
     * isRouteRequiringValidation
     *
     * @param string $method
     * @param string $uri
     * @return boolean
     */
    protected function isRouteRequiringValidation(string $method, string $uri): bool
    {
        foreach(self::ALLOWED_ROUTES_WITHOUT_TOKEN as $route) {
            if ($method === $route['method'] && $uri === $route['uri']) {
                return false;
            }
        }

        return true;
    }
}
