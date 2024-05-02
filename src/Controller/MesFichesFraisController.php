<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\FicheFrais;
use App\Form\MonthSelectorFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MesFichesFraisController extends AbstractController
{
    #[Route('/mes/fiches/frais', name: 'app_mes_fiches_frais')]
    public function selectedMonth(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($this->isGranted('ROLE_COMPTABLE')) {
            $mesFiches = $entityManager->getRepository(FicheFrais::class)->findAll();
        } else {
            $mesFiches = $entityManager->getRepository(FicheFrais::class)->findBy(['user'=>$this->getUser()]);
        }

        $form = $this->createForm(MonthSelectorFormType::class, $mesFiches);
        $selectedFiche = null;

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            /** @var FicheFrais $selectedFiche */
            $selectedFiche = $form->get('selectedMonth')->getData();
        }

        $etatForms = [];
        foreach ($mesFiches as $fiche) {
            $etats = $entityManager->getRepository(Etat::class)->findAll();
            $choices = [];
            foreach ($etats as $etat) {
                $choices[$etat->getLibelle()] = $etat;
            }

            $etatForm = $this->createFormBuilder($fiche)
                ->add('etat', ChoiceType::class, [
                    'choices' => $choices,
                    'label' => 'Modifier l\'état',
                ])
                ->getForm();

            $etatForm->handleRequest($request);

            if ($etatForm->isSubmitted() && $etatForm->isValid()) {
                $entityManager->flush();

                return $this->redirectToRoute('app_mes_fiches_frais');
            }

            $etatForms[$fiche->getId()] = $etatForm->createView();
        }


        return $this->render('mes_fiches_frais/index.html.twig', [
            'form' => $form->createView(),
            'selectedFiche' => $selectedFiche,
            'etatForms' => $etatForms
        ]);
    }
}
