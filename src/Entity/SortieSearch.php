<?php


namespace App\Entity;

class SortieSearch
{

    /**
     * STRING DE LA BARRE DE RECHERCHE
     * @var string|null
     */
    private $q = '';
    private $sortiePassee = false;
    private $campus;
    private $premierDate;
    private $deuxiemeDate;
    private $inscrit = false;
    private $nonInscrit = false;
    private $organisateur = false;

    public function getQ(): string
    {
        return $this->q;
    }

    /**
     * @param string $q
     */
    public function setQ(string $q): void
    {
        $this->q = $q;
    }


    /**
     * @return bool
     */
    public function isSortiePassee(): bool
    {
        return $this->sortiePassee;
    }

    /**
     * @param bool $sortiePassee
     */
    public function setSortiePassee(bool $sortiePassee): void
    {
        $this->sortiePassee = $sortiePassee;
    }

    /**
     * @return mixed
     */
    public function getCampus()
    {
        return $this->campus;
    }

    /**
     * @param mixed $campus
     */
    public function setCampus($campus): void
    {
        $this->campus = $campus;
    }

    /**
     * @return mixed
     */
    public function getPremierDate()
    {
        return $this->premierDate;
    }

    /**
     * @param mixed $premierDate
     */
    public function setPremierDate($premierDate): void
    {
        $this->premierDate = $premierDate;
    }

    /**
     * @return mixed
     */
    public function getDeuxiemeDate()
    {
        return $this->deuxiemeDate;
    }

    /**
     * @param mixed $deuxiemeDate
     */
    public function setDeuxiemeDate($deuxiemeDate): void
    {
        $this->deuxiemeDate = $deuxiemeDate;
    }

    /**
     * @return bool
     */
    public function isInscrit(): bool
    {
        return $this->inscrit;
    }

    /**
     * @param bool $inscrit
     */
    public function setInscrit(bool $inscrit): void
    {
        $this->inscrit = $inscrit;
    }

    /**
     * @return bool
     */
    public function isNonInscrit(): bool
    {
        return $this->nonInscrit;
    }

    /**
     * @param bool $nonInscrit
     */
    public function setNonInscrit(bool $nonInscrit): void
    {
        $this->nonInscrit = $nonInscrit;
    }

    /**
     * @return bool
     */
    public function isOrganisateur(): bool
    {
        return $this->organisateur;
    }

    /**
     * @param bool $organisateur
     */
    public function setOrganisateur(bool $organisateur): void
    {
        $this->organisateur = $organisateur;
    }


}