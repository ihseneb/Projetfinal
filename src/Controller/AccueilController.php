<?php
/**
 * Created by PhpStorm.
 * User: ihseneb
 * Date: 20/02/2018
 * Time: 09:18
 */

// src/Controller/AccueilController.php
namespace App\Controller;
use App\Entity\Utilisateur;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AccueilController extends Controller
{
    /**
     * @Route("/", name="index")
     */
    public function name()
    {
        return $this->render('accueil/accueil.html.twig');
    }

    /**
     * @Route("/admin", name="admin")
     */
    public function admin()
    {
        $joueurs = $this->getDoctrine()->getRepository(Utilisateur::class)->findAll();
        return $this->render('admin/index.html.twig', ['joueurs' => $joueurs]);
    }

    /**
     * @Route("/admin{id}", name="admin_suppr")
     */

    public function deleteUser(Utilisateur $user)
    {


        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush();


        return $this->redirectToRoute('admin');
    }


    /**
     * @Route("/admin/ban/{id}", name="admin_ban")
     */

    public function banUser(Utilisateur $user)
    {

        if ($user->getisBan() == 'off') {


            $em = $this->getDoctrine()->getManager();
            $user->setIsBan('on');
            $em->persist($user);

            $em->flush();
        }else{

            $em = $this->getDoctrine()->getManager();
            $user->setIsBan('off');
            $em->persist($user);

            $em->flush();

        }


        return $this->redirectToRoute('admin');
    }


    /**
     * @Route("/regle", name="regle")
     */
    public function regle()
    {
        return $this->render('accueil/regle.html.twig');
    }

    /**
     * @Route("/rules", name="rules")
     */
    public function rules()
    {
        return $this->render('accueil/rules.html.twig');
    }

    /**
     * @Route("/reglas", name="reglas")
     */
    public function reglas()
    {
        return $this->render('accueil/reglas.html.twig');
    }


}

?>