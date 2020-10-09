<?php


namespace App\Form\Type;


use App\Entity\Team;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TeamSelectUniqueType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options){
        $builder->add("teamName", EntityType::class,
            ["class"=>Team::class, "choice_label" => 'teamName', 'choice_value' => 'teamName', "data_class" => null, 'attr' => ['class' => 'custom-select'] ]);
    }

}