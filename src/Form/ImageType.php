<?php

namespace App\Form;

use App\Entity\Coll;
use App\Entity\Image;
use App\Repository\CollRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('url',FileType::class,['mapped'=>false, 'required' =>false,
                'attr' => [ 'class' => 'form-control']])

            ->add('coll', EntityType::class, array(
                'class' => Coll::class,
                'choice_label' => 'name',
                'required' => true,
                'attr' => ['class' => 'form-control col-md-4']
            ))


            ->add('addimage', SubmitType::class ,[ 'attr' => ['class' => 'btn btn-warning my-2']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Image::class,
        ]);
    }
}
