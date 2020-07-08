<?php

namespace App\Form;

use App\Entity\Coll;
use App\Entity\Image;
use App\Repository\ImageRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class AdminCollType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class,[ 'attr' => ['class' => 'form-control']])
            ->add('description', TextType::class,[ 'attr' => ['class' => 'form-control']])
            ->add('price', NumberType::class,[ 'attr' => ['class' => 'form-control']])
            ->add('image', EntityType::class,[ 'expanded' => true,
                'multiple' => true, 'class'=>Image::class, 'choice_label'=>'url', 'attr' => ['class' => 'form-check']])

            ->add('file_url',FileType::class,['mapped'=>false, 'required' =>false,
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => [
                            'application/pdf',
                            'application/x-pdf',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid PDF document',
                    ])
                ],
                'attr' => [ 'class' => 'form-control']])
            ->add('add', SubmitType::class,[ 'attr' => ['class' => 'btn btn-success my-2']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Coll::class,
        ]);
    }
}
