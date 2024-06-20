<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\RegistrationRepository;

class RegistrationController extends AbstractController
{
    #[Route('/registration', name: 'app_registration')]
    public function index(RegistrationRepository $registrationRepository): Response
    {

        $registrations = $registrationRepository->findAll();

        return $this->render('registration/index.html.twig', [
            'registrations' => $registrations,
        ]);
    }
}
