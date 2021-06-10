<?php

namespace App\Command;

use App\Entity\Etat;
use App\Entity\Sortie;
use DateInterval;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ChangeStateSortieCommand extends Command
{
    protected static $defaultName = 'app:change-etat';
    protected static $defaultDescription = 'change state of Sortie from date';
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;


    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('change-etat')
            ->setDescription(self::$defaultDescription);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $em = $this->entityManager;
        $sortieRepository = $em->getRepository("App:Sortie");
        $etatRepository = $em->getRepository("App:Etat");

        $sorties = $sortieRepository->findAll();

        $now = new \DateTime('now');
        foreach ($sorties as $s) {
            $minutesToAdd = $s->getDuree();
            $etat = $s->getEtat();
            if ($now > $s->getDateFin() && $now < $s->getDateDebut()) {
                $etat = $etatRepository->find(3);
                $io->success('cloturé');
            } else if ($now >= $s->getDateDebut() && $now < ($s->getDateDebut()->modify("+{$minutesToAdd} minutes"))) {
                $io->success('en cours');
                $etat = $etatRepository->find(4);
            } else if ($now > ($s->getDateDebut()->modify("+{$minutesToAdd} minutes"))
                && $now <= ($s->getDateDebut()->modify("+43800 minutes"))) {
                $etat = $etatRepository->find(5);
                $io->success('passée');


            } else if ($now > ($s->getDateDebut()->modify("+43800 minutes"))) {
                $etat = $etatRepository->find(7);
                $io->success('archivée');
            }
            if ($etat != $s->getEtat()) {
                $s->setEtat($etat);
                $em->persist($s);
                $em->flush();
            }
        }

        return Command::SUCCESS;
    }
}
