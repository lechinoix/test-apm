<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CreateUserCommand extends Command
{
    public const INPUT_EMAIL = 'email';
    public const INPUT_PASSWORD = 'password';

    /**
     * @var ManagerRegistry
     */
    private $managerRegistry;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    public function __construct(ManagerRegistry $managerRegistry, UserPasswordEncoderInterface $encoder)
    {
        parent::__construct();

        $this->managerRegistry = $managerRegistry;
        $this->encoder = $encoder;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setName('database:user:create')
            ->setDescription('Create an admin user.')
            ->addArgument(self::INPUT_EMAIL, InputArgument::REQUIRED, 'The email')
            ->addArgument(self::INPUT_PASSWORD, InputArgument::REQUIRED, 'The password');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $newUser = new User();
        $email = strval($input->getArgument(self::INPUT_EMAIL));
        $newUser->setEmail($email);
        $password = strval($input->getArgument(self::INPUT_PASSWORD));
        $encodedPassword = $this->encoder->encodePassword($newUser, $password);
        $newUser->setPassword($encodedPassword);
        $newUser->setRoles([User::ROLE_ADMIN]);
        $this->managerRegistry->getManager()->persist($newUser);
        $this->managerRegistry->getManager()->flush();
        $output->writeln(sprintf('Created admin <comment>%s</comment>', $email));

        return 0;
    }

    /**
     * {@inheritdoc}
     */
    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        $questions = [];
        if (!$input->getArgument(self::INPUT_EMAIL)) {
            $question = new Question('Please choose an email: ');
            $question->setValidator(function ($email) {
                if (empty($email)) {
                    throw new \Exception('Email can not be empty');
                }

                return $email;
            });
            $questions[self::INPUT_EMAIL] = $question;
        }
        if (!$input->getArgument(self::INPUT_PASSWORD)) {
            $question = new Question('Please choose a password: ');
            $question->setValidator(function ($password) {
                if (empty($password)) {
                    throw new \Exception('Password can not be empty');
                }

                return $password;
            });
            $question->setHidden(true);
            $questions[self::INPUT_PASSWORD] = $question;
        }

        foreach ($questions as $name => $question) {
            $answer = $this->getHelper('question')->ask($input, $output, $question);
            $input->setArgument($name, $answer);
        }
    }
}
