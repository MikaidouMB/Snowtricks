<?php


namespace App\DataFixtures;


use App\Entity\Trick;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{

    public function load(ObjectManager $manager)
    {
        for ($index = 1; $index < 10; $index ++){
            $manager->persist(
                (new Trick())
                ->setName(sprintf("Foo %d"))
            );

        }
        $manager->flush();
    }
}