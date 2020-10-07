<?php


namespace App\Form\Type;


use App\Entity\Team;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class TeamSelectType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options){
        $builder->add("name", EntityType::class, ["class"=>Team::class, "choice_label" => 'name', 'choice_value' => 'name',"multiple"=>true]);
    }
}