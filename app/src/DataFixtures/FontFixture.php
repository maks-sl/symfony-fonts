<?php

namespace App\DataFixtures;

use App\Model\Font\Entity\Font;
use App\Model\Font\Entity\Id;
use App\Model\Font\Entity\Language;
use App\Model\Font\Entity\License;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class FontFixture extends Fixture
{

    public function load(ObjectManager $manager): void
    {
        $manager->persist(
            new Font(
                $id = Id::next(),
                $date = new \DateTimeImmutable(),
                $slug = 'yolk',
                $name = 'Yolk',
                $author = 'Yolk Author',
                License::free(),
                [Language::cyrillic(), Language::latin()]
            )
        );

        $manager->persist(
            new Font(
                $id = Id::next(),
                $date = new \DateTimeImmutable(),
                $slug = 'union',
                $name = 'Union',
                $author = 'Union Author',
                License::paid(),
                [Language::latin()]
            )
        );

        $manager->flush();
    }

}
