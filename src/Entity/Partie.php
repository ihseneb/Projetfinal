<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PartieRepository")
 * @ORM\Table(name="partie")
 */

class Partie
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;


    /**
     * @var Utilisateur
     * @ORM\ManyToOne(targetEntity="App\Entity\Utilisateur")
     */

    private $j1;

    /**
     * @var Utilisateur
     * @ORM\ManyToOne(targetEntity="App\Entity\Utilisateur")
     */

    private $j2;

    /**
     * @ORM\Column(type="text")
     */

    private $score_j1;

    /**
     * @ORM\Column(type="text")
     */

    private $score_j2;

    /**
     * @ORM\Column(type="text")
     */

    private $main_j1;

    /**
     * @ORM\Column(type="text")
     */

    private $main_j2;

    /**
     * @ORM\Column(name="terrain_j1", type="text")
     */

    private $terrain_j1;

    /**
     * @ORM\Column(name="terrain_j2", type="text")
     */

    private $terrain_j2;

    /**
     * @ORM\Column(name="tour", type="integer")
     */

    private $tour;

    /**
     * @ORM\Column(name="manche", type="text")
     */

    private $manche;

    /**
     * @ORM\Column(name="action_j1", type="text")
     */

    private $action_j1;

    /**
     * @ORM\Column(name="action_j2", type="text")
     */

    private $action_j2;

    /**
     * @ORM\Column(name="carte_ecarte", type="string")
     */

    private $carte_ecarte;

    /**
     * @ORM\Column(type="text")
     */
    private $objectif_attribution;

    /**
     * @ORM\Column(type="text")
     */

    private $pioche;

    /**
     * @ORM\Column(type="string")
     */
    private $vainqueur;

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
     * @return Utilisateur
     */
    public function getJ1(): Utilisateur
    {
        return $this->j1;
    }

    /**
     * @param Utilisateur $j1
     */
    public function setJ1(Utilisateur $j1): void
    {
        $this->j1 = $j1;
    }

    /**
     * @return Utilisateur
     */
    public function getJ2(): Utilisateur
    {
        return $this->j2;
    }

    /**
     * @param Utilisateur $j2
     */
    public function setJ2(Utilisateur $j2): void
    {
        $this->j2 = $j2;
    }

    /**
     * @return mixed
     */
    public function getScoreJ1()
    {
        return json_decode($this->score_j1);
    }

    /**
     * @param mixed $score_j1
     */
    public function setScoreJ1($score_j1): void
    {
        $this->score_j1 = $score_j1;
    }

    /**
     * @return mixed
     */
    public function getScoreJ2()
    {
        return json_decode($this->score_j2);
    }

    /**
     * @param mixed $score_j2
     */
    public function setScoreJ2($score_j2): void
    {
        $this->score_j2 = $score_j2;
    }

    /**
     * @return mixed
     */
    public function getMainJ1()
    {
        return json_decode($this->main_j1);
    }
    /**
     * @param mixed $main_j1
     */
    public function setMainJ1($main_j1): void
    {
        $this->main_j1 = $main_j1;
    }

    /**
     * @return mixed
     */
    public function getMainJ2()
    {
        return json_decode($this->main_j2);
    }
    /**
     * @param mixed $main_j2
     */
    public function setMainJ2($main_j2): void
    {
        $this->main_j2 = $main_j2;
    }


    /**
     * @return mixed
     */
    public function getTerrainJ1()
    {
        return json_decode($this->terrain_j1, true);
    }

    /**
     * @param mixed $terrain_j1
     */
    public function setTerrainJ1($terrain_j1): void
    {
        $this->terrain_j1 = $terrain_j1;
    }

    /**
     * @return mixed
     */
    public function getTerrainJ2()
    {
        return json_decode($this->terrain_j2, true);
    }

    /**
     * @param mixed $terrain_j2
     */
    public function setTerrainJ2($terrain_j2): void
    {
        $this->terrain_j2 = $terrain_j2;
    }

    /**
     * @return mixed
     */
    public function getTour()
    {
        return $this->tour;
    }

    /**
     * @param mixed $tour
     */
    public function setTour($tour): void
    {
        $this->tour = $tour;
    }

    /**
     * @return mixed
     */
    public function getManche()
    {
        return json_decode($this->manche);
    }

    /**
     * @param mixed $manche
     */
    public function setManche($manche): void
    {
        $this->manche = $manche;
    }

    /**
     * @return mixed
     */
    public function getActionJ1()
    {
        return json_decode($this->action_j1);
    }

    /**
     * @param mixed $action_j1
     */
    public function setActionJ1($action_j1): void
    {
        $this->action_j1 = $action_j1;
    }

    /**
     * @return mixed
     */
    public function getActionJ2()
    {
        return json_decode($this->action_j2);
    }

    /**
     * @param mixed $action_j2
     */
    public function setActionJ2($action_j2): void
    {
        $this->action_j2 = $action_j2;
    }

    /**
     * @return mixed
     */
    public function getCarteEcarte()
    {
        return $this->carte_ecarte;
    }

    /**
     * @param mixed $carte_ecarte
     */
    public function setCarteEcarte($carte_ecarte): void
    {
        $this->carte_ecarte = $carte_ecarte;
    }

    /**
     * @return mixed
     */
    public function getPioche()
    {
        return json_decode($this->pioche) ;
    }

    /**
     * @param mixed $pioche
     */
    public function setPioche($pioche): void
    {
        $this->pioche = $pioche;
    }

    /**
     * @return mixed
     */
    public function getObjectifAttribution()
    {
        return json_decode($this->objectif_attribution);
    }

    /**
     * @param mixed $objectif_attribution
     */
    public function setObjectifAttribution($objectif_attribution): void
    {
        $this->objectif_attribution = $objectif_attribution;
    }

    /**
     * @return mixed
     */
    public function getVainqueur()
    {
        return $this->vainqueur;
    }

    /**
     * @param mixed $vainqueur
     */
    public function setVainqueur($vainqueur): void
    {
        $this->vainqueur = $vainqueur;
    }




}
