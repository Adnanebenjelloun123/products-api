<?php

namespace App\Command\Security;

use App\Entity\ApiToken;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class GenerateApiKeyCommand extends Command
{
    public function __construct( ManagerRegistry $doctrine, UserRepository $userRepository)
    {
        $this->doctrine = $doctrine;
        $this->userRepository = $userRepository;
        parent::__construct();

    }

    protected function configure()
    {
        $this
          ->setName('app:apikey:generate')
          ->setDescription('Generate Api key.')
          ->addArgument('user', InputArgument::REQUIRED, 'The user of the api key')
          ->addArgument('hostname', InputArgument::OPTIONAL, 'This hostname is used as the origin of api key request')
          ->addArgument('expiresAt', InputArgument::OPTIONAL, 'The expiration datetime of the api key')
        ;
    }

    /**
     * @return int|void|null
     *
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Generate a new api key');
        $hostname = null;
        if ($input->hasArgument('user')) {
            $user = $this->userRepository->find($input->getArgument('user'));
            if (null === $user) {
                $output->writeln('<error>User Not found.</error>');
                throw new RuntimeException();
            }
        } else {
            $user = $io->askHidden(
              'Enter a user id?',
              function ($user) use ($output) {
                  if (empty($user)) {
                      $output->writeln('<error>User cannot be empty.</error>');
                      throw new RuntimeException();
                  }

                  return $user;
              }
            );
        }
        if ($input->hasArgument('hostname')) {
            $hostname = $input->getArgument('hostname');
        }
        $expiresAt = null;
        if ($input->hasArgument('expiresAt') && null !== $input->getArgument('expiresAt')) {
            $expiresAt = new DateTime($input->getArgument('expiresAt'));
        }
        try {
            $em = $this->doctrine->getManager();
            $apiToken = new ApiToken($user);
            $apiToken->setHostname($hostname);
            $apiToken->setExpiresAt($expiresAt);
            $em->persist($apiToken);
            $em->flush();
            $output->writeln('<fg=green>The api key '.$apiToken->getToken().' has been generated successfully.</>');
        } catch (Exception $exception) {
            $output->writeln($exception->getMessage());

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}