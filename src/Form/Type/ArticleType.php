<?php


namespace App\Form\Type;


use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options){
        $builder->add("titre", TextType::class, ["label"=>"titre de l'article"]);
        $builder->add("contenu", TextareaType::class, ["label"=>"contenu de l'article"]);
        $builder->add("user", EntityType::class, ["class"=>User::class, "choice_label" => 'nom']);

    }
}