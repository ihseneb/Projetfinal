<?php
/**
 * Created by PhpStorm.
 * User: ihseneb
 * Date: 20/02/2018
 * Time: 09:34
 */

// src/Controller/ProfilController.php
namespace App\Controller;

use App\Entity\Utilisateur;
use App\Entity\Objectif;
use App\Entity\Partie;
use App\Entity\Carte;
use App\Repository\PartieRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ProfilController extends Controller
{
    /**
     * @Route("/profil", name="profil")
     */
    public function profil()
    {
        $myid = $this->getUser()->getId();

        $partie = $this->getDoctrine()
            ->getRepository(Partie::class)
            ->findPartieById($myid);

        return $this->render('profil/profil.html.twig', array(
            'mypartie' => $partie,
            'myid' => $myid
        ));
    }
}

?>