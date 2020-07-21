<?php

namespace App\Form;

use App\Entity\Order;
use App\Entity\Typee;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('typee', EntityType::class,[ 'expanded' => true,
                'multiple' => false, 'class'=>Typee::class, 'choice_label'=>'name', 'attr' => ['class' => 'form-control form-check']])

            ->add('height',IntegerType::class,[ 'attr' => ['class' => 'form-control',  'placeholder' => 'cm']])
            ->add('width',IntegerType::class,[ 'attr' => ['class' => 'form-control',  'placeholder' => 'cm']])
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
            ->add('text',TextareaType::class,[ 'required'=>false, 'attr' => ['class' => 'form-control',  'placeholder' => 'Notes']])
            ->add('quantity',IntegerType::class,[ 'attr' => ['class' => 'form-control',  'placeholder' => 'number']])

            ->add('overlapping', ChoiceType::class, ['attr' => ['class' => 'form-control',  'placeholder' => 'number'],
                'choices'  => [
                    'Aucun ' => 0,
                    '4 cm' => 1,
                ],
                'expanded' => true,
                'multiple' => false,
            ])
            ->add('order', SubmitType::class ,[ 'attr' => ['class' => 'btn btn-success mt-2']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
        ]);
    }
}
