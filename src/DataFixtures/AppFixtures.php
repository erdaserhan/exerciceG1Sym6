<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
# On va hasher les mot de passe
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
# On va récupérer notre entité User
use App\Entity\User;

class AppFixtures extends Fixture
{

    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {

        $user = new User();
        $user->setUsername('admin');
        $user->setUserMail('admin@gmail.com');
        $user->setRoles(['ROLE_ADMIN', 'ROLE_REDAC', 'ROLE_MODERATOR']);
        $pwdHash = $this->passwordHasher->hashPassword($user, 'admin');
        $user->setPassword($pwdHash);
        $user->setUserActive(true);
        $user->setUserRealName('The Admin !');

        $manager->persist($user);




        # Instanciation de 5 Rédacteurs
        for ($i = 1; $i <= 5; $i++) {
            $user = new User();
            $user->setUsername('redac'.$i);
            $user->setUserMail('redac'.$i.'@gmail.com');
            $user->setRoles(['ROLE_REDAC']);
            $pwdHash = $this->passwordHasher->hashPassword($user, 'redac'.$i);
            $user->setPassword($pwdHash);
            $user->setUserActive(true);
            $user->setUserRealName('The Redac '.$i);

            $manager->persist($user);
        }

        # Instanciation de 5 Moderators
        for ($i = 1; $i <= 3; $i++) {
            $user = new User();
            $user->setUsername('moderator'.$i);
            $user->setUserMail('moderator'.$i.'@gmail.com');
            $user->setRoles(['ROLE_MODERATOR']);
            $pwdHash = $this->passwordHasher->hashPassword($user, 'moderator'.$i);
            $user->setPassword($pwdHash);
            $user->setUserActive(true);
            $user->setUserRealName('The Moderator '.$i);

            $manager->persist($user);
        }


        # Instanciation de 5 Users
        for ($i = 1; $i <= 10; $i++) {
            $user = new User();
            $user->setUsername('moderator'.$i);
            $user->setUserMail('moderator'.$i.'@gmail.com');
            $user->setRoles(['ROLE_MODERATOR']);
            $pwdHash = $this->passwordHasher->hashPassword($user, 'moderator'.$i);
            $user->setPassword($pwdHash);
            $user->setUserActive(true);
            $user->setUserRealName('The Moderator '.$i);

            $manager->persist($user);
        }



        $manager->flush();
    }
}
