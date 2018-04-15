<?php
// src/Controller/GameController.php
namespace App\Controller;
use App\Entity\Utilisateur;
use App\Entity\Objectif;
use App\Entity\Partie;
use App\Entity\Carte;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;




class GameController extends Controller


{
    /**
     * @Route("/jouer", name="nouvelle_partie")
     */
    public function nouvellePartie() {
        $joueurs = $this->getDoctrine()->getRepository(Utilisateur::class)->findAll();
        return $this->render('game/nouvelle.html.twig', ['joueurs' => $joueurs]);
    }
    /**
     * @Route("/creer", name="creer_partie")
     */
    public function creerPartie(Request $request) {

        $idAdversaire = $request->request->get('adversaire');
        $joueur = $this->getUser();
        $joueur_id = $this->getUser()->getId();
        $adversaire = $this->getDoctrine()->getRepository(Utilisateur::class)->find($idAdversaire);

        //récupérer les cartes depuis la base de données et mélanger leur id
        $cartes = $this->getDoctrine()->getRepository(Carte::class)->findAll();

        /** @var Carte $carte */
        $tCartes = array();
        foreach ($cartes as $carte) {
            $tCartes[] = $carte->getId();
        }
        shuffle($tCartes);

        //retrait de la première carte
        $carte_ecartee = array_pop($tCartes);

        //Distribution des cartes aux joueurs,
        $tMainJ1 = array();
        for($i=0; $i<7; $i++) {
            $tMainJ1[] = array_pop($tCartes);
        }
        $tMainJ2 = array();
        for($i=0; $i<6; $i++) {
            $tMainJ2[] = array_pop($tCartes);
        }

        //La création de la pioche ,sauvegarde des dernières cartes dans la pioche
        $tPioche = $tCartes;

        // actions au départ
        $secret = array('etat'=>0, 'carte'=>0);
        $dissimulation = array('etat'=>0, 'carte'=>array());
        $cadeau = array('etat'=>0, 'carte'=>array());
        $concurrence = array('etat'=>0, 'carte'=>array('p1'=>array(), 'p2'=>array()));
        $carte_j = array();

        // tableau de toutes les actions
        $tAction = array('dissimulation'=>$dissimulation, 'secret'=>$secret, 'cadeau'=>$cadeau, 'concurrence'=>$concurrence);

        // attribution des objectif par id à j1 et j2
        $tObjectifs_attribution = array('p_1'=>array('j1'=> 0 ,'j2'=> 0, 'jeton'=>0), 'p_2'=>array('j1'=> 0 ,'j2'=> 0, 'jeton'=>0), 'p_3'=>array('j1'=> 0 ,'j2'=> 0, 'jeton'=>0), 'p_4'=>array('j1'=> 0 ,'j2'=> 0, 'jeton'=>0), 'p_5'=>array('j1'=> 0 ,'j2'=> 0, 'jeton'=>0), 'p_6'=>array('j1'=> 0 ,'j2'=> 0, 'jeton'=>0), 'p_7'=>array('j1'=> 0 ,'j2'=> 0, 'jeton'=>0));

        //Score
        $scorej1 = array('point_o' => 0, 'point_o_nb' => 0);
        $scorej2 = array('point_o' => 0, 'point_o_nb' => 0);
        $stat_manche = array('nb'=> 1, 'etat'=>0);

        //créer un objet de type Partie
        $partie = new Partie();
        $partie->setJ1($joueur);
        $partie->setJ2($adversaire);
        $partie->setCarteEcarte(json_encode($carte_ecartee));
        $partie->setMainJ1(json_encode($tMainJ1));
        $partie->setMainJ2(json_encode($tMainJ2));
        $partie->setPioche(json_encode($tPioche));
        $partie->setTour($joueur_id);
        $partie->setManche(json_encode($stat_manche));
        $partie->setActionJ1(json_encode($tAction));
        $partie->setActionJ2(json_encode($tAction));
        $partie->setObjectifAttribution(json_encode($tObjectifs_attribution));
        $partie->setScoreJ1(json_encode($scorej1));
        $partie->setScoreJ2(json_encode($scorej2));
        $partie->setTerrainJ1(json_encode($carte_j));
        $partie->setTerrainJ2(json_encode($carte_j));
        $partie->setVainqueur('R');

        //Sauvegarde mon objet Partie dans la base de données et redirection vers l'affichage
        $em = $this->getDoctrine()->getManager();
        $em->persist($partie);
        $em->flush();
        return $this->redirectToRoute('afficher_partie', ['id' => $partie->getId(), 'partie'=>$partie]);
    }
    /**
     * @Route("/afficher/{id}", name="afficher_partie")
     */
    public function afficherPartie(Partie $partie, Request $request) {


        //SET PARTIE

        $cartes = $this->getDoctrine()->getRepository("App:Carte")->findAll();
        $objectifs = $this->getDoctrine()->getRepository("App:Objectif")->findAll();
        $manche = $partie->getManche();





        /** @var Carte $carte */
        $tCartes = array();
        foreach($cartes as $carte) {
            $tCartes[$carte->getId()] = $carte;
        }

        /** @var Objectif $objectif */
        $tObjectifs = array();
        foreach($objectifs as $objectif) {
            $tObjectifs[$objectif->getId()] = $objectif;
        }

        //Choisir current joueur
        $currentuser = $this->getUser();

        if ($currentuser == $partie->getJ1()){
            $main = $partie->getMainJ1();
            $action= $partie->getActionJ1();
            $action_ad = $partie->getActionJ2();
            $tour = $partie->getTour();
            $myTerrain = $partie->getTerrainJ1();
            $hisTerrain = $partie->getTerrainJ2();
            $myScore = $partie->getScoreJ1();
            $hisScore = $partie->getScoreJ2();
            $myId = $partie->getJ1()->getId();
            $hisId = $partie->getJ2()->getId();
            $myName = $partie->getJ1()->getUsername();

        }elseif($currentuser == $partie->getJ2()){
            $main = $partie->getMainJ2();
            $action= $partie->getActionJ2();
            $action_ad = $partie->getActionJ1();
            $tour = $partie->getTour();
            $myTerrain = $partie->getTerrainJ2();
            $hisTerrain = $partie->getTerrainJ1();
            $myScore = $partie->getScoreJ2();
            $hisScore = $partie->getScoreJ1();
            $myId = $partie->getJ2()->getId();
            $hisId = $partie->getJ1()->getId();
            $myName = $partie->getJ2()->getUsername();
        }

        //Parcourir la main de current user
        $cartes_main = array();

        foreach ($main as $id) {
            if($id !=0){
                $carte = $this->getDoctrine()->getRepository(Carte::class)->find($id);
                $cartes_main[] = ['nom'=>$carte->getNom(), 'id'=>$id, 'valeur'=>$carte->getPoints(), 'img'=>$carte->getImg()];
            }
        }










        //////////////////////////////////////////////////////////////////
        // RECUPERATION CARTE DISSIMULE
        //////////////////////////////////////////////////////////////////

        $carte_diss_1 = $request->request->get('carte_diss_1');
        $carte_diss_2 = $request->request->get('carte_diss_2');
        $tcarte_diss = array($carte_diss_1, $carte_diss_2);
        if($carte_diss_1 != null) {
            $Main = array();
            if ($currentuser == $partie->getJ1()) {
                $action = $partie->getActionJ1();
                $action->dissimulation->carte = $tcarte_diss;
                $action->dissimulation->etat = 1;
                $partie->setActionJ1(json_encode($action));
                $partie->setTour($partie->getJ2()->getId());


                $pioche = $partie->getPioche();
                $main_adv = $partie->getMainJ2();

               if (!empty($pioche) ){
                    array_push($main_adv, $pioche[0]);
                    unset($pioche[0]);
                    $tpioche =  array_values($pioche);
                    $partie->setMainJ2(json_encode($main_adv));
                    $partie->setPioche(json_encode($tpioche));
                }


                $main = $partie->getMainJ1();
                foreach ($main as $key => $value) {
                    if ($value == $carte_diss_1) {
                        unset($main[$key]);
                    }
                }
                foreach ($main as $key => $value) {
                    if ($value == $carte_diss_2) {
                        unset($main[$key]);
                    }
                    else {
                        $Main[] = $value;
                    }
                }
                $partie->setMainJ1(json_encode($Main));
                $em = $this->getDoctrine()->getManager();
                $em->persist($partie);
                $em->flush();
                //Parcourir la main de current user
                $cartes_main = array();

                foreach ($main as $id) {
                    if($id !=0){
                        $carte = $this->getDoctrine()->getRepository(Carte::class)->find($id);
                        $cartes_main[] = ['nom'=>$carte->getNom(), 'id'=>$id, 'valeur'=>$carte->getPoints(), 'img'=>$carte->getImg()];
                    }
                }

            } else {
                $action = $partie->getActionJ2();
                $action->dissimulation->carte = $tcarte_diss;
                $action->dissimulation->etat = 1;
                $partie->setActionJ2(json_encode($action));
                $partie->setTour($partie->getJ1()->getId());

                $pioche = $partie->getPioche();
                $main_adv = $partie->getMainJ1();

                if (!empty($pioche) ) {
                    array_push($main_adv, $pioche[0]);
                    unset($pioche[0]);
                    $tpioche = array_values($pioche);
                    $partie->setMainJ1(json_encode($main_adv));
                    $partie->setPioche(json_encode($tpioche));
                }
                $main = $partie->getMainJ2();
                foreach ($main as $key => $value) {
                    if ($value == $carte_diss_1) {
                        unset($main[$key]);
                    }
                }
                foreach ($main as $key => $value) {
                    if ($value == $carte_diss_2) {
                        unset($main[$key]);
                    }
                    else {
                        $Main[] = $value;
                    }
                }
                $partie->setMainJ2(json_encode($Main));
                $em = $this->getDoctrine()->getManager();
                $em->persist($partie);
                $em->flush();
                //Parcourir la main de current user
                $cartes_main = array();

                foreach ($main as $id) {
                    if($id !=0){
                        $carte = $this->getDoctrine()->getRepository(Carte::class)->find($id);
                        $cartes_main[] = ['nom'=>$carte->getNom(), 'id'=>$id, 'valeur'=>$carte->getPoints(), 'img'=>$carte->getImg()];
                    }
                }
            }

        }


        //////////////////////////////////////////////////////////////////
        // RECUPERATION CARTE SECRET
        //////////////////////////////////////////////////////////////////

        $carte_secret = $request->request->get('carte_sec_1');

        if($carte_secret != null) {
            $Main = array();
            if ($currentuser == $partie->getJ1()) {
                $action = $partie->getActionJ1();
                $action->secret->carte = $carte_secret;
                $action->secret->etat = 1;
                $partie->setActionJ1(json_encode($action));
                $partie->setTour($partie->getJ2()->getId());

                $pioche = $partie->getPioche();
                $main_adv = $partie->getMainJ2();

               if (!empty($pioche) ) {
                    array_push($main_adv, $pioche[0]);
                    unset($pioche[0]);
                    $tpioche = array_values($pioche);
                    $partie->setMainJ2(json_encode($main_adv));
                    $partie->setPioche(json_encode($tpioche));
                }
                $main = $partie->getMainJ1();
                foreach ($main as $key => $value) {
                    if ($value == $carte_secret) {
                        unset($main[$key]);
                    }else {
                        $Main[] = $value;
                    }
                }
                $partie->setMainJ1(json_encode($Main));
                $em = $this->getDoctrine()->getManager();
                $em->persist($partie);
                $em->flush();
                //Parcourir la main de current user
                $cartes_main = array();

                foreach ($main as $id) {
                    if($id !=0){
                        $carte = $this->getDoctrine()->getRepository(Carte::class)->find($id);
                        $cartes_main[] = ['nom'=>$carte->getNom(), 'id'=>$id, 'valeur'=>$carte->getPoints(), 'img'=>$carte->getImg()];
                    }
                }

            } else {
                $action = $partie->getActionJ2();
                $action->secret->carte = $carte_secret;
                $action->secret->etat = 1;
                $partie->setActionJ2(json_encode($action));
                $partie->setTour($partie->getJ1()->getId());

                $pioche = $partie->getPioche();
                $main_adv = $partie->getMainJ1();

                if (!empty($pioche) ) {
                    array_push($main_adv, $pioche[0]);
                    unset($pioche[0]);
                    $tpioche = array_values($pioche);
                    $partie->setMainJ1(json_encode($main_adv));
                    $partie->setPioche(json_encode($tpioche));
                }
                $main = $partie->getMainJ2();
                foreach ($main as $key => $value) {
                    if ($value == $carte_secret) {
                        unset($main[$key]);
                    }else {
                        $Main[] = $value;
                    }
                }
                $partie->setMainJ2(json_encode($Main));
                $em = $this->getDoctrine()->getManager();
                $em->persist($partie);
                $em->flush();
                //Parcourir la main de current user
                $cartes_main = array();

                foreach ($main as $id) {
                    if($id !=0){
                        $carte = $this->getDoctrine()->getRepository(Carte::class)->find($id);
                        $cartes_main[] = ['nom'=>$carte->getNom(), 'id'=>$id, 'valeur'=>$carte->getPoints(), 'img'=>$carte->getImg()];
                    }
                }
            }

        }


        //////////////////////////////////////////////////////////////////
        // RECUPERATION CARTE CADEAU
        //////////////////////////////////////////////////////////////////


        $carte_cad_1 = $request->request->get('carte_cad_1');
        $carte_cad_2 = $request->request->get('carte_cad_2');
        $carte_cad_3 = $request->request->get('carte_cad_3');
        $tcarte_cad = array($carte_cad_1, $carte_cad_2, $carte_cad_3);
        if($carte_cad_1 != null) {
            $Main = array();
            if ($currentuser == $partie->getJ1()) {
                $action = $partie->getActionJ1();
                $action->cadeau->carte = $tcarte_cad;
                $action->cadeau->etat = 1;
                $partie->setActionJ1(json_encode($action));
                $partie->setTour($partie->getJ2()->getId());

                $pioche = $partie->getPioche();
                $main_adv = $partie->getMainJ2();

               if (!empty($pioche) ) {
                    array_push($main_adv, $pioche[0]);
                    unset($pioche[0]);
                    $tpioche = array_values($pioche);
                    $partie->setMainJ2(json_encode($main_adv));
                    $partie->setPioche(json_encode($tpioche));
                }
                $main = $partie->getMainJ1();
                foreach ($main as $key => $value) {
                    if ($value == $carte_cad_1) {
                        unset($main[$key]);
                    }
                }
                foreach ($main as $key => $value) {
                    if ($value == $carte_cad_2) {
                        unset($main[$key]);
                    }
                }
                foreach ($main as $key => $value) {
                    if ($value == $carte_cad_3) {
                        unset($main[$key]);
                    }
                    else {
                        $Main[] = $value;
                    }
                }
                $partie->setMainJ1(json_encode($Main));
                $em = $this->getDoctrine()->getManager();
                $em->persist($partie);
                $em->flush();
                //Parcourir la main de current user
                $cartes_main = array();

                foreach ($main as $id) {
                    if($id !=0){
                        $carte = $this->getDoctrine()->getRepository(Carte::class)->find($id);
                        $cartes_main[] = ['nom'=>$carte->getNom(), 'id'=>$id, 'valeur'=>$carte->getPoints(), 'img'=>$carte->getImg()];
                    }
                }

            } else {
                $action = $partie->getActionJ2();
                $action->cadeau->carte = $tcarte_cad;
                $action->cadeau->etat = 1;
                $partie->setActionJ2(json_encode($action));
                $partie->setTour($partie->getJ1()->getId());

                $pioche = $partie->getPioche();
                $main_adv = $partie->getMainJ1();

               if (!empty($pioche) ) {
                    array_push($main_adv, $pioche[0]);
                    unset($pioche[0]);
                    $tpioche = array_values($pioche);
                    $partie->setMainJ1(json_encode($main_adv));
                    $partie->setPioche(json_encode($tpioche));
                }
                $main = $partie->getMainJ2();
                foreach ($main as $key => $value) {
                    if ($value == $carte_cad_1) {
                        unset($main[$key]);
                    }
                }
                foreach ($main as $key => $value) {
                    if ($value == $carte_cad_2) {
                        unset($main[$key]);
                    }
                }
                foreach ($main as $key => $value) {
                    if ($value == $carte_cad_3) {
                        unset($main[$key]);
                    }
                    else {
                        $Main[] = $value;
                    }
                }
                $partie->setMainJ2(json_encode($Main));
                $em = $this->getDoctrine()->getManager();
                $em->persist($partie);
                $em->flush();
                //Parcourir la main de current user
                $cartes_main = array();

                foreach ($main as $id) {
                    if($id !=0){
                        $carte = $this->getDoctrine()->getRepository(Carte::class)->find($id);
                        $cartes_main[] = ['nom'=>$carte->getNom(), 'id'=>$id, 'valeur'=>$carte->getPoints(), 'img'=>$carte->getImg()];
                    }
                }
            }

        }

        //////////////////////////////////////////////////////////////////
        // RECUPERATION CARTE CONCURRENCE
        //////////////////////////////////////////////////////////////////


        $carte_conc_1 = $request->request->get('carte_conc_1');
        $carte_conc_2 = $request->request->get('carte_conc_2');
        $carte_conc_3 = $request->request->get('carte_conc_3');
        $carte_conc_4 = $request->request->get('carte_conc_4');
        $tcarte_conc_p1 = array($carte_conc_1, $carte_conc_2);
        $tcarte_conc_p2 = array($carte_conc_3, $carte_conc_4);

        if($carte_conc_1 != null) {
            $Main = array();
            if ($currentuser == $partie->getJ1()) {
                $action = $partie->getActionJ1();
                $action->concurrence->carte->p1 = $tcarte_conc_p1;
                $action->concurrence->carte->p2 = $tcarte_conc_p2;
                $action->concurrence->etat = 1;
                $partie->setActionJ1(json_encode($action));
                $partie->setTour($partie->getJ2()->getId());

                $pioche = $partie->getPioche();
                $main_adv = $partie->getMainJ2();

               if (!empty($pioche) ) {
                    array_push($main_adv, $pioche[0]);
                    unset($pioche[0]);
                    $tpioche = array_values($pioche);
                    $partie->setMainJ2(json_encode($main_adv));
                    $partie->setPioche(json_encode($tpioche));
                }
                $main = $partie->getMainJ1();
                foreach ($main as $key => $value) {
                    if ($value == $carte_conc_1) {
                        unset($main[$key]);
                    }
                }
                foreach ($main as $key => $value) {
                    if ($value == $carte_conc_2) {
                        unset($main[$key]);
                    }
                }
                foreach ($main as $key => $value) {
                    if ($value == $carte_conc_3) {
                        unset($main[$key]);
                    }
                }
                foreach ($main as $key => $value) {
                    if ($value == $carte_conc_4) {
                        unset($main[$key]);
                    }
                    else {
                        $Main[] = $value;
                    }
                }
                $partie->setMainJ1(json_encode($Main));
                $em = $this->getDoctrine()->getManager();
                $em->persist($partie);
                $em->flush();
                //Parcourir la main de current user
                $cartes_main = array();

                foreach ($main as $id) {
                    if($id !=0){
                        $carte = $this->getDoctrine()->getRepository(Carte::class)->find($id);
                        $cartes_main[] = ['nom'=>$carte->getNom(), 'id'=>$id, 'valeur'=>$carte->getPoints(), 'img'=>$carte->getImg()];
                    }
                }

            } else {
                $action = $partie->getActionJ2();
                $action->concurrence->carte->p1 = $tcarte_conc_p1;
                $action->concurrence->carte->p2 = $tcarte_conc_p2;
                $action->concurrence->etat = 1;
                $partie->setActionJ2(json_encode($action));
                $partie->setTour($partie->getJ1()->getId());



                $pioche = $partie->getPioche();
                $main_adv = $partie->getMainJ1();
                   
                if (!empty($pioche) ) {
                    array_push($main_adv, $pioche[0]);
                    unset($pioche[0]);
                    $tpioche = array_values($pioche);
                    $partie->setMainJ1(json_encode($main_adv));
                    $partie->setPioche(json_encode($tpioche));
                }

                $main = $partie->getMainJ2();
                foreach ($main as $key => $value) {
                    if ($value == $carte_conc_1) {
                        unset($main[$key]);
                    }
                }
                foreach ($main as $key => $value) {
                    if ($value == $carte_conc_2) {
                        unset($main[$key]);
                    }
                }
                foreach ($main as $key => $value) {
                    if ($value == $carte_conc_3) {
                        unset($main[$key]);
                    }
                }
                foreach ($main as $key => $value) {
                    if ($value == $carte_conc_4) {
                        unset($main[$key]);
                    }
                    else {
                        $Main[] = $value;
                    }
                }
                $partie->setMainJ2(json_encode($Main));
                $em = $this->getDoctrine()->getManager();
                $em->persist($partie);
                $em->flush();
                //Parcourir la main de current user
                $cartes_main = array();

                foreach ($main as $id) {
                    if($id !=0){
                        $carte = $this->getDoctrine()->getRepository(Carte::class)->find($id);
                        $cartes_main[] = ['nom'=>$carte->getNom(), 'id'=>$id, 'valeur'=>$carte->getPoints(), 'img'=>$carte->getImg()];
                    }
                }
            }

        }


        //////////////////////////////////////////////////////////////////
        // RECUPERATION CARTE CADEAU CHOISI
        //////////////////////////////////////////////////////////////////


        $carte_cadeau_recup = $request->request->get('recup_cadeau');




        if($carte_cadeau_recup != null) {

            if ($currentuser == $partie->getJ1()) {

/*
                if (!empty($pioche) ) {
                    array_push($main_adv, $pioche[0]);
                    unset($pioche[0]);
                    $tpioche = array_values($pioche);
                    $partie->setMainJ1(json_encode($main_adv));
                    $partie->setPioche(json_encode($tpioche));
                }*/




                $terrainj1 = $partie->getTerrainJ1();
                array_push($terrainj1, $carte_cadeau_recup);
                $partie->setTerrainJ1(json_encode(array_values($terrainj1)));

                $terrainj2 = $partie->getTerrainJ2();

                unset($action_ad->cadeau->carte[array_search($carte_cadeau_recup, $action_ad->cadeau->carte)]);
                array_push($terrainj2, $action_ad->cadeau->carte);
                $partie->setTerrainJ2(json_encode(array_values($terrainj2)));



                $action_ad->cadeau->carte = array();
                $action_ad->cadeau->etat = 2;
                $partie->setActionJ2(json_encode($action_ad));

                $em = $this->getDoctrine()->getManager();
                $em->persist($partie);
                $em->flush();

            } else {



                $terrainj2 = $partie->getTerrainJ2();
                array_push($terrainj2, $carte_cadeau_recup);
                $Tterrainj2 = array_values($terrainj2);
                $partie->setTerrainJ2(json_encode($Tterrainj2));

                $terrainj1 = $partie->getTerrainJ1();
                unset($action_ad->cadeau->carte[array_search($carte_cadeau_recup, $action_ad->cadeau->carte)]);
                array_push($terrainj1, $action_ad->cadeau->carte);
                $Tterrainj1 = array_values($terrainj1);
                $partie->setTerrainJ1(json_encode($Tterrainj1));


                $action_ad->cadeau->carte = array();
                $action_ad->cadeau->etat = 2;
                $partie->setActionJ1(json_encode($action_ad));



                $em = $this->getDoctrine()->getManager();
                $em->persist($partie);
                $em->flush();

            }

        }

        //////////////////////////////////////////////////////////////////
        // RECUPERATION PAIRE CHOISI
        //////////////////////////////////////////////////////////////////


        $carte_p1_1 = $request->request->get('recup_conc_p1_1');
        $carte_p1_2 = $request->request->get('recup_conc_p1_2');
        $carte_p2_1 = $request->request->get('recup_conc_p2_1');
        $carte_p2_2 = $request->request->get('recup_conc_p2_2');



        if($carte_p1_1 != null) {

            if ($currentuser == $partie->getJ1()) {


                $terrainj1 = $partie->getTerrainJ1();
                array_push($terrainj1, $carte_p1_1, $carte_p1_2);
                $partie->setTerrainJ1(json_encode($terrainj1));

                $terrainj2 = $partie->getTerrainJ2();
                array_push($terrainj2, $action_ad->concurrence->carte->p2);
                $partie->setTerrainJ2(json_encode($terrainj2));

                $action_ad->concurrence->carte->p1 = array();
                $action_ad->concurrence->carte->p2 = array();
                $action_ad->concurrence->etat = 2;
                $partie->setActionJ2(json_encode($action_ad));



                $em = $this->getDoctrine()->getManager();
                $em->persist($partie);
                $em->flush();

            } else {


                $terrainj2 = $partie->getTerrainJ2();
                array_push($terrainj2, $carte_p1_1, $carte_p1_2);
                $partie->setTerrainJ2(json_encode($terrainj2));

                $terrainj1 = $partie->getTerrainJ1();
                array_push($terrainj1, $action_ad->concurrence->carte->p2);
                $partie->setTerrainJ1(json_encode($terrainj1));


                $action_ad->concurrence->carte->p1 = array();
                $action_ad->concurrence->carte->p2 = array();
                $action_ad->concurrence->etat = 2;
                $partie->setActionJ1(json_encode($action_ad));



                $em = $this->getDoctrine()->getManager();
                $em->persist($partie);
                $em->flush();

            }

        }elseif ($carte_p2_1 != null){

            if ($currentuser == $partie->getJ1()) {


                $terrainj1 = $partie->getTerrainJ1();
                array_push($terrainj1, $carte_p2_1, $carte_p2_2);
                $partie->setTerrainJ1(json_encode($terrainj1));

                //

                $terrainj2 = $partie->getTerrainJ2();
                array_push($terrainj2, $action_ad->concurrence->carte->p1);
                $partie->setTerrainJ2(json_encode($terrainj2));

                $action_ad->concurrence->carte->p1 = array();
                $action_ad->concurrence->carte->p2 = array();
                $action_ad->concurrence->etat = 2;
                $partie->setActionJ2(json_encode($action_ad));



                $em = $this->getDoctrine()->getManager();
                $em->persist($partie);
                $em->flush();

            } else {




                $terrainj2 = $partie->getTerrainJ2();
                array_push($terrainj2, $carte_p2_1, $carte_p2_2);
                $partie->setTerrainJ2(json_encode($terrainj2));

                $terrainj1 = $partie->getTerrainJ1();
                array_push($terrainj1, $action_ad->concurrence->carte->p1);
                $partie->setTerrainJ1(json_encode($terrainj1));

                $action_ad->concurrence->carte->p1 = array();
                $action_ad->concurrence->carte->p2 = array();
                $action_ad->concurrence->etat = 2;
                $partie->setActionJ1(json_encode($action_ad));



                $em = $this->getDoctrine()->getManager();
                $em->persist($partie);
                $em->flush();

            }


        }

        $getattr = $partie->getObjectifAttribution();

        if(empty($pioche) && $action_ad->dissimulation->etat == 1 && $action_ad->secret->etat == 1 && $action_ad->cadeau->etat == 2 && $action_ad->concurrence->etat == 2 && $action->dissimulation->etat == 1 && $action->secret->etat == 1 && $action->cadeau->etat == 2 && $action->concurrence->etat == 2 && $getattr->p_1->j1 == 0 && $getattr->p_2->j1 == 0 && $getattr->p_3->j1 == 0 && $getattr->p_4->j1 == 0 && $getattr->p_5->j1 == 0 && $partie->getManche()->etat == 0 ){


            if ($currentuser == $partie->getJ1()){

                $myTerrain = $partie->getTerrainJ1();
                $hisTerrain = $partie->getTerrainJ2();
                $myScore = $partie->getScoreJ1();
                $hisScore = $partie->getScoreJ2();



                array_push($myTerrain, $partie->getActionJ1()->secret->carte);
                array_push($hisTerrain, $partie->getActionJ2()->secret->carte);
                $partie->setTerrainJ1(json_encode($myTerrain));
                $partie->setTerrainJ2(json_encode($hisTerrain));

                $myTerrain = $partie->getTerrainJ1();
                $hisTerrain = $partie->getTerrainJ2();

                foreach ($myTerrain as $key => $val){


                    if (!is_iterable($val)){
                        
                        if ( $tCartes[$val]->getObjectifs()->getId() == 1){
                            $getattr->p_1->j1 +=  1;
                        }
                        if ( $tCartes[$val]->getObjectifs()->getId() == 2){
                            $getattr->p_2->j1 +=  1;
                        }
                        if ( $tCartes[$val]->getObjectifs()->getId() == 3){
                            $getattr->p_3->j1 +=  1;
                        }
                        if ( $tCartes[$val]->getObjectifs()->getId() == 4){
                            $getattr->p_4->j1 +=  1;
                        }
                        if ( $tCartes[$val]->getObjectifs()->getId() == 5){
                            $getattr->p_5->j1 +=  1;
                        }
                        if ( $tCartes[$val]->getObjectifs()->getId() == 6){
                            $getattr->p_6->j1 +=  1;
                        }
                        if ( $tCartes[$val]->getObjectifs()->getId() == 7){
                            $getattr->p_7->j1 +=  1;
                        }

                    }else{

                        foreach ($val as $key2 => $val2){
                            
                            if ( $tCartes[$val2]->getObjectifs()->getId() == 1){
                                $getattr->p_1->j1 += 1;
                            };
                            if ( $tCartes[$val2]->getObjectifs()->getId() == 2){
                                $getattr->p_2->j1 += 1;
                            };
                            if ( $tCartes[$val2]->getObjectifs()->getId() == 3){
                                $getattr->p_3->j1 += 1;
                            };
                            if ( $tCartes[$val2]->getObjectifs()->getId() == 4){
                                $getattr->p_4->j1 += 1;
                            };
                            if ( $tCartes[$val2]->getObjectifs()->getId() == 5){
                                $getattr->p_5->j1 += 1;
                            };
                            if ( $tCartes[$val2]->getObjectifs()->getId() == 6){
                                $getattr->p_6->j1 += 1;
                            };
                            if ( $tCartes[$val2]->getObjectifs()->getId() == 7){
                                $getattr->p_7->j1 += 1;
                            };
                        }
                    }

                };

                foreach ($hisTerrain as $key => $val){


                    if (!is_iterable($val)){
                        
                        if ( $tCartes[$val]->getObjectifs()->getId() == 1){
                            $getattr->p_1->j2 +=  1;
                        }
                        if ( $tCartes[$val]->getObjectifs()->getId() == 2){
                            $getattr->p_2->j2 +=  1;
                        }
                        if ( $tCartes[$val]->getObjectifs()->getId() == 3){
                            $getattr->p_3->j2 +=  1;
                        }
                        if ( $tCartes[$val]->getObjectifs()->getId() == 4){
                            $getattr->p_4->j2 +=  1;
                        }
                        if ( $tCartes[$val]->getObjectifs()->getId() == 5){
                            $getattr->p_5->j2 +=  1;
                        }
                        if ( $tCartes[$val]->getObjectifs()->getId() == 6){
                            $getattr->p_6->j2 +=  1;
                        }
                        if ( $tCartes[$val]->getObjectifs()->getId() == 7){
                            $getattr->p_7->j2 +=  1;
                        }

                    }else{

                        foreach ($val as $key2 => $val2){
                            
                            if ( $tCartes[$val2]->getObjectifs()->getId() == 1){
                                $getattr->p_1->j2 += 1;
                            };
                            if ( $tCartes[$val2]->getObjectifs()->getId() == 2){
                                $getattr->p_2->j2 += 1;
                            };
                            if ( $tCartes[$val2]->getObjectifs()->getId() == 3){
                                $getattr->p_3->j2 += 1;
                            };
                            if ( $tCartes[$val2]->getObjectifs()->getId() == 4){
                                $getattr->p_4->j2 += 1;
                            };
                            if ( $tCartes[$val2]->getObjectifs()->getId() == 5){
                                $getattr->p_5->j2 += 1;
                            };
                            if ( $tCartes[$val2]->getObjectifs()->getId() == 6){
                                $getattr->p_6->j2 += 1;
                            };
                            if ( $tCartes[$val2]->getObjectifs()->getId() == 7){
                                $getattr->p_7->j2 += 1;
                            };
                        }
                    }

                };




                if ($getattr->p_1->j1 > $getattr->p_1->j2 || $getattr->p_1->j1 == $getattr->p_1->j2 &&  $getattr->p_1->jeton == $partie->getJ1()->getId() ){
                    $myScore->point_o += 2;
                    $myScore->point_o_nb += 1;
                    $getattr->p_1->jeton = $partie->getJ1()->getId();
                }elseif ($getattr->p_1->j1 < $getattr->p_1->j2 || $getattr->p_1->j1 == $getattr->p_1->j2 &&  $getattr->p_1->jeton == $partie->getJ2()->getId()){
                    $hisScore->point_o += 2;
                    $hisScore->point_o_nb += 1;
                    $getattr->p_1->jeton = $partie->getJ2()->getId();
                }

                if ($getattr->p_2->j1 > $getattr->p_2->j2 || $getattr->p_2->j1 == $getattr->p_2->j2 &&  $getattr->p_2->jeton == $partie->getJ1()->getId() ){
                    $myScore->point_o += 2;
                    $myScore->point_o_nb += 1;
                    $getattr->p_2->jeton = $partie->getJ1()->getId();
                }elseif ($getattr->p_2->j1 < $getattr->p_2->j2 || $getattr->p_2->j1 == $getattr->p_2->j2 &&  $getattr->p_2->jeton == $partie->getJ2()->getId()){
                    $hisScore->point_o += 2;
                    $hisScore->point_o_nb += 1;
                    $getattr->p_2->jeton = $partie->getJ2()->getId();
                }

                if ($getattr->p_3->j1 > $getattr->p_3->j2 || $getattr->p_3->j1 == $getattr->p_3->j2 &&  $getattr->p_3->jeton == $partie->getJ1()->getId()){
                    $myScore->point_o += 2;
                    $myScore->point_o_nb += 1;
                    $getattr->p_3->jeton = $partie->getJ1()->getId();
                }elseif ($getattr->p_3->j1 < $getattr->p_3->j2 || $getattr->p_3->j1 == $getattr->p_3->j2 &&  $getattr->p_3->jeton == $partie->getJ2()->getId()){
                    $hisScore->point_o += 2;
                    $hisScore->point_o_nb += 1;
                    $getattr->p_3->jeton = $partie->getJ2()->getId();
                }

                if ($getattr->p_4->j1 > $getattr->p_4->j2 || $getattr->p_4->j1 == $getattr->p_4->j2 &&  $getattr->p_4->jeton == $partie->getJ1()->getId()){
                    $myScore->point_o += 3;
                    $myScore->point_o_nb += 1;
                    $getattr->p_4->jeton = $partie->getJ1()->getId();
                }elseif ($getattr->p_4->j1 < $getattr->p_4->j2 || $getattr->p_4->j1 == $getattr->p_4->j2 &&  $getattr->p_4->jeton == $partie->getJ2()->getId()){
                    $hisScore->point_o += 3;
                    $hisScore->point_o_nb += 1;
                    $getattr->p_4->jeton = $partie->getJ2()->getId();
                }

                if ($getattr->p_5->j1 > $getattr->p_5->j2 || $getattr->p_5->j1 == $getattr->p_5->j2 &&  $getattr->p_5->jeton == $partie->getJ1()->getId()){
                    $myScore->point_o += 3;
                    $myScore->point_o_nb += 1;
                    $getattr->p_5->jeton = $partie->getJ1()->getId();
                }elseif ($getattr->p_5->j1 < $getattr->p_5->j2 || $getattr->p_5->j1 == $getattr->p_5->j2 &&  $getattr->p_5->jeton == $partie->getJ2()->getId()){
                    $hisScore->point_o += 3;
                    $hisScore->point_o_nb += 1;
                    $getattr->p_5->jeton = $partie->getJ2()->getId();
                }

                if ($getattr->p_6->j1 > $getattr->p_6->j2 || $getattr->p_6->j1 == $getattr->p_6->j2 &&  $getattr->p_6->jeton == $partie->getJ1()->getId() ){
                    $myScore->point_o += 4;
                    $myScore->point_o_nb += 1;
                    $getattr->p_6->jeton = $partie->getJ1()->getId();
                }elseif ($getattr->p_6->j1 < $getattr->p_6->j2 || $getattr->p_6->j1 == $getattr->p_6->j2 &&  $getattr->p_6->jeton == $partie->getJ2()->getId()){
                    $hisScore->point_o += 4;
                    $hisScore->point_o_nb += 1;
                    $getattr->p_6->jeton = $partie->getJ2()->getId();
                }

                if ($getattr->p_7->j1 > $getattr->p_7->j2 || $getattr->p_7->j1 == $getattr->p_7->j2 &&  $getattr->p_7->jeton == $partie->getJ1()->getId()){
                    $myScore->point_o += 5;
                    $myScore->point_o_nb += 1;
                    $getattr->p_7->jeton = $partie->getJ1()->getId();
                }elseif ($getattr->p_7->j1 < $getattr->p_7->j2 || $getattr->p_7->j1 == $getattr->p_7->j2 &&  $getattr->p_7->jeton == $partie->getJ2()->getId()){
                    $hisScore->point_o += 5;
                    $hisScore->point_o_nb += 1;
                    $getattr->p_7->jeton = $partie->getJ2()->getId();
                }



                $partie->setScoreJ1(json_encode($myScore));
                $partie->setScoreJ2(json_encode($hisScore));
                $partie->setObjectifAttribution(json_encode($getattr));
                $p_manche = $partie->getManche();
                $p_manche->etat = 1;
                $partie->setManche(json_encode($p_manche));
                $em = $this->getDoctrine()->getManager();
                $em->persist($partie);
                $em->flush();

            } else {

                $myTerrain = $partie->getTerrainJ2();
                $hisTerrain = $partie->getTerrainJ1();
                $myScore = $partie->getScoreJ2();
                $hisScore = $partie->getScoreJ1();

                array_push($myTerrain, $partie->getActionJ2()->secret->carte);
                array_push($hisTerrain, $partie->getActionJ1()->secret->carte);
                $partie->setTerrainJ2(json_encode($myTerrain));
                $partie->setTerrainJ1(json_encode($hisTerrain));

                $myTerrain = $partie->getTerrainJ2();
                $hisTerrain = $partie->getTerrainJ1();

                foreach ($myTerrain as $key => $val){
                    if (!is_iterable($val)){
                        
                        if ( $tCartes[$val]->getObjectifs()->getId() == 1){
                            $getattr->p_1->j2 +=  1;
                        }
                        if ( $tCartes[$val]->getObjectifs()->getId() == 2){
                            $getattr->p_2->j2 +=  1;
                        }
                        if ( $tCartes[$val]->getObjectifs()->getId() == 3){
                            $getattr->p_3->j2 +=  1;
                        }
                        if ( $tCartes[$val]->getObjectifs()->getId() == 4){
                            $getattr->p_4->j2 +=  1;
                        }
                        if ( $tCartes[$val]->getObjectifs()->getId() == 5){
                            $getattr->p_5->j2 +=  1;
                        }
                        if ( $tCartes[$val]->getObjectifs()->getId() == 6){
                            $getattr->p_6->j2 +=  1;
                        }
                        if ( $tCartes[$val]->getObjectifs()->getId() == 7){
                            $getattr->p_7->j2 +=  1;
                        }

                    }else{

                        foreach ($val as $key2 => $val2){
                            
                            if ( $tCartes[$val2]->getObjectifs()->getId() == 1){
                                $getattr->p_1->j2 += 1;
                            };
                            if ( $tCartes[$val2]->getObjectifs()->getId() == 2){
                                $getattr->p_2->j2 += 1;
                            };
                            if ( $tCartes[$val2]->getObjectifs()->getId() == 3){
                                $getattr->p_3->j2 += 1;
                            };
                            if ( $tCartes[$val2]->getObjectifs()->getId() == 4){
                                $getattr->p_4->j2 += 1;
                            };
                            if ( $tCartes[$val2]->getObjectifs()->getId() == 5){
                                $getattr->p_5->j2 += 1;
                            };
                            if ( $tCartes[$val2]->getObjectifs()->getId() == 6){
                                $getattr->p_6->j2 += 1;
                            };
                            if ( $tCartes[$val2]->getObjectifs()->getId() == 7){
                                $getattr->p_7->j2 += 1;
                            };
                        }
                    }

                };

                foreach ($hisTerrain as $key => $val){
                    if (!is_iterable($val)){
                        
                        if ( $tCartes[$val]->getObjectifs()->getId() == 1){
                            $getattr->p_1->j1 +=  1;
                        }
                        if ( $tCartes[$val]->getObjectifs()->getId() == 2){
                            $getattr->p_2->j1 +=  1;
                        }
                        if ( $tCartes[$val]->getObjectifs()->getId() == 3){
                            $getattr->p_3->j1 +=  1;
                        }
                        if ( $tCartes[$val]->getObjectifs()->getId() == 4){
                            $getattr->p_4->j1 +=  1;
                        }
                        if ( $tCartes[$val]->getObjectifs()->getId() == 5){
                            $getattr->p_5->j1 +=  1;
                        }
                        if ( $tCartes[$val]->getObjectifs()->getId() == 6){
                            $getattr->p_6->j1 +=  1;
                        }
                        if ( $tCartes[$val]->getObjectifs()->getId() == 7){
                            $getattr->p_7->j1 +=  1;
                        }

                    }else{

                        foreach ($val as $key2 => $val2){
                            
                            if ( $tCartes[$val2]->getObjectifs()->getId() == 1){
                                $getattr->p_1->j1 += 1;
                            };
                            if ( $tCartes[$val2]->getObjectifs()->getId() == 2){
                                $getattr->p_2->j1 += 1;
                            };
                            if ( $tCartes[$val2]->getObjectifs()->getId() == 3){
                                $getattr->p_3->j1 += 1;
                            };
                            if ( $tCartes[$val2]->getObjectifs()->getId() == 4){
                                $getattr->p_4->j1 += 1;
                            };
                            if ( $tCartes[$val2]->getObjectifs()->getId() == 5){
                                $getattr->p_5->j1 += 1;
                            };
                            if ( $tCartes[$val2]->getObjectifs()->getId() == 6){
                                $getattr->p_6->j1 += 1;
                            };
                            if ( $tCartes[$val2]->getObjectifs()->getId() == 7){
                                $getattr->p_7->j1 += 1;
                            };
                        }
                    }

                };

                if ($getattr->p_1->j1 > $getattr->p_1->j2 || $getattr->p_1->j1 == $getattr->p_1->j2 &&  $getattr->p_1->jeton == $partie->getJ1()->getId() ){
                    $myScore->point_o += 2;
                    $myScore->point_o_nb += 1;
                    $getattr->p_1->jeton = $partie->getJ1()->getId();
                }elseif ($getattr->p_1->j1 < $getattr->p_1->j2 || $getattr->p_1->j1 == $getattr->p_1->j2 &&  $getattr->p_1->jeton == $partie->getJ2()->getId()){
                    $hisScore->point_o += 2;
                    $hisScore->point_o_nb += 1;
                    $getattr->p_1->jeton = $partie->getJ2()->getId();
                }

                if ($getattr->p_2->j1 > $getattr->p_2->j2 || $getattr->p_2->j1 == $getattr->p_2->j2 &&  $getattr->p_2->jeton == $partie->getJ1()->getId() ){
                    $myScore->point_o += 2;
                    $myScore->point_o_nb += 1;
                    $getattr->p_2->jeton = $partie->getJ1()->getId();
                }elseif ($getattr->p_2->j1 < $getattr->p_2->j2 || $getattr->p_2->j1 == $getattr->p_2->j2 &&  $getattr->p_2->jeton == $partie->getJ2()->getId()){
                    $hisScore->point_o += 2;
                    $hisScore->point_o_nb += 1;
                    $getattr->p_2->jeton = $partie->getJ2()->getId();
                }

                if ($getattr->p_3->j1 > $getattr->p_3->j2 || $getattr->p_3->j1 == $getattr->p_3->j2 &&  $getattr->p_3->jeton == $partie->getJ1()->getId()){
                    $myScore->point_o += 2;
                    $myScore->point_o_nb += 1;
                    $getattr->p_3->jeton = $partie->getJ1()->getId();
                }elseif ($getattr->p_3->j1 < $getattr->p_3->j2 || $getattr->p_3->j1 == $getattr->p_3->j2 &&  $getattr->p_3->jeton == $partie->getJ2()->getId()){
                    $hisScore->point_o += 2;
                    $hisScore->point_o_nb += 1;
                    $getattr->p_3->jeton = $partie->getJ2()->getId();
                }

                if ($getattr->p_4->j1 > $getattr->p_4->j2 || $getattr->p_4->j1 == $getattr->p_4->j2 &&  $getattr->p_4->jeton == $partie->getJ1()->getId()){
                    $myScore->point_o += 3;
                    $myScore->point_o_nb += 1;
                    $getattr->p_4->jeton = $partie->getJ1()->getId();
                }elseif ($getattr->p_4->j1 < $getattr->p_4->j2 || $getattr->p_4->j1 == $getattr->p_4->j2 &&  $getattr->p_4->jeton == $partie->getJ2()->getId()){
                    $hisScore->point_o += 3;
                    $hisScore->point_o_nb += 1;
                    $getattr->p_4->jeton = $partie->getJ2()->getId();
                }

                if ($getattr->p_5->j1 > $getattr->p_5->j2 || $getattr->p_5->j1 == $getattr->p_5->j2 &&  $getattr->p_5->jeton == $partie->getJ1()->getId()){
                    $myScore->point_o += 3;
                    $myScore->point_o_nb += 1;
                    $getattr->p_5->jeton = $partie->getJ1()->getId();
                }elseif ($getattr->p_5->j1 < $getattr->p_5->j2 || $getattr->p_5->j1 == $getattr->p_5->j2 &&  $getattr->p_5->jeton == $partie->getJ2()->getId()){
                    $hisScore->point_o += 3;
                    $hisScore->point_o_nb += 1;
                    $getattr->p_5->jeton = $partie->getJ2()->getId();
                }

                if ($getattr->p_6->j1 > $getattr->p_6->j2 || $getattr->p_6->j1 == $getattr->p_6->j2 &&  $getattr->p_6->jeton == $partie->getJ1()->getId() ){
                    $myScore->point_o += 4;
                    $myScore->point_o_nb += 1;
                    $getattr->p_6->jeton = $partie->getJ1()->getId();
                }elseif ($getattr->p_6->j1 < $getattr->p_6->j2 || $getattr->p_6->j1 == $getattr->p_6->j2 &&  $getattr->p_6->jeton == $partie->getJ2()->getId()){
                    $hisScore->point_o += 4;
                    $hisScore->point_o_nb += 1;
                    $getattr->p_6->jeton = $partie->getJ2()->getId();
                }

                if ($getattr->p_7->j1 > $getattr->p_7->j2 || $getattr->p_7->j1 == $getattr->p_7->j2 &&  $getattr->p_7->jeton == $partie->getJ1()->getId()){
                    $myScore->point_o += 5;
                    $myScore->point_o_nb += 1;
                    $getattr->p_7->jeton = $partie->getJ1()->getId();
                }elseif ($getattr->p_7->j1 < $getattr->p_7->j2 || $getattr->p_7->j1 == $getattr->p_7->j2 &&  $getattr->p_7->jeton == $partie->getJ2()->getId()){
                    $hisScore->point_o += 5;
                    $hisScore->point_o_nb += 1;
                    $getattr->p_7->jeton = $partie->getJ2()->getId();
                }



                $partie->setScoreJ1(json_encode($hisScore));
                $partie->setScoreJ2(json_encode($myScore));
                $partie->setObjectifAttribution(json_encode($getattr));
                $p_manche = $partie->getManche();
                $p_manche->etat = 1;
                $partie->setManche(json_encode($p_manche));
                $em = $this->getDoctrine()->getManager();
                $em->persist($partie);
                $em->flush();


            }


        };


        //////////////////////////////////////////////////////////
        /// DETERMINER LES VAINQUEURS
        //////////////////////////////////////////////////////////
        $manche = $partie->getManche();
        $siGg = $partie->getVainqueur();
           

            if ($manche->etat == 1 && $siGg == 'R'){
                if ($partie->getScoreJ1()->point_o >= 11 && $partie->getScoreJ1()->point_o > $partie->getScoreJ2()->point_o){

                    $j1 = $partie->getJ1();
                    $j2 = $partie->getJ2();
                    $victoire = $j1->getWinParties();
                    $victoire = $victoire + 1;
                    $nbPartiej1 = $j1->getNbPartie();
                    $nbPartiej1 = $nbPartiej1 + 1;
                    $nbPartiej2 = $j2->getNbPartie();
                    $nbPartiej2 = $nbPartiej2 + 1;

                    $j1->setWinParties($victoire);
                    $j1->setNbPartie($nbPartiej1);
                    $j2->setNbPartie($nbPartiej2);

                    $partie->setVainqueur($j1->getUsername());
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($j1);
                    $em->persist($j2);
                    $em->flush();

                }elseif ($partie->getScoreJ1()->point_o < $partie->getScoreJ2()->point_o && $partie->getScoreJ2()->point_o >= 11){


                    $j1 = $partie->getJ1();
                    $j2 = $partie->getJ2();
                    $victoire = $j2->getWinParties();
                    $victoire = $victoire + 1;
                    $j2->setWinParties($victoire);

                    $nbPartiej1 = $j1->getNbPartie();
                    $nbPartiej1 = $nbPartiej1 + 1;
                    $nbPartiej2 = $j2->getNbPartie();
                    $nbPartiej2 = $nbPartiej2 + 1;


                    $partie->setVainqueur($j2->getUsername());
                    $j1->setNbPartie($nbPartiej1);
                    $j2->setNbPartie($nbPartiej2);

                    $em = $this->getDoctrine()->getManager();
                    $em->persist($j1);
                    $em->persist($j2);
                    $em->flush();

                }elseif ($partie->getScoreJ1()->point_o_nb >= 4){

                    $j1 = $partie->getJ1();
                    $j2 = $partie->getJ2();
                    $victoire = $j1->getWinParties();
                    $victoire = $victoire + 1;
                    $nbPartiej1 = $j1->getNbPartie();
                    $nbPartiej1 = $nbPartiej1 + 1;
                    $nbPartiej2 = $j2->getNbPartie();
                    $nbPartiej2 = $nbPartiej2 + 1;

                    $j1->setWinParties($victoire);
                    $j1->setNbPartie($nbPartiej1);
                    $j2->setNbPartie($nbPartiej2);

                    $partie->setVainqueur($j1->getUsername());
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($j1);
                    $em->persist($j2);
                    $em->flush();

                }elseif($partie->getScoreJ2()->point_o_nb >= 4){


                    $j1 = $partie->getJ1();
                    $j2 = $partie->getJ1();
                    $victoire = $j2->getWinParties();
                    $victoire = $victoire + 1;
                    $nbPartiej1 = $j1->getNbPartie();
                    $nbPartiej1 = $nbPartiej1 + 1;
                    $nbPartiej2 = $j2->getNbPartie();
                    $nbPartiej2 = $nbPartiej2 + 1;

                    $j2->setWinParties($victoire);
                    $j1->setNbPartie($nbPartiej1);
                    $j2->setNbPartie($nbPartiej2);

                    $partie->setVainqueur($j2->getUsername());
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($j1);
                    $em->persist($j2);
                    $em->flush();

                }else{

                    $this->addFlash('n_manche',
                        'Tenez vous prêt pour une nouvelle manche !');

                    //MESSAGE N PARTIE

                    //////////////////////////////////////////////////////////
                    /// CREER UNE PARTIE
                    //////////////////////////////////////////////////////////
                    ///

                    //récupérer les cartes depuis la base de données et mélanger leur id
                    $cartes = $this->getDoctrine()->getRepository(Carte::class)->findAll();

                    /** @var Carte $carte */
                    $tCartes = array();
                    foreach ($cartes as $carte) {
                        $tCartes[] = $carte->getId();
                    }
                    shuffle($tCartes);

                    //retrait de la première carte
                    $carte_ecartee = array_pop($tCartes);

                    //Distribution des cartes aux joueurs,
                    $tMainJ1 = array();
                    for($i=0; $i<7; $i++) {
                        $tMainJ1[] = array_pop($tCartes);
                    }
                    $tMainJ2 = array();
                    for($i=0; $i<6; $i++) {
                        $tMainJ2[] = array_pop($tCartes);
                    }

                    //La création de la pioche ,sauvegarde des dernières cartes dans la pioche
                    $tPioche = $tCartes;

                    // actions au départ
                    $secret = array('etat'=>0, 'carte'=>0);
                    $dissimulation = array('etat'=>0, 'carte'=>array());
                    $cadeau = array('etat'=>0, 'carte'=>array());
                    $concurrence = array('etat'=>0, 'carte'=>array('p1'=>array(), 'p2'=>array()));
                    $carte_j = array();

                    // tableau de toutes les actions
                    $tAction = array('dissimulation'=>$dissimulation, 'secret'=>$secret, 'cadeau'=>$cadeau, 'concurrence'=>$concurrence);

                    $getattr = $partie->getObjectifAttribution();
                    $getattr->p_1->j1 = 0;
                    $getattr->p_1->j2 = 0;
                    $getattr->p_2->j1 = 0;
                    $getattr->p_2->j2 = 0;
                    $getattr->p_3->j1 = 0;
                    $getattr->p_3->j2 = 0;
                    $getattr->p_4->j1 = 0;
                    $getattr->p_4->j2 = 0;
                    $getattr->p_5->j1 = 0;
                    $getattr->p_5->j2 = 0;
                    $getattr->p_6->j1 = 0;
                    $getattr->p_6->j2 = 0;
                    $getattr->p_7->j1 = 0;
                    $getattr->p_7->j2 = 0;

                    $stat_manche = $partie->getManche();
                    $stat_manche->nb += 1;
                    $stat_manche->etat = 0;

                    $j1_score_nb = $partie->getScoreJ1();
                    $j2_score_nb = $partie->getScoreJ2();

                    $j1_score_nb->point_o_nb = 0;
                    $j2_score_nb->point_o_nb = 0;

                    $partie->setCarteEcarte(json_encode($carte_ecartee));
                    $partie->setMainJ1(json_encode($tMainJ1));
                    $partie->setMainJ2(json_encode($tMainJ2));
                    $partie->setPioche(json_encode($tPioche));
                    $partie->setManche(json_encode($stat_manche));
                    $partie->setActionJ1(json_encode($tAction));
                    $partie->setActionJ2(json_encode($tAction));
                    $partie->setObjectifAttribution(json_encode($getattr));
                    $partie->setTerrainJ1(json_encode($carte_j));
                    $partie->setTerrainJ2(json_encode($carte_j));
                    $partie->setScoreJ1(json_encode($j1_score_nb));
                    $partie->setScoreJ2(json_encode($j2_score_nb));

                    $em = $this->getDoctrine()->getManager();
                    $em->persist($partie);
                    $em->flush();

                    return $this->redirectToRoute('afficher_partie', ['id' => $partie->getId(), 'partie'=>$partie]);
                }

            }

        $siGg = $partie->getVainqueur();

            if ($siGg != 'R'){
                $this->addFlash('W_manche',
                    $partie->getVainqueur().' est le gagnant de la partie !');
            }








        return $this->render('game/afficher.html.twig',
            [
                'partie' => $partie,
                'cartes' => $tCartes,
                'objectifs' => $objectifs,
                'main'=> $cartes_main,
                'action'=> $action,
                'action_ad'=> $action_ad,
                'manche' => $manche,
                'monTour' => $tour,
                'currentUser' =>  $currentuser,
                'myTerrain' => $myTerrain,
                'hisTerrain' => $hisTerrain,
                'myScore' => $myScore,
                'hisScore' => $hisScore,
                'myId' => $myId,
                'hisId' => $hisId,
                'myName' => $myName

            ]);
    }

    /**
     * @Route("/refresh/{id}", name="refresh_partie")
     */
    public function refreshPartie(Partie $partie) {

        //Choisir current joueur
        $currentuser = $this->getUser();

        if ($currentuser == $partie->getJ1()){
            //$main = $partie->getMainJ1();
            $action= $partie->getActionJ1();
            $action_ad = $partie->getActionJ2();
            $tour = $partie->getTour();
            $myId = $partie->getJ1()->getId();
            $hisId = $partie->getJ2()->getId();

        }elseif($currentuser == $partie->getJ2()){
            //$main = $partie->getMainJ2();
            $action= $partie->getActionJ2();
            $action_ad = $partie->getActionJ1();
            $tour = $partie->getTour();
            $myId = $partie->getJ2()->getId();
            $hisId = $partie->getJ1()->getId();
        }

        $objectif_attr = $partie->getObjectifAttribution();




        $Gagnant = $partie->getVainqueur();
        $Manche = $partie->getManche();
        $nbManche = $Manche->nb;

        $data = array('actionAdv' => $action_ad, 'monAction' => $action,'monTour' => $tour, 'siGagnant' => $Gagnant, 'siNvManche' => $nbManche, 'myId'=>$myId, 'hisId'=>$hisId, 'objectif_attr'=>$objectif_attr);

        return new JsonResponse($data);


    }

    /**
     * @Route("/recup_cad/{id}", name="recup_carte_cad")
     */
    public function recup_cadPartie(Partie $partie) {

        //récupérer les cartes depuis la base de données et mélanger leur id
        $cartes = $this->getDoctrine()->getRepository(Carte::class)->findAll();

        /** @var Carte $carte */
        $tCartes = array();
        foreach($cartes as $carte) {
            $tCartes[$carte->getId()] = $carte;
        }


        //Choisir current joueur
        $currentuser = $this->getUser();

        if ($currentuser == $partie->getJ1()){
            //$main = $partie->getMainJ1();
            //$action= $partie->getActionJ1();
            $action_ad = $partie->getActionJ2();

        }elseif($currentuser == $partie->getJ2()){
            //$main = $partie->getMainJ2();
            //$action= $partie->getActionJ2();
            $action_ad = $partie->getActionJ1();
        }


        return $this->render('recup/cadeau.html.twig', ['action_ad'=> $action_ad, 'partie' => $partie, 'cartes' => $tCartes]);


    }

    /**
     * @Route("/recup_conc/{id}", name="recup_carte_conc")
     */
    public function recup_concPartie(Partie $partie) {

        //récupérer les cartes depuis la base de données et mélanger leur id
        $cartes = $this->getDoctrine()->getRepository(Carte::class)->findAll();

        /** @var Carte $carte */
        $tCartes = array();
        foreach($cartes as $carte) {
            $tCartes[$carte->getId()] = $carte;
        }


        //Choisir current joueur
        $currentuser = $this->getUser();

        if ($currentuser == $partie->getJ1()){
            //$main = $partie->getMainJ1();
            //$action= $partie->getActionJ1();
            $action_ad = $partie->getActionJ2();

        }elseif($currentuser == $partie->getJ2()){
            //$main = $partie->getMainJ2();
            //$action= $partie->getActionJ2();
            $action_ad = $partie->getActionJ1();
        }


        return $this->render('recup/conc.html.twig', ['action_ad'=> $action_ad, 'partie' => $partie, 'cartes' => $tCartes]);


    }



    }