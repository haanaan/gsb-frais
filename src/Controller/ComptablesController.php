<?php

namespace App\Controller;

use App\Entity\FicheFrais;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ComptablesController extends AbstractController
{
    #[Route('/comptables', name: 'app_comptables')]
    public function index(EntityManagerInterface $entityManager, Request $request): Response
    {
        $users = $entityManager->getRepository(User::class)->findAll();
        // Créez un tableau pour le champ de sélection
        $choices = [];
        foreach ($users as $user) {
            $choices[$user->getNom()] = $user->getId();
        }

        // Créez le formulaire de sélection d'utilisateur
        $form = $this->createFormBuilder()
            ->add('user', ChoiceType::class, [
                'choices' => $choices,
                'label' => 'Choisir un utilisateur',
            ])
            ->getForm();

        $form->handleRequest($request);

        $ficheFrais = [];
        if ($form->isSubmitted() && $form->isValid()) {
            $selectedUserId = $form->get('user')->getData();
            $selectedUser = $entityManager->getRepository(User::class)->find($selectedUserId);

            $ficheFrais = $entityManager->getRepository(FicheFrais::class)->findBy(['user' => $selectedUser]);
        }

        return $this->render('comptables/index.html.twig', [
            'users' => $users,
            'form' => $form->createView(),
            'ficheFrais' => $ficheFrais,
        ]);
    }


}
