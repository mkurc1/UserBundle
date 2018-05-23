<?php

namespace UserBundle\Command;

use UserBundle\Doctrine\UserManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CreateUserCommand extends ContainerAwareCommand
{
    protected static $defaultName = 'user:create';

    protected function configure()
    {
        $this
            ->setDescription('Create a user')
            ->addArgument('username', InputArgument::REQUIRED)
            ->addArgument('email', InputArgument::REQUIRED)
            ->addArgument('password', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username = $input->getArgument('username');
        $email = $input->getArgument('email');
        $password = $input->getArgument('password');

        /** * @var UserManager $userManager */
        $userManager = $this->getContainer()->get(UserManager::class);

        $user = $userManager->findOneBy(['username' => $username]);
        if ($user) {
            throw new \Exception(sprintf('User %s already exists!', $username));
        }

        $user = $userManager->findOneBy(['email' => $email]);
        if ($user) {
            throw new \Exception(sprintf('User email %s already exists!', $username));
        }

        $userManager->createUser($username, $email, $password);

        $io = new SymfonyStyle($input, $output);
        $io->success(sprintf('You have a new user %s.', $username));
    }
}
