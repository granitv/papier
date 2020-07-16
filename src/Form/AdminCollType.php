<?php

namespace App\Form;

use App\Entity\CategoryColl;
use App\Entity\Coll;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
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
            ->add('Categorycolls', EntityType::class,[ 'expanded' => true,
                'multiple' => true, 'class'=>CategoryColl::class, 'choice_label'=>'name', 'attr' => ['class' => 'form-check']])
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
