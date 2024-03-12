<?php

namespace App\Controller;

use App\Entity\FicheFrais;
use App\Form\MonthSelectorFormType;
use App\Form\SaisirFraisForfaitType;
use App\Form\SaisirFraisHorsForfaitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SaisirFicheFraisController extends AbstractController
{
    #[Route('/saisirFicheFrais', name: 'app_saisir_fiche_frais')]
    public function saisirFiche(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $formHorsForfait = $this->createForm(SaisirFraisHorsForfaitType::class);
        $formForfait = $this->createForm(SaisirFraisForfaitType::class);

        $formHorsForfait->handleRequest($request);
        $formForfait->handleRequest($request);

        return $this->render('saisir_fiche_frais/index.html.twig', [
            'controller_name' => 'SaisirFicheFraisController',

        ]);
    }
}
