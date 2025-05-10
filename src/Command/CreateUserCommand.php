<?php

namespace App\Command;

use App\DTO\Request\User\UserCreateRequestDto;
use App\Service\UserService\UserServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:create-user',
    description: 'Create a user',
)]
class CreateUserCommand extends Command
{
    public function __construct(
        private readonly UserServiceInterface $userService,
        private readonly EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('username', InputArgument::REQUIRED, 'The username of the user')
            ->addArgument('email', InputArgument::REQUIRED, 'The email of the user')
            ->addArgument('password', InputArgument::REQUIRED, 'The password of the user')
            ->addArgument('firstName', InputArgument::REQUIRED, 'The first name of the user')
            ->addArgument('lastName', InputArgument::REQUIRED, 'The last name of the user')
            ->addArgument('canReceiveNotifications', InputArgument::REQUIRED, 'The notification preference of the user')
            ->addArgument('roles', InputArgument::IS_ARRAY, 'The roles of the user');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $username = $input->getArgument('username');
        $email = $input->getArgument('email');
        $password = $input->getArgument('password');
        $firstName = $input->getArgument('firstName');
        $lastName = $input->getArgument('lastName');
        $canReceiveNotifications = $input->getArgument('canReceiveNotifications');
        $roles = $input->getArgument('roles');

        $userCreateRequest = new UserCreateRequestDto(
            $username,
            $firstName,
            $lastName,
            $email,
            $password
        );
        $user = $this->userService->createUser($userCreateRequest);

        $user->setCanReceiveNotifications($canReceiveNotifications);
        $user->setRoles($roles);
        $this->entityManager->flush();

        $io->success('User created successfully.');

        return Command::SUCCESS;
    }
}
