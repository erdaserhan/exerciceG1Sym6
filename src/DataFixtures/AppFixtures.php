<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
# On va hasher les mot de passe
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
# Chargement de Faker et création d'un alias nommé Faker
use Faker\Factory as Faker;
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

        # Instanciation de 3 Moderators
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

        //Instanciation de Faker en français
        $faker = Faker::create('fr_FR');

        # Instanciation entre 20 et 40 User sans rôles en utilisant Faker
        $hasard = mt_rand(20,40);
        for ($i = 1; $i <= $hasard; $i++) {
            $user = new User();
            //nom d'utilisateur au hasard commençant pas user-1234
            $username = $faker->numerify('user-####');
            $user->setUsername($username);
            // Création d'un mail au hasard
            $mail = $faker->email();
            $user->setUserMail($mail);
            $user->setRoles(['ROLE_USER']);
            //Transformation du nom en mot de passe
            #(pour tester)
            $pwdHash = $this->passwordHasher->hashPassword($user, $username);
            $user->setPassword($pwdHash);
            # On va activer 1 user sur 3
            $randActive = mt_rand(0,2);
            $user->setUserActive($randActive);
            $realName = $faker->name();
            $user->setUserRealName($realName);
            $manager->persist($user);
        }

            $manager->flush();
    }
}
