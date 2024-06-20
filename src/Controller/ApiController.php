<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\RateLimiter\RateLimiterFactory;

class ApiController extends AbstractController
{
    /**
     * @Route("/api/endpoint", name="api_endpoint", methods={"POST"})
     */
    public function index(Request $request, RateLimiterFactory $apiLimiter): Response
    {
        $limiter = $apiLimiter->create($request->getClientIp());
        $limit = $limiter->consume();
        $headers = [
            'X-RateLimit-Remaining' => $limit->getRemainingTokens(),
            'X-RateLimit-Retry-After' => $limit->getRetryAfter()->getTimestamp() - time(),
            'X-RateLimit-Limit' => $limit->getLimit(),
        ];

        if (false === $limit->isAccepted()) {
            return new Response('Rate limit exceeded', Response::HTTP_TOO_MANY_REQUESTS, $headers);
        }

        $response = new Response('Endpoint executed successfully');
        $response->headers->add($headers);

        return $response;
    }
}
