<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

readonly class ApiBasicAuthSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private string $apiBasicUser,
        private string $apiBasicPassword,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $path = $request->getPathInfo();

        if (!str_starts_with($path, '/api/')) {
            return;
        }

        $user = $request->getUser();
        $password = $request->getPassword();

        if ($user !== $this->apiBasicUser || $password !== $this->apiBasicPassword) {
            $response = new JsonResponse(
                ['message' => 'Unauthorized. Invalid or missing Basic Auth credentials.'],
                Response::HTTP_UNAUTHORIZED
            );

            $response->headers->set('WWW-Authenticate', 'Basic realm="API"');

            $event->setResponse($response);
        }
    }
}
