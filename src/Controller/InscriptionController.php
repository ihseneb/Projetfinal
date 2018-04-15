<?php
/**
 * Created by PhpStorm.
 * User: ihseneb
 * Date: 10/03/2018
 * Time: 19:13
 */

// src/Controller/InscriptionController.php
namespace App\Controller;

use App\Form\UtilisateurType;
use App\Entity\Utilisateur;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class InscriptionController extends Controller
{
    /**
     * @Route("/inscription", name="user_registration")
     */
    public function registerAction(Request $request, UserPasswordEncoderInterface $passwordEncoder, \Swift_Mailer $mailer)
    {
        $user = new Utilisateur();
        $form = $this->createForm(UtilisateurType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $password = $passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);

            // Par defaut l'utilisateur aura toujours le rôle ROLE_USER
            $user->setRoles('ROLE_USER');
            $user->setNbPartie('0');
            $user->setWinParties('0');
            $user->setPartieEncours('0');
            $user->setIsBan('off');

            // On enregistre l'utilisateur dans la base
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $name = $user->getUsername();

            $message = (new \Swift_Message('user_registration'))
                ->setFrom('edensrush3@gmail.com')
                ->setTo($user->getEmail())
                ->setBody(
                    $this->renderView(
                    // templates/emails/registration.html.twig
                        'emails/registration.html.twig',
                        array('name' => $name)
                    ),
                    'text/html'
                );

            $mailer->send($message);


            return $this->redirectToRoute('security_login');
        }

        return $this->render(
            'inscription/inscription.html.twig',
            array('form' => $form->createView())
        );
    }
}
