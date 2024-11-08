<?php

namespace App\Form;

use App\Entity\Video;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;



class VideoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('videoLink')
            // ->add('premium', CheckboxType::class, [
            //     'label' => 'Premium Video',
            //     'required' => false,
            // ])
            ->add('description')
            // ->add('createdAt', null, [
            //     'widget' => 'single_text',
            // ])
            // ->add('updateAt', null, [
            //     'widget' => 'single_text',
            // ])
                    // ->add('premium', CheckboxType::class, [
                    //     'label' => 'Is this video premium?',
                    //     'required' => false,
                    // ])
            ->add('save', SubmitType::class, [
                'label' => 'Save'
                ]) 
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Video::class,
        ]);
    }
}
