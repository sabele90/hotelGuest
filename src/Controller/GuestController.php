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

class GuestController extends AbstractController
{
    private $security;
    private $mailer;

    public function __construct(Security $security, MailerInterface $mailer)
    {
        $this->security = $security;
        $this->mailer = $mailer;
    }

    #[Route('/guest/new', name: 'guest_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $guest = new Guest();
        $registration = new Registration();
        $form = $this->createForm(GuestType::class, $guest);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //obtenemos el id del usuario que ha inciado sesión
            $user = $this->security->getUser();
            $guest->setUser($user);


            $registration->setCheckInDate($form->get('checkInDate')->getData());
            $registration->setCheckOutDate($form->get('checkOutDate')->getData());
            $registration->addGuest($guest);

            $entityManager->persist($registration);
            $entityManager->persist($guest);
            $entityManager->flush();
            //Usuario registrado ecitosamente:
            $this->sendConfirmationEmail($user, $guest, $registration);
            $this->addFlash('success', 'Guest registered successfully.');

            return $this->redirectToRoute('app_registration');
        }


        return $this->render('guest/new.html.twig', [
            'form' => $form->createView(),
        ]);
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
            ->from('sabele@hotmail.es')
            ->to('sabele@hotmail.es')
            ->subject('Reservation Confirmation')
            ->htmlTemplate('emails/reservation.html.twig')
            ->context([
                'user' => $user,
                'guest' => $guest,
                'registration' => $registration,
            ]);

        try {
            $this->mailer->send($email);
            $this->addFlash('success', 'Email enviado correctamente.');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Error al enviar el email: ' . $e->getMessage());
        }
    }
}
