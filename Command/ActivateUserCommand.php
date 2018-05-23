<?php

namespace App\UserBundle\Command;

use App\UserBundle\Doctrine\UserManager;
use App\UserBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ActivateUserCommand extends ContainerAwareCommand
{
    protected static $defaultName = 'user:activate';

    protected function configure()
    {
        $this
            ->setDescription('Activate a user')
            ->addArgument('username', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username = $input->getArgument('username');

        /** * @var UserManager $userManager */
        $userManager = $this->getContainer()->get(UserManager::class);

        /** @var User $user */
        $user = $userManager->findOneBy(['username' => $username]);
        if (!$user) {
            throw new \Exception(sprintf('User %s not exists!', $username));
        }

        $user->setEnabled(true);
        $userManager->update($user);

        $io = new SymfonyStyle($input, $output);
        $io->success(sprintf('User "%s" has been activated.', $username));
    }
}
