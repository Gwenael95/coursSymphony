<?php


namespace App\Form\Type;


use App\Entity\Team;
use App\Repository\TeamRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class teamType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options){
        $builder->add("name", EntityType::class, ["class"=>Team::class, "choice_label" => 'name']);
        //attention, au submit ne fonctionne pas , doit donner en child un nom d'entity

    }

}