<?php

namespace App\Form;

use App\Entity\Coll;
use App\Entity\Order;
use App\Entity\Typee;
use Doctrine\DBAL\Types\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;




class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('typee', EntityType::class,[
                'required' => true, 'class'=>Typee::class, 'choice_label'=>'name', 'attr' => ['class' => 'form-control']])
            ->add('height',IntegerType::class,[ 'attr' => ['class' => 'form-control',  'placeholder' => 'cm']])
            ->add('width',IntegerType::class,[ 'attr' => ['class' => 'form-control',  'placeholder' => 'cm']])
            ->add('quantity',IntegerType::class,[ 'attr' => ['class' => 'form-control',  'placeholder' => 'number']])
            ->add('overlapping', ChoiceType::class, ['attr' => ['class' => 'form-control',  'placeholder' => 'number'],
                'choices'  => [
                    'Aucun ' => 0,
                    '4 cm' => 1,
                ],
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
