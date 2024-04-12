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
    public function saisirFiche(Request $request, EntityManagerInterface $entityManager, FicheFraisRepository $ficheFraisRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $today = date("Ym");
        $ficheFrais = $ficheFraisRepository->findOneBy(['user'=>$this->getUser(), 'mois'=>$today]);
        // verification si la fiche de frais du mois existe
        if ($ficheFrais == null){
            $ficheFrais = new FicheFrais();
            $ficheFrais->setUser($this->getUser());
            $ficheFrais->setMois($today);
            $ficheFrais->setDateModif(new \DateTime());
            $ficheFrais->setNbJustificatifs(0);
            $ficheFrais->setMontantValide(0);
            $etatCreation = $entityManager->getRepository(Etat::class)->find(2);
            $ficheFrais->setEtat($etatCreation);

            // création ligne frais forfaits étape
            $etape = $entityManager->getRepository(FraisForfait::class)->find(1);
            $lignesFraisForfait = new LigneFraisForfait($ficheFrais, $etape, 0);
            $ficheFrais->addLigneFraisForfait($lignesFraisForfait);

            // création ligne frais forfaits km
            $km = $entityManager->getRepository(FraisForfait::class)->find(2);
            $lignesFraisForfait = new LigneFraisForfait($ficheFrais, $km, 0);
            $ficheFrais->addLigneFraisForfait($lignesFraisForfait);

            // création ligne frais forfaits nuit
            $nuit = $entityManager->getRepository(FraisForfait::class)->find(3);
            $lignesFraisForfait = new LigneFraisForfait($ficheFrais, $nuit, 0);
            $ficheFrais->addLigneFraisForfait($lignesFraisForfait);

            // création ligne frais forfaits repas
            $repas = $entityManager->getRepository(FraisForfait::class)->find(4);
            $lignesFraisForfait = new LigneFraisForfait($ficheFrais, $repas, 0);
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
        $toutesLesLignes = $ficheFrais->getLigneFraisForfaits();
        if (!$formFraisForfait->isSubmitted()){
            foreach ($toutesLesLignes as $ligne){
                if ($ligne->getFraisForfait()->getId() == 1){
                    $formFraisForfait->get('quantiteEtape')->setData($ligne->getQuantite());
                }
                elseif ($ligne->getFraisForfait()->getId() == 2){
                    $formFraisForfait->get('quantiteKm')->setData($ligne->getQuantite());
                }
                elseif ($ligne->getFraisForfait()->getId() == 3){
                    $formFraisForfait->get('quantiteNuit')->setData($ligne->getQuantite());
                }
                else {
                    $formFraisForfait->get('quantiteRepas')->setData($ligne->getQuantite());
                }
            }
        }

        if ($formFraisForfait->isSubmitted() && $formFraisForfait->isValid()) {
            foreach ($toutesLesLignes as $ligne){
                if ($ligne->getFraisForfait()->getId() == 1){
                    $ligne->setQuantite($formFraisForfait->get('quantiteEtape')->getData());
                }
                elseif ($ligne->getFraisForfait()->getId() == 2){
                    $ligne->setQuantite($formFraisForfait->get('quantiteKm')->getData());
                }
                elseif ($ligne->getFraisForfait()->getId() == 3){
                    $ligne->setQuantite($formFraisForfait->get('quantiteNuit')->getData());
                }
                else {
                    $ligne->setQuantite($formFraisForfait->get('quantiteRepas')->getData());
                }
            }
            $entityManager->persist($ficheFrais);
            $entityManager->flush();

            return $this->redirectToRoute('app_saisir_fiche_frais', [], Response::HTTP_SEE_OTHER);
        }

        $ligneFraisHorsForfait = new LigneFraisHorsForfait($ficheFrais, '', new \DateTime(), 0);
        $formFraisHorsForfait = $this->createForm(SaisirFraisHorsForfaitType::class, $ligneFraisHorsForfait);
        $formFraisHorsForfait->handleRequest($request);

        if ($formFraisHorsForfait->isSubmitted() && $formFraisHorsForfait->isValid()) {
            $entityManager->persist($ligneFraisHorsForfait);
            $entityManager->flush();

            return $this->redirectToRoute('app_saisir_fiche_frais', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('saisir_fiche_frais/index.html.twig', [
            'ficheFrais' => $ficheFrais,
            'formFraisForfait' => $formFraisForfait,
            'formFraisHorsForfait' => $formFraisHorsForfait,
        ]);
    }

}
