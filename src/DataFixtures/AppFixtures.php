<?php


namespace App\DataFixtures;


use App\Entity\Category;
use App\Entity\Images;
use App\Entity\Message;
use App\Entity\Trick;
use App\Entity\User;
use App\Entity\Videos;
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


    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create();

        for($i = 1; $i <= 10; $i++){
            $category = new Category();
            $category->setLabel("Categorie N°".$i);
            $manager->persist($category);

            for($j = 1; $j <= 3; $j++){
                $user = new User();
                $trick = new Trick();
                $image = new Images();
                $video = new Videos();
                $message = new Message();

                $trick->setUser($user);
                $trick->addCategory($category);
                $trick->setNameFigure($faker->name);
                $trick->setSlug($this->slugger->slug($trick->getNameFigure()));
                $trick->setDescription($faker->realText());
                $trick->addImage($image);

                $user->setEmail($faker->email);
                $user->setPassword($this->hasher->hashPassword($user,'azerty123456'));
                $user->setUsername($faker->userName);
                $user->setPhoto('user_admin.jpeg');
                $user->setIsVerified(1);
                //Varier les roles
                $user->setRoles(["ROLE_USER"]);

                $message->setContent($faker->realText(50));
                $message->setUser($user);
                $message->setTrick($trick);

                $video->setTrick($trick);
                $image->setName('877546.jpg');
                $image->setTrick($trick);


                $manager->persist($message);
                $manager->persist($image);
                $manager->persist($video);
                $manager->persist($user);
                $manager->persist($trick);
            }
            $manager->flush();
        }
    }
}