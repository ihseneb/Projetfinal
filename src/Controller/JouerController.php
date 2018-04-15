<?php
/**
 * Created by PhpStorm.
 * User: ihseneb
 * Date: 20/02/2018
 * Time: 09:41
 */

// src/Controller/JouerController.php
namespace App\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class JouerController extends Controller
{
    /**
     * @Route("/jouer", name="app_jouer")
     */
    public function name()
    {
        return $this->render('jouer/jouer.html.twig');
    }
}

?>