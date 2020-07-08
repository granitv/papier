<?php

namespace App\Form;

use App\Entity\User;

use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class AdminUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class,[ 'attr' => ['class' => 'form-control']])
            ->add('email', EmailType::class,[ 'attr' => ['class' => 'form-control']])
            ->add(
                'roles', ChoiceType::class, [
                    'choices' => ['user' => "ROLE_USER", 'admin' => "ROLE_ADMIN"],
                    'expanded' => true,
                    'multiple' => true,
                ]
            )

            ->add('edit', SubmitType::class,[ 'attr' => ['class' => 'btn btn-success my-2']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
