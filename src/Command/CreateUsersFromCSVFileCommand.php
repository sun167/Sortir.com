<?php

namespace App\Command;

use App\Entity\Participant;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class CreateUsersFromCSVFileCommand extends Command
{
    //INSTALLATION COMPOSANT composer require symfony/serializer
    private SymfonyStyle $io;
    private EntityManagerInterface $entityManager;
    private string $dataDirectory;
    private ParticipantRepository $participantRepository;

    public function __construct(EntityManagerInterface $entityManager, string $dataDirectory, ParticipantRepository $participantRepository)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->dataDirectory = $dataDirectory;
        $this->participantRepository = $participantRepository;
    }

    protected static $defaultName = 'app:create-users-from-file';

    protected function configure(): void {
        $this->setDescription('Importer donner de Participant via un fichier CSV');
    }

    protected function initialize(InputInterface $input, OutputInterface $output) : void {
        $this->io = new SymfonyStyle($input,$output);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $this->createUsers();
        return Command::SUCCESS;
    }

    private function getDataFromFile(): array {
        //en console faire console app:create-users-from-file pour inserer le fichier nommer ci-dessous
        $file = $this->dataDirectory.'Participant_data.csv';
        $fileExtension = pathinfo($file, PATHINFO_EXTENSION);
        $normalizers = [new ObjectNormalizer()];
        $encoders = [new CsvEncoder()];

        $serializer_data = new Serializer($normalizers,$encoders);

        /**
         * @var string $fileString
         */
        $fileString = file_get_contents($file);

        $data = $serializer_data->decode($fileString, $fileExtension);

        if(array_key_exists('results', $data)) {
            return $data['results'];
        }
        return $data;

    }

    private function createUsers(): void {
        $this->io->section('CREATION DES PARTICIPANTS A PARTIR DU FICHIER CSV');
        $usersCreated = 0;
        foreach ($this->getDataFromFile() as $row) {
            if (array_key_exists('email', $row) && !empty($row['email'])) {
                $participant = $this->participantRepository->findOneBy(['email' => $row['email']]);

                if (!$participant) {
                    $participant = new Participant();
                    $participant
                        ->setPseudo($row['pseudo'])
                        ->setRoles(["ROLE_PARTICIPANT"])
                        ->setPassword('badpassword')
                        ->setNom($row['nom'])
                        ->setPrenom($row['prenom'])
                        ->setTelephone($row['telephone'])
                        ->setEmail($row['email'])
                        ->setIsAdmin(false)
                        ->setIsActif(true);

                    $this->entityManager->persist($participant);
                    $usersCreated++;
                }
            }
        }
        $this->entityManager->flush();
        if ($usersCreated > 0) {
            $string = "{$usersCreated} UTILISATEURS AJOUTEES EN BDD";
        } else {
            $string = 'AUCUN UTILISATEUR AJOUTEE';
        }
        $this->io->success($string);
    }
}

