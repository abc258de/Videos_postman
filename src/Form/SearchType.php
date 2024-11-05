<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType; 
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Model\SearchData;

class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('query', TextType::class, [
                'attr' => [
                    'placeholder' => "Recherche des videos ..."
                ]
            ]);
    }

    // function pas obligatoire ... masi ça ne marchera pas sans elle ...
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
            'data_class' => SearchData::class,
            'method' => 'GET',
            'csrf_protection' => false
        ]); 
    }
}