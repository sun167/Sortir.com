<?php


namespace App\EventListener;


use App\Entity\Sortie;
use Doctrine\ORM\Event\LifecycleEventArgs;

class SortieArchiveListener
{
    public function sortieArchive(LifecycleEventArgs $args){
        $entity = $args->getObject();
        if(!entity instanceof Sortie)
            return;

        dd($entity);
    }
}