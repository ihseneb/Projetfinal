<?php
/**
 * Created by PhpStorm.
 * User: ihseneb
 * Date: 06/03/2018
 * Time: 10:11
 */
namespace App\DataFixtures;

use App\Entity\Utilisateur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        foreach ($this->getUserData() as [$username, $password, $email, $roles, $nb_partie, $win_parties, $partie_encours]) {
            $user = new Utilisateur();
            $user->setUsername($username);
            $user->setPassword($this->passwordEncoder->encodePassword($user, $password));
            $user->setEmail($email);
            $user->setRoles($roles);
            $user->setNbPartie($nb_partie);
            $user->setWinParties($win_parties);
            $user->setPartieEncours($partie_encours);

            $manager->persist($user);
            //$this->addReference($username, $user);

        }

        $manager->flush();
    }

    private function getUserData(): array
    {
        return [
            // $userData = [$username, $password, $email, $roles];
            ['Benjamin', 'benmdp', 'jane_admin@symfony.com', 'ROLE_ADMIN','0','0','0'],
            ['Ihcen', 'uttmdp', 'tom_admin@symfony.com', 'ROLE_ADMIN','0','0','0'],
            ['john_user', 'kitten', 'john_user@symfony.com', 'ROLE_USER','0','0','0'],
        ];
    }


}