<?php


namespace App\Upload;


use App\Entity\Sortie;

class SortieImage
{
    public function save($file, Sortie $sortie, $directory) {
        $newFileName = $sortie->getNom().'.'.uniqid().'.'.$file->guessExtension();
        $file->move($newFileName);
        $sortie->setUrlPhoto($newFileName);
    }
}