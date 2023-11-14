<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\FicheFrais;
use App\Entity\FraisForfait;
use App\Entity\LigneFraisForfait;
use App\Entity\LigneFraisHorsForfait;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class DataImportController extends AbstractController
{
    #[Route('/import/users', name: 'app_data_import_users')]
    public function users(ManagerRegistry $doctrine, UserPasswordHasherInterface $passwordHasher): Response
    {
        $usersjson = file_get_contents('./visiteur.json');
        $users = json_decode($usersjson);

        foreach ($users as $user) {
            $newuser = new User();
            $newuser->setNom($user->nom);
            $newuser->setPrenom($user->prenom);
            $newuser->setLogin($user->login);
            $newuser->setAdresse($user->adresse);
            $newuser->setCp($user->cp);
            $newuser->setVille($user->ville);
            $newuser->setOldId($user->id);
            $newuser->setDateEmbauche(new \DateTime($user->dateEmbauche));
            $newuser->setPassword($passwordHasher->hashPassword($newuser, $user->mdp));
            $doctrine->getManager()->persist($newuser);
            $doctrine->getManager()->flush();
        }

        return $this->render('data_import/index.html.twig', [
            'controller_name' => 'DataImportController',
        ]);
    }

    #[Route('/import/fichesfrais', name: 'app_data_import_fichesfrais')]
    public function fichesfrais(ManagerRegistry $doctrine): Response
    {
        $fichesfraisjson = file_get_contents('./fichefrais.json');
        $fichesfrais = json_decode($fichesfraisjson);

        foreach ($fichesfrais as $fichefrais) {
            $newfichefrais = new FicheFrais();
            $theUser = $doctrine->getRepository(User::class)->findOneBy(['oldId' => $fichefrais->idVisiteur]);
            if ($theUser != null) {
                $newfichefrais->setUser($theUser);
            } else {
                var_dump($theUser);
            }
            $newfichefrais->setDateModif(new \DateTime($fichefrais->dateModif));
            $newfichefrais->setMois($fichefrais->mois);
            $newfichefrais->setMontantValide($fichefrais->montantValide);
            $newfichefrais->setNbJustificatifs($fichefrais->nbJustificatifs);

            switch ($fichefrais->idEtat) {
                case 'CL' :
                    $newfichefrais->setEtat($doctrine->getRepository(Etat::class)->find(1));
                    break;
                case 'CR' :
                    $newfichefrais->setEtat($doctrine->getRepository(Etat::class)->find(2));
                    break;
                case 'RB' :
                    $newfichefrais->setEtat($doctrine->getRepository(Etat::class)->find(3));
                    break;
                case 'VA' :
                    $newfichefrais->setEtat($doctrine->getRepository(Etat::class)->find(4));
                    break;
                default :
                    $newfichefrais->setEtat($doctrine->getRepository(Etat::class)->find(1));
            }

            $doctrine->getManager()->persist($newfichefrais);
            $doctrine->getManager()->flush();
        }

        return $this->render('data_import/index.html.twig', [
            'controller_name' => 'DataImportController',
        ]);
    }

    #[Route('/import/lignesff', name: 'app_data_import_lignesff')]
    public function lignesff(ManagerRegistry $doctrine): Response
    {
        $lignesfraisforfaitjson = file_get_contents('./lignefraisforfait.json');
        $lignesfraisforfait = json_decode($lignesfraisforfaitjson);
        foreach ($lignesfraisforfait as $lignefraisforfait) {
            $newlignefraisforfait = new LigneFraisForfait(null, null, null);
            $theUser = $doctrine->getRepository(User::class)->findOneBy(['oldId' => $lignefraisforfait->idVisiteur]);
            $theFicheFrais = $doctrine->getRepository(FicheFrais::class)->findOneBy(['user' => $theUser, 'mois' => $lignefraisforfait->mois]);
            $newlignefraisforfait->setFichefrais($theFicheFrais);
            $newlignefraisforfait->setQuantite($lignefraisforfait->quantite);

            switch ($lignefraisforfait->idFraisForfait) {
                case 'ETP' :
                    $newlignefraisforfait->setFraisforfait($doctrine->getRepository(FraisForfait::class)->find(1));
                    break;
                case 'NUI' :
                    $newlignefraisforfait->setFraisforfait($doctrine->getRepository(FraisForfait::class)->find(2));
                    break;
                case 'REP' :
                    $newlignefraisforfait->setFraisforfait($doctrine->getRepository(FraisForfait::class)->find(3));
                    break;
                case 'KM' :
                    $newlignefraisforfait->setFraisforfait($doctrine->getRepository(FraisForfait::class)->find(4));
                    break;
                default :
                    $newlignefraisforfait->setFraisforfait($doctrine->getRepository(Etat::class)->find(1));
            }

            $doctrine->getManager()->persist($newlignefraisforfait);
            $doctrine->getManager()->flush();
        }

        return $this->render('data_import/index.html.twig', [
            'controller_name' => 'DataImportController',
        ]);
    }

    #[Route('/import/lignesfhf', name: 'app_data_import_lignesfhf')]
    public function lignesfhf(ManagerRegistry $doctrine): Response
    {
        $lignesfraishorsforfaitjson = file_get_contents('./lignefraishorsforfait.json');
        $lignesfraishorsforfait = json_decode($lignesfraishorsforfaitjson);

        foreach ($lignesfraishorsforfait as $lignefraishorsforfait)
        {
            $newlignefraishorsforfait = new LigneFraisHorsForfait(null, null, null, null);
            $theUser = $doctrine->getRepository(User::class)->findOneBy(['oldId' => $lignefraishorsforfait->idVisiteur]);
            $theFicheFrais = $doctrine->getRepository(FicheFrais::class)->findOneBy(['user' => $theUser, 'mois' => $lignefraishorsforfait->mois]);
            $newlignefraishorsforfait->setFichefrais($theFicheFrais);
            $newlignefraishorsforfait->setDate(new \DateTime($lignefraishorsforfait->date));
            $newlignefraishorsforfait->setLibelle($lignefraishorsforfait->libelle);
            $newlignefraishorsforfait->setMontant($lignefraishorsforfait->montant);

            $doctrine->getManager()->persist($newlignefraishorsforfait);
            $doctrine->getManager()->flush();
        }

        return $this->render('data_import/index.html.twig', [
            'controller_name' => 'DataImportController',
        ]);
    }
}
