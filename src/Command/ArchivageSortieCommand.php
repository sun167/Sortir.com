<?php

namespace App\Command;

use App\Entity\Sortie;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ArchivageSortieCommand extends Command
{
    protected static $defaultName = 'app:archivage-sortie';
    protected static $defaultDescription = 'Archivage des sorties depuis plus d\'un mois';

    private $entityManager;

    public function __construct(string $name = null, EntityManagerInterface $entityManager)
    {
        parent::__construct($name);
        $this->entityManager=$entityManager;
    }

    protected function configure(): void
    {
        $this
            ->setDescription(self::$defaultDescription);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->writeln('ARCHIVAGE JOIN THE BATTLE');
        $sortieRepository = $this->entityManager->getRepository(Sortie::class);
        $sortie = $sortieRepository->findAll();
        $date = new \DateTime();
        $date->modify('-1 months');
        $deletedSorties = 0;

        /**
         * @var Sortie $sortie
         */
        foreach ($sortie as $sorties) {
            if($sortie->getDateDebut()) {
                $this->entityManager->remove($sorties);
                $deletedSorties++;
                $io->success('ET LA VICTOIRE REVIENT A : ARCHIVAGE');
            } else {
                $io->error('ET LA VICTOIRE REVIENT A : ERROR');
            }
        }
        $this->entityManager->flush();
        $io->success($deletedSorties . " ont été archivées !!!");
        return Command::SUCCESS;
    }
}
