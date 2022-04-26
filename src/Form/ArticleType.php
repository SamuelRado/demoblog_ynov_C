<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // l'objet $builder permet de construire un formulaire
        // add() permet d'ajouter un champ au formulaire
        $builder
            ->add('title')
            ->add('content')
            ->add('imageFile', FileType::class, ['required' => false])
            // ->add('createdAt')   le champ createdAt doit être rempli automatiquement lors de l'insertion d'un article
            ->add('category', EntityType::class, [  // j'indique que le champ category est une entity
                'class' => Category::class, // je précise quelle entity
                'choice_label' => 'title'
            ]) 
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'attr' => [
                'novalidate' => 'novalidate' // désactive la validation html
            ],
            'data_class' => Article::class,
        ]);
    }
}
