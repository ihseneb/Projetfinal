<?php
/**
 * Created by PhpStorm.
 * User: ihseneb
 * Date: 20/02/2018
 * Time: 09:26
 */

// src/Controller/PartiesController.php
namespace App\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PartiesController extends Controller
{
    /**
     * @Route("/parties/nbparties", name="app_parties_nbparties")
     */
    public function nbparties()
    {
        $parties = mt_rand(0, 100);
        return $this->render('parties/nbparties.html.twig', array(
            'parties' => $parties,
        ));
    }
}

?>