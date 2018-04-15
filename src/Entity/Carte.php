<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CarteRepository")
 * @ORM\Table(name="carte")
 */

class Carte
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="nom", type="string", length=100)
     */

    private $nom;

    /**
     * @ORM\Column(name="points", type="integer")
     */

    private $points;

    /**
     * @ORM\Column(name="img", type="text")
     */

    private $img;

    /**
     * Plusieurs Objectifs on plusieurs carte
     * @ORM\ManyToOne(targetEntity="App\Entity\Objectif", inversedBy="cartes")
     */
    private $objectifs;

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
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * @param mixed $nom
     */
    public function setNom($nom): void
    {
        $this->nom = $nom;
    }

    /**
     * @return mixed
     */
    public function getPoints()
    {
        return $this->points;
    }

    /**
     * @param mixed $points
     */
    public function setPoints($points): void
    {
        $this->points = $points;
    }

    /**
     * @return mixed
     */
    public function getImg()
    {
        return $this->img;
    }

    /**
     * @param mixed $img
     */
    public function setImg($img): void
    {
        $this->img = $img;
    }

    /**
     * Get objectifs
     * @return \App\Entity\Objectif
     */
    public function getObjectifs()
    {
        return $this->objectifs;
    }



}


