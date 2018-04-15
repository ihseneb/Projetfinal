<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity(repositoryClass="App\Repository\ObjectifRepository")
 * @ORM\Table(name="Objectif")
 */

class Objectif
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="objectif", type="text")
     */

    private $objectif;


    /**
     * @ORM\Column(name="carte_img", type="text")
     */

    private $carte_img;

    /**
     * @ORM\Column(name="nb_point", type="integer")
     */

    private $nb_points;

    /**
     * Un objectif peut avoir plusieur carte
     * @ORM\OneToMany(targetEntity="App\Entity\Carte", mappedBy="objectifs")
     */

    private $cartes;


    public function __construct()
    {
        $this->cartes = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getObjectif()
    {
        return $this->objectif;
    }

    /**
     * @param mixed $objectif
     */
    public function setObjectif($objectif): void
    {
        $this->objectif = $objectif;
    }


    /**
     * @return mixed
     */
    public function getCarteImg()
    {
        return $this->carte_img;
    }

    /**
     * @param mixed $carte_img
     */
    public function setCarteImg($carte_img): void
    {
        $this->carte_img = $carte_img;
    }

    /**
     * @return mixed
     */
    public function getNbPoints()
    {
        return $this->nb_points;
    }

    /**
     * @param mixed $nb_points
     */
    public function setNbPoints($nb_points): void
    {
        $this->nb_points = $nb_points;
    }

    /**
     * Get Carte
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCartes()
    {
        return $this->cartes;
    }

    /**
     * @param mixed $cartes
     */
    public function setCartes($cartes): void
    {
        $this->carte = $cartes;
    }




}
