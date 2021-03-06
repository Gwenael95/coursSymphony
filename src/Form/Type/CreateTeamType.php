<?php


namespace App\Form\Type;


use App\Entity\Team;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class CreateTeamType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add("teamName", TextType::class, ["label" => 'Nom de l\'équipe', 'attr' => ['class' => 'form-control form-control-lg'],]);
    }

}