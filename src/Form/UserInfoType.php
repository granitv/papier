<?php

namespace App\Form;

use App\Entity\UserInfo;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserInfoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fullname', TextType::class,[ 'attr' => ['class' => 'form-control',  'placeholder' => 'Full Name']])
            ->add('country', TextType::class,[ 'attr' => ['class' => 'form-control',  'placeholder' => 'Country']])
            ->add('street', TextType::class,[ 'attr' => ['class' => 'form-control',  'placeholder' => 'Street']])
            ->add('postcode', TextType::class,[ 'attr' => ['class' => 'form-control',  'placeholder' => 'Postcode']])
            ->add('city', TextType::class,[ 'attr' => ['class' => 'form-control',  'placeholder' => 'City']])
            ->add('tel',IntegerType::class,[ 'attr' => ['class' => 'form-control',  'placeholder' => 'Tel']])
            ->add('note',TextareaType::class,[ 'attr' => ['class' => 'form-control',  'placeholder' => 'Notes']])
            ->add('save',SubmitType::class,['attr'=>['class'=>'btn btn-success my-2']])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserInfo::class,
        ]);
    }
}
