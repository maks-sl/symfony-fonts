<?php

declare(strict_types=1);

namespace App\Command\User;

use App\ReadModel\User\UserFetcher;
use App\Model\User\UseCase\Create;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreateCommand extends Command
{
    private $users;
    private $validator;
    private $handler;

    public function __construct(UserFetcher $users, ValidatorInterface $validator, Create\Handler $handler)
    {
        $this->users = $users;
        $this->validator = $validator;
        $this->handler = $handler;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('user:create')
            ->setDescription('Creates a new active user');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');
        $command = new Create\Command();

        $email = $helper->ask($input, $output, new Question('Email: '));
        if ($this->users->hasByEmail($email)) {
            throw new LogicException('User with this email already exists.');
        }
        $command->email = $email;
        $command->firstName = $helper->ask($input, $output, new Question('First name: '));
        $command->lastName = $helper->ask($input, $output, new Question('Last name: '));
        $command->password = $helper->ask($input, $output, (new Question('Password: '))->setHidden(true));

        $violations = $this->validator->validate($command);

        if ($violations->count()) {
            foreach ($violations as $violation) {
                $output->writeln('<error>' . $violation->getPropertyPath() . ': ' . $violation->getMessage() . '</error>');
            }
            return 0;
        }

        $this->handler->handle($command);

        $output->writeln('<info>User created</info>');
        return 0;
    }
}
