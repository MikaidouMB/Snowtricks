<?php


namespace App\DataFixtures;


use App\Entity\Trick;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{

    public function load(ObjectManager $manager)
    {
       $figures = [
           1 => [
               'nameFigure' => 'La 1ere figure fixture',
               'description' => 'la 1ere description de fixture figure'
           ],
           2 => [
               'nameFigure' => 'La 2e figure fixture',
               'description' => 'la 2ere description de fixture figure'
           ],
            3 => [
            'nameFigure' => 'La 3e figure fixture',
            'description' => 'la 3e description de fixture figure'
            ],
           4 => [
               'nameFigure' => 'La 4e figure fixture',
               'description' => 'la 4e description de fixture figure'
           ]
       ];

       foreach ($figures as $key => $value){
           $figure = new Trick();
           $figure->setNameFigure($value['nameFigure']);
           $figure->setDescription($value['description']);
           $manager->persist($figure);
       }
        $manager->flush();
    }
}