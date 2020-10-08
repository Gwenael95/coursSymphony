<?php

namespace App\Repository;

use App\Entity\Project;
use App\Entity\Team;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Project|null find($id, $lockMode = null, $lockVersion = null)
 * @method Project|null findOneBy(array $criteria, array $orderBy = null)
 * @method Project[]    findAll()
 * @method Project[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Project::class);
    }

    public function findAllProject()
    {
        return $this->findAll();
    }

    public function findProjectFromTeam(Team $team)
    {
        return $this->findOneByTeam($team);
    }

    public function findProjectByprojectName(string $projectName)
    {
        return $this->findOneByName($projectName);
    }
    public function findOneProjectByProjectId(string $id)
    {
        return $this->findOneByProjectId($id);
    }
}
