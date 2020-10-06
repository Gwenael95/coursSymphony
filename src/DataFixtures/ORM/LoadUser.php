<?php


namespace App\DataFixtures\ORM;


use App\Entity\User;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Yaml\Yaml;

class LoadUser extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function load(ObjectManager $manager)
    {

        $template= Yaml::parse(file_get_contents(__DIR__."/../Files/User.yml"));
        foreach ($template["user"] as $row){
            $this->loadUser($manager, $row);
        }
        $manager->flush();

    }

    public function loadUser(ObjectManager $manager, $row)
    {
        $user = new User();
        $user->setEmail($row["email"]);
        $user->setRoles($row["role"]);
        $user->setNom($row["nom"]);
        $user->setPrenom($row["prenom"]);
        $manager->persist($user);
    }

    public function getOrder()
    {
        return 0;
    }


}