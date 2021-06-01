<?php


namespace App\Entity;
use App\Entity\Sortie;

class SortieSearch
{

    /**
     * STRING DE LA BARRE DE RECHERCHE
     * @var string
     */
    private $q = '';


    /**
     * @var boolean
     */
    private $sortiePassee = false;

    /**
     *
     */
    private $campus;
    /**
     * @return string
     */

//    private $premiereDate;
//    private $deuxiemeDate;
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


}