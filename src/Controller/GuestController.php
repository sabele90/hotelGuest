<?php

namespace App\Controller;

use App\Entity\Guest;
use App\Entity\Registration;
use App\Form\GuestType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\RateLimiter\RateLimiterFactory;

class GuestController extends AbstractController
{
    private $security;
    private $mailer;
    private $apiLimiter;

    public function __construct(Security $security, MailerInterface $mailer, RateLimiterFactory $apiLimiter)
    {
        $this->security = $security;
        $this->mailer = $mailer;
        $this->apiLimiter = $apiLimiter;
    }

    #[Route('/guest/new', name: 'guest_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $limiter = $this->apiLimiter->create($request->getClientIp());
        $limit = $limiter->consume();
        $headers = [
            'X-RateLimit-Remaining' => $limit->getRemainingTokens(),
            'X-RateLimit-Retry-After' => $limit->getRetryAfter()->getTimestamp() - time(),
            'X-RateLimit-Limit' => $limit->getLimit(),
        ];

        if (false === $limit->isAccepted()) {
            $this->addFlash('danger', 'Rate limit exceeded. Please try again later.');

            $response = $this->redirectToRoute('app_registration');
            foreach ($headers as $key => $value) {
                $response->headers->set($key, $value);
            }
            return $response;
        }

        $guest = new Guest();
        $registration = new Registration();
        $form = $this->createForm(GuestType::class, $guest);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->security->getUser();
            $guest->setUser($user);

            $registration->setCheckInDate($form->get('checkInDate')->getData());
            $registration->setCheckOutDate($form->get('checkOutDate')->getData());
            $registration->addGuest($guest);

            $entityManager->persist($registration);
            $entityManager->persist($guest);
            $entityManager->flush();

            // Envío de correo electrónico
            $this->sendConfirmationEmail($user, $guest, $registration);

            $this->addFlash('success', 'Guest registered successfully.');
            return $this->redirectToRoute('app_registration');
        }

        $response = $this->render('guest/new.html.twig', [
            'form' => $form->createView(),
        ]);
        $response->headers->add($headers);

        return $response;
    }

    #[Route('/guest/{id}/edit', name: 'guest_edit')]
    public function edit(Request $request, Guest $guest, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(GuestType::class, $guest);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $guest->setUser($this->security->getUser());
            $entityManager->flush();

            $this->addFlash('success', 'Guest details updated successfully.');
            return $this->redirectToRoute('app_registration');
        }

        return $this->render('guest/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    private function sendConfirmationEmail($user, $guest, $registration)
    {
        $email = (new TemplatedEmail())
            ->from('no-reply@emerahotel.com')
            ->to($user->getEmail())
            ->subject('Reservation Confirmation')
            ->htmlTemplate('emails/reservation.html.twig')
            ->context([
                'user' => $user,
                'guest' => $guest,
                'registration' => $registration,
            ]);

        try {
            $this->mailer->send($email);
            $this->addFlash('success', 'Email sent successfully.');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Failed to send email: ' . $e->getMessage());
        }
    }
}
