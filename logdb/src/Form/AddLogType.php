<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddLogType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('insertDate', DateType::class, [
                'widget' => 'choice',
                'html5' => true,
                'attr' => ['class' => 'js-datepicker'],
            ])
            ->add('sourceIps', TextType::class, [
                'required' => true
            ])
            ->add('destinationIps', CollectionType::class, [
                'entry_type'   => TextType::class,
                'allow_add'    => true,
                'allow_delete' => true,
                'prototype'    => true,
                'required'     => true,
                'attr'         => [
                    'class' => "ip_address2",
                    'id' => 'ip_address2'
                ]
            ])
            ->add('logType',ChoiceType::class, [
                'choices' => ['Access Log' => "Access", 'HDFS Log' => "HDFS"],
            ])
            ->add('type', TextType::class, [
                'required' => false
            ])
            ->add('size', IntegerType::class, [
                'required' => false,
            ])
            ->add('method', ChoiceType::class, [
                'required' => false,
                'choices' => [
                    'GET' => "GET",
                    'PUT' => "PUT",
                    'POST' => "POST",
                    'DELETE' => "DELETE",
                    'TRACE' => "TRACE",
                    'UPDATE' => "UPDATE",
                    'OPTION' => "OPTION",
                    'ENTITY' => "ENTITY"]
            ])
            ->add('requested_resource', TextType::class, [
                'required' => false,
            ])
            ->add('response_status', IntegerType::class, [
                'required' => false
            ])
            ->add('response_size', IntegerType::class, [
                'required' => false,
            ])
            ->add('referer', TextType::class, [
                'required' => false,
            ])
            ->add('user_agent', TextType::class, [
                'required' => false,
            ])
            ->add('blockIds', CollectionType::class, [
                'entry_type'   => IntegerType::class,
                'allow_add'    => true,
                'allow_delete' => true,
                'prototype'    => true,
                'required'     => true
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
