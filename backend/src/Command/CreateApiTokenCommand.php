<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\JwtTokenService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:api-token:create',
    description: 'Create a new JWT API token for a user'
)]
class CreateApiTokenCommand extends Command
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly JwtTokenService $jwtTokenService,
        private readonly UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'User email')
            ->addArgument('password', InputArgument::REQUIRED, 'User password (for verification)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $email = $input->getArgument('email');
        $password = $input->getArgument('password');

        $user = $this->userRepository->findOneBy(['email' => $email]);

        if (!$user) {
            $io->error("User with email '{$email}' not found.");
            return Command::FAILURE;
        }

        // Verify password
        if (!$this->passwordHasher->isPasswordValid($user, $password)) {
            $io->error('Invalid password.');
            return Command::FAILURE;
        }

        // Generate token
        $tokenData = $this->jwtTokenService->createTokenForUser($user);

        $io->title('API Token Generated');
        $io->table(
            ['Property', 'Value'],
            [
                ['Email', $user->getEmail()],
                ['Name', "{$user->getFirstName()} {$user->getLastName()}"],
                ['Token', $tokenData['token']],
                ['Token Type', $tokenData['token_type']],
                ['Expires In', $tokenData['expires_in'] . ' seconds'],
                ['Expires At', $tokenData['expires_at']],
            ]
        );

        $io->warning('Store this token securely. You will not be able to see it again.');
        $io->note('Use this token in the Authorization header as: Bearer <token>');

        return Command::SUCCESS;
    }
}
