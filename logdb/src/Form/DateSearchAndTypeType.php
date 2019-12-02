<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DateSearchAndTypeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('startDate', DateType::class, [
                'widget' => 'choice',
                'html5' => false,
                'attr' => ['class' => 'js-datepicker'],
            ])
            ->add('endDate', DateType::class, [
                'widget' => 'choice',
                'html5' => false,
                'attr' => ['class' => 'js-datepicker'],
            ])
            ->add('logType',ChoiceType::class, [
                'choices' => ['Delete' => 'delete', 'Received' => 'Received','Replicate' => 'replicate','Served' => 'Served', 'Receiving' => 'Receiving'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
