<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpClient\HttpClient;

class WebhookController extends AbstractController
{
    public function sendWebhook(Request $request): Response
    {

        $data = json_decode($request->getContent(), true);

        $data['sent_by'] = 'Hello emera user';

        // Hacer una solicitud POST al webhook de destino
        $httpClient = HttpClient::create();
        $response = $httpClient->request('POST', 'https://webhook.site/e517553c-6fcb-4ade-9524-208e2d6c4068', [
            'json' => $data,
        ]);

        // Manejar la respuesta del webhook (opcional)
        $statusCode = $response->getStatusCode();

        // Puedes devolver una respuesta según la respuesta del webhook
        return new Response('Webhook enviado correctamente', $statusCode);
    }
}
