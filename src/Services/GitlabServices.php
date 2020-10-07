<?php


namespace App\Services;


use Doctrine\Persistence\ObjectManager;

class GitlabServices
{
    public function addTeam(ObjectManager $entityManager, $team ):int {
        $entityManager->persist($team);
        $entityManager->flush();
    }
}