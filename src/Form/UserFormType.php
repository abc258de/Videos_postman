<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Vich\UploaderBundle\Form\Type\VichImageType;

class UserFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('firstname',TextType::class, [
            'label' => 'userForm.firstname' 
        ])
        ->add('lastname', TextType::class, [
            'label' => 'userForm.lastname'
        ])
        ->add('imageFile', VichImageType::class, [
            'required' => false,
            'allow_delete' => true,
            'delete_label' => 'Delete profile image',
            'download_uri' => true,

            // 'image_uri' => function($object, $property) use ($options) {
            //     // Check if the entity has an image
            //     if ($object->getImageFile()) {
            //         return $object->getImageUri(); // Your method to get the image URI
            //     } else {
            //         return $options['default_image_uri']; // Return default image URI
            //     }
            // }, NOT NEEDED AS YOU MAY CHANGE AT ORM WITH ONE SINGLE LINE

            'image_uri' => true,
            'asset_helper' => true,
            'imagine_pattern' => 'avatar_thumbnail'
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}