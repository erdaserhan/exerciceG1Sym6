<?php

namespace App\DataFixtures;




use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
# On va hacher les mots de passe
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
# Chargement de Faker et création d'un alias nommé Faker
use Faker\Factory as Faker;
# chargement de slugify
use Cocur\Slugify\Slugify;
# on va récupérer notre entité User
use App\Entity\User;
# on va récupérer notre entité Post
use App\Entity\Post;
# on va récupérer notre entité Section
use App\Entity\Section;
# on va récupérer notre entité Tag
use App\Entity\Tag;
# on va récupérer notre entité Comment
use App\Entity\Comment;

class AppFixtures extends Fixture
{
    # attribut contenant le hacher de mot de passe
    private UserPasswordHasherInterface $passwordHasher;


    # constructeur qui remplit les attributs
    public function __construct(
        UserPasswordHasherInterface $passwordHasher,

    )
    {
        # hache le mot de passe
        $this->passwordHasher = $passwordHasher;
    }

    # constructeur qui remplit les attributs
    public function load(ObjectManager $manager): void
    {

        ###
        # GESTION de USER
        ###
        // Création de Faker
        $faker = Faker::create('fr_FR');
        // Création du slugify
        $slugify = new Slugify();
        ###
        # Instanciation d'un User Admin
        #
        $user = new User();
        $user->setUsername('admin');
        $user->setUserMail('admin@gmail.com');
        $user->setRoles(['ROLE_ADMIN','ROLE_REDAC','ROLE_MODERATOR']);
        # hachage du mot de passe
        $pwdHash = $this->passwordHasher->hashPassword($user, 'admin');
        # insertion du mot de passe haché
        $user->setPassword($pwdHash);
        $user->setUserActive(true);
        $user->setUserRealName('The Admin !');

        // création/ update d'un tableau contenant
        // les User qui peuvent écrire un article
        $users[] = $user;

        # Utilisation du $manager pour mettre le
        # User en mémoire
        $manager->persist($user);

        ###
        # Instanciation de 5 Rédacteurs
        #
        for($i = 1; $i <= 5; $i++){
            $user = new User();
            $user->setUsername('redac'.$i);
            $user->setUserMail('redac'.$i.'@gmail.com');
            $user->setRoles(['ROLE_REDAC']);
            $pwdHash = $this->passwordHasher->hashPassword($user, 'redac'.$i);
            $user->setPassword($pwdHash);
            $user->setUserActive(true);
            $user->setUserRealName('The Redac '.$i.' !');

            // création/ update d'un tableau contenant
            // les User qui peuvent écrire un article
            $users[] = $user;

            # Utilisation du $manager pour mettre le
            # User en mémoire
            $manager->persist($user);
        }

        ###
        # Instanciation de 3 modérateurs
        #
        for($i = 1; $i <= 3; $i++){
            $user = new User();
            $user->setUsername('modo'.$i);
            $user->setUserMail('modo'.$i.'@gmail.com');
            $user->setRoles(['ROLE_MODERATOR']);
            $pwdHash = $this->passwordHasher->hashPassword($user, 'modo'.$i);
            $user->setPassword($pwdHash);
            $user->setUserActive(true);
            $user->setUserRealName('The Moderator '.$i.' !');

            // création/ update d'un tableau contenant
            // les User qui peuvent écrire un article
            // ou un commentaire
            $users[] = $user;

            # Utilisation du $manager pour mettre le
            # User en mémoire
            $manager->persist($user);
        }


        ###
        # Instanciation entre 20 et 40 User sans rôles
        # en utilisant Faker
        #
        $hasard = mt_rand(20,40);
        for($i = 1; $i <= $hasard; $i++){
            $user = new User();
            # nom d'utilisateur au hasard commençant par user-1234
            $username = $faker->numerify('user-####');
            $user->setUsername($username);
            # création d'un mail au hasard
            $mail = $faker->email();
            $user->setUserMail($mail);
            $user->setRoles(['ROLE_USER']);
            # transformation du nom en mot de passe
            # (pour tester)
            $pwdHash = $this->passwordHasher->hashPassword($user, $username);
            $user->setPassword($pwdHash);
            # on va activer 1 user sur 3
            $randActive = mt_rand(0,2);
            $user->setUserActive($randActive);
            # Création d'un 'vrai' nom en français
            $realName = $faker->name();
            $user->setUserRealName($realName);
            // on garde les utilisateurs pour les commentaires
            $usersComment[] = $user;

            $manager->persist($user);

        }

        ###
        # GESTION de POST
        ###
        for($i = 1; $i <= 100; $i++){
            $post = new Post();
            // on prend un auteur au hasard
            $user = array_rand($users);
            $post->setUser($users[$user]);
            // titre entre 20 et 150 caractères
            $title = $faker->realTextBetween(20,150);
            $post->setPostTitle($title);
            // texte entre 3 et 6 paragraphes
            $post->setPostText($faker->paragraphs(mt_rand(3,6), true));
            // on va remonter dans le passé entre 30 et 60 jours
            $day = mt_rand(30,60);
            $post->setPostDateCreated(new DateTime("now -$day day"));
            // on va publier 3 articles sur 4 (+-) 1,2,3 => true 4 => false
            $published = mt_rand(1,4) < 4;
            $post->setPostIsPublished($published);
            if($published){
                // on va remonter dans le passé entre 5 et 15 jours
                $day = mt_rand(5,15);
                $post->setPostDatePublished(new DateTime("now -$day day"));
            }
            // on garde les postes
            $posts[] = $post;

            $manager->persist($post);

        }
        ###
        # GESTION de Section
        ###

        for($i = 1; $i <= 5; $i++){
            $section = new Section();
            $section->setSectionTitle($faker->realTextBetween(8,18));
            $section->setSectionDescription($faker->realTextBetween(100,400));
            $postRandom = array_rand($posts, mt_rand(2,count($posts)));
            foreach ($postRandom as $post){
                $section->addPost($posts[$post]);
            }


            $manager->persist($section);
        }

        ###
        # GESTION de Tag
        ###
        $nbTags = mt_rand(20,35);
        for($i = 1; $i <= $nbTags; $i++){
            $tag = new Tag();
            // création du nom
            $name = $faker->words(mt_rand(2,3),true);
            $tag->setTagName($name);
            $tag->setTagSlug($slugify->slugify($name));
            $postRandom = array_rand($posts, mt_rand(2,round(count($posts)/3)));
            foreach ($postRandom as $post){
                $tag->addPost($posts[$post]);
            }


            $manager->persist($tag);
        }
        ###
        # GESTION des commentaires
        ###
        $nbComments = mt_rand(150,400);
        for($i = 1; $i <= $nbComments; $i++){
            $comment = new Comment();
            $comment->setCommentText($faker->realTextBetween(100,500));
            // on va récupérer tous les auteurs qui peuvent écrire un commentaire
            $userAll = array_merge($usersComment,$users);
            // un commentaire, un utilisateur
            $userComment = array_rand($userAll);
            $comment->setUser($userAll[$userComment]);
            // un commentaire, un article
            $articleComment = array_rand($posts);
            $comment->setPost($posts[$articleComment]);
            // écrit entre 1 et 4 jours
            $day = mt_rand(1,4);
            $comment->setCommentDate(new DateTime("now -$day day"));
            // 3 sur 4 sont validés
            $validate = mt_rand(1,4)<4;
            $comment->setCommentVisible($validate);

            $manager->persist($comment);
        }

        # envoie à la base de donnée (commit)
        $manager->flush();
    }
}
