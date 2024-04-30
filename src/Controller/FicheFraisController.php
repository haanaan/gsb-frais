<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\FicheFrais;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FicheFraisController extends AbstractController
{
    #[Route('/fichefrais/{id}', name: 'app_fiche_frais')]
    public function index(FicheFrais $ficheFrais, Request $request, EntityManagerInterface $entityManager): Response
    {
        $mesFiches = $entityManager->getRepository(FicheFrais::class)->findBy(['user'=>$this->getUser()]);

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
                    'label' => 'Etat ',
                ])
                ->getForm();

            $etatForm->handleRequest($request);

            if ($etatForm->isSubmitted() && $etatForm->isValid()) {
                $entityManager->flush();

                return $this->redirectToRoute('app_fiche_frais', ['id' => $fiche->getId()]);
            }

            $etatForms[$fiche->getId()] = $etatForm->createView();
        }

        return $this->render('mes_fiches_frais/index.html.twig', [
            'selectedFiche' => $ficheFrais,
            'etatForms' => $etatForms
        ]);
    }
}
