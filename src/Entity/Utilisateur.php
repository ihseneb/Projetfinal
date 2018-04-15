<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UtilisateurRepository")
 * @ORM\Table(name="utilisateur")
 * @UniqueEntity(fields="email", message="Email déjà pris")
 * @UniqueEntity(fields="username", message="Username déjà pris")
 */

class Utilisateur implements UserInterface, \Serializable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100, unique=true)
     * @Assert\NotBlank()
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(type="string", unique=true)
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100)
     */
    private $password;

    /**
     *
     * @ORM\Column(type="string")
     */
    private $roles=[];


    /**
     * @ORM\Column(type="integer")
     */

    private $nb_partie;

    /**
     * @ORM\Column(type="integer")
     */

    private $win_parties;



    /**
     * @ORM\Column(type="integer")
     */

    private $partie_encours;


    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $isBan;


    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }
    /**
     * @return string
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }
    /**
     * @param string $username
     */
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }


    /**
     * @return string
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getRoles(): ?array
    {

        $roles = $this->roles;

        return array($roles);

    }

    /**
     * @param mixed $roles
     */
    public function setRoles($roles): void
    {
        $this->roles = $roles;
    }

    /**
     * @return mixed
     */
    public function getNbPartie()
    {
        return $this->nb_partie;
    }

    /**
     * @param mixed $nb_partie
     */
    public function setNbPartie($nb_partie): void
    {
        $this->nb_partie = $nb_partie;
    }

    /**
     * @return mixed
     */
    public function getWinParties()
    {
        return $this->win_parties;
    }

    /**
     * @param mixed $win_parties
     */
    public function setWinParties($win_parties): void
    {
        $this->win_parties = $win_parties;
    }

    /**
     * @return mixed
     */
    public function getPartieEncours()
    {
        return $this->partie_encours;
    }

    /**
     * @param mixed $partie_encours
     */
    public function setPartieEncours($partie_encours): void
    {
        $this->partie_encours = $partie_encours;
    }

    /**
     * @return string
     */
    public function getisBan(): string
    {
        return $this->isBan;
    }

    /**
     * @param string $isBan
     */
    public function setIsBan(string $isBan): void
    {
        $this->isBan = $isBan;
    }


    /**
     * Retour le salt qui a servi à coder le mot de passe
     *
     * {@inheritdoc}
     */
    public function getSalt(): ?string
    {
        // See "Do you need to use a Salt?" at https://symfony.com/doc/current/cookbook/security/entity_provider.html
        // we're using bcrypt in security.yml to encode the password, so
        // the salt value is built-in and you don't have to generate one

        return null;
    }

    /**
     * Removes sensitive data from the user.
     *
     * {@inheritdoc}
     */
    public function eraseCredentials(): void
    {
        // Nous n'avons pas besoin de cette methode car nous n'utilions pas de plainPassword
        // Mais elle est obligatoire car comprise dans l'interface UserInterface
        // $this->plainPassword = null;
    }

    /**
     * {@inheritdoc}
     */
    public function serialize(): string
    {
        return serialize([$this->id, $this->username, $this->password]);
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized): void
    {
        [$this->id, $this->username, $this->password] = unserialize($serialized, ['allowed_classes' => false]);
    }




}
