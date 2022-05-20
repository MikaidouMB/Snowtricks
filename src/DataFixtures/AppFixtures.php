<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Message;
use App\Entity\Trick;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{
    /**
     * @var \Symfony\Component\String\Slugger\SluggerInterface
     */
    private SluggerInterface $slugger;
    private UserPasswordHasherInterface $hasher;

    public function __construct(SluggerInterface $slugger, UserPasswordHasherInterface $hasher)
    {
       $this->slugger =$slugger;
       $this->hasher = $hasher;
    }

    /**
     * @throws \Exception
     */
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create();
        $arrayCategories = [
            "kicker",
            "Regular",
            "Goofy",
            "Nose",
            "Tail",
            "Ollie",
            "kick",
            "hip",
            "Big air",
            "quarter"
        ];
        foreach ($arrayCategories as $key => $value){
            $category = new Category();

            $category->setLabel($value);
            $manager->persist($category);

            for($j = 1; $j <= 2; $j++){
                $user = new User();
                $trick = new Trick();
                $message = new Message();

                $trick->setUser($user);
                $trick->addCategory($category);
                $trick->setNameFigure($faker->name);
                $trick->setSlug($this->slugger->slug($trick->getNameFigure()));
                $trick->setDescription($faker->realText());

                $user->setEmail($faker->email);
                $user->setPassword($this->hasher->hashPassword($user,'azerty123456'));
                $user->setUsername($faker->userName);
                $user->setIsVerified(1);

                $array = array(
                    "ROLE_USER" => 1,
                    "ROLE_ADMIN" => 2,
                    "ROLE_MODO" => 3
                );

                $user->setRoles((array)array_rand($array));
                $manager->persist($user);

                $statusMessage = random_int(0, 1);
                $message->setIsValidated($statusMessage);
                $message->setContent($faker->realText(50));
                $message->setUser($user);
                $message->setTrick($trick);
                $manager->persist($message);
                $manager->persist($trick);
            }
            $manager->flush();
        }
    }
}