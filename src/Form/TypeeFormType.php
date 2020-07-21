<?php

namespace App\Form;

use App\Entity\Typee;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TypeeFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class,[ 'attr' => ['class' => 'form-control']])
            ->add('description', TextType::class,[ 'attr' => ['class' => 'form-control']])
            ->add('price', NumberType::class,[ 'attr' => ['class' => 'form-control']])
            ->add(
                'active', ChoiceType::class, [
                    'choices' => ['Active' => 1, 'Deactive' => 0],
                    'expanded' => true,
                    'multiple' => false,
                ]
            )
            ->add('add', SubmitType::class ,[ 'attr' => ['class' => 'btn btn-success mt-2']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Typee::class,
        ]);
    }
}
