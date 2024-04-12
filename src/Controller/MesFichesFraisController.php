<?php

namespace App\Controller;

use App\Entity\FicheFrais;
use App\Form\MonthSelectorFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MesFichesFraisController extends AbstractController
{
    #[Route('/mes/fiches/frais', name: 'app_mes_fiches_frais')]
    public function selectedMonth(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $mesFiches = $entityManager->getRepository(FicheFrais::class)->findBy(['user'=>$this->getUser()]);
        $form = $this->createForm(MonthSelectorFormType::class, $mesFiches);
        $selectedFiche = null;

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            /** @var FicheFrais $selectedFiche */
            $selectedFiche = $form->get('selectedMonth')->getData();
        }


        return $this->render('mes_fiches_frais/index.html.twig', [
            'form' => $form->createView(),
            'selectedFiche' => $selectedFiche
        ]);
    }
}
