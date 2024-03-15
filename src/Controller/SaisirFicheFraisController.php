<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\FicheFrais;
use App\Entity\FraisForfait;
use App\Entity\LigneFraisForfait;
use App\Entity\LigneFraisHorsForfait;
use App\Form\MonthSelectorFormType;
use App\Form\SaisirFraisForfaitType;
use App\Form\SaisirFraisHorsForfaitType;
use App\Repository\EtatRepository;
use App\Repository\FicheFraisRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SaisirFicheFraisController extends AbstractController
{
    #[Route('/saisirFicheFrais', name: 'app_saisir_fiche_frais')]
    public function saisirFiche(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $today = date("Ym");
        $ficheFrais = $entityManager->getRepository(FicheFrais::class)->findOneBy(['user'=>$this->getUser(), 'mois'=>$today]);
        // verification si la fiche de frais du mois existe
        if ($ficheFrais == null){
            $ficheFrais = new FicheFrais();
            $ficheFrais->setUser($this->getUser());
            $ficheFrais->setMois($today);
            $ficheFrais->setDateModif(new \DateTime());
            $etatCreation = $entityManager->getRepository(Etat::class)->find(2);
            $ficheFrais->setEtat($etatCreation);

            // création ligne frais forfaits étapes
            $etape = $entityManager->getRepository(FraisForfait::class)->find(1);
            $lignesFraisForfait = new LigneFraisForfait();
            $lignesFraisForfait->setFraisforfait($etape);
            $lignesFraisForfait->setFichefrais($ficheFrais);
            $lignesFraisForfait->setQuantite(0);
            $ficheFrais->addLigneFraisForfait($lignesFraisForfait);

            // création ligne frais forfaits km
            $km = $entityManager->getRepository(FraisForfait::class)->find(2);
            $lignesFraisForfait = new LigneFraisForfait();
            $lignesFraisForfait->setFraisforfait($km);
            $lignesFraisForfait->setFichefrais($ficheFrais);
            $lignesFraisForfait->setQuantite(0);
            $ficheFrais->addLigneFraisForfait($lignesFraisForfait);

            // création ligne frais forfaits nuit
            $nuit = $entityManager->getRepository(FraisForfait::class)->find(3);
            $lignesFraisForfait = new LigneFraisForfait();
            $lignesFraisForfait->setFraisforfait($nuit);
            $lignesFraisForfait->setFichefrais($ficheFrais);
            $lignesFraisForfait->setQuantite(0);
            $ficheFrais->addLigneFraisForfait($lignesFraisForfait);

            // création ligne frais forfaits repas
            $repas = $entityManager->getRepository(FraisForfait::class)->find(4);
            $lignesFraisForfait = new LigneFraisForfait();
            $lignesFraisForfait->setFraisforfait($repas);
            $lignesFraisForfait->setFichefrais($ficheFrais);
            $lignesFraisForfait->setQuantite(0);
            $ficheFrais->addLigneFraisForfait($lignesFraisForfait);

            $entityManager->persist($ficheFrais);
            $entityManager->persist($etape);
            $entityManager->persist($km);
            $entityManager->persist($nuit);
            $entityManager->persist($repas);
            $entityManager->flush();
        }

        $formFraisForfait = $this->createForm(SaisirFraisForfaitType::class);
        $formFraisForfait->handleRequest($request);

        if ($formFraisForfait->isSubmitted() && $formFraisForfait->isValid()) {
            $toutesLesLignes = $ficheFrais->getLigneFraisForfait();
            foreach ($toutesLesLignes as $lignes){
                if ($lignes->getFraisForfait()->getId() == 1){
                    $lignes->setQuantite($formFraisForfait->get('ForfaitEtape')->getData());
                }
                elseif ($lignes->getFraisForfait()->getId() == 2){
                    $lignes->setQuantite($formFraisForfait->get('FraisKilometrique')->getData());
                }
                elseif ($lignes->getFraisForfait()->getId() == 3){
                    $lignes->setQuantite($formFraisForfait->get('NuiteeHotel')->getData());
                }
                else {
                    $lignes->setQuantite($formFraisForfait->get('RepasRestaurant')->getData());
                }

                $entityManager->persist($lignes);
                $entityManager->flush();

                return $this->redirectToRoute('app_etat_index', [], Response::HTTP_SEE_OTHER);
        }

        $ligneFraisHorsForfait = new LigneFraisHorsForfait();
        $formFraisHorsForfait = $this->createForm(SaisirFraisHorsForfaitType::class, $ligneFraisHorsForfait);
        $formFraisHorsForfait->handleRequest($request);

        if ($formFraisHorsForfait->isSubmitted() && $formFraisHorsForfait->isValid()) {
            $entityManager->persist($ligneFraisHorsForfait);
            $entityManager->flush();

            return $this->redirectToRoute('app_etat_index', [], Response::HTTP_SEE_OTHER);
        }


        return $this->render('saisir_fiche_frais/index.html.twig', [
        'controller_name' => 'SaisirFicheFraisController',
        'formFraisForfait' => $formFraisForfait->createView(),
        'formFraisHorsForfait' => $formFraisHorsForfait->createView(),
        ]);
    }
}
