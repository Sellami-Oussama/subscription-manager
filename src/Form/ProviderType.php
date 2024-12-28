<?php

namespace App\Form;

use App\Entity\Provider;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Url;

class ProviderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Enter provider name'
                ],
                'label' => 'Name'
            ])
            ->add('website', UrlType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Enter website URL'
                ],
                'required' => false,
                'label' => 'Website',
                'constraints' => [
                    new Url([
                        'message' => 'Please enter a valid URL'
                    ])
                ]
            ])
            ->add('contactEmail', EmailType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Enter contact email'
                ],
                'label' => 'Contact Email',
                'constraints' => [
                    new Email([
                        'message' => 'Please enter a valid email address'
                    ])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Provider::class,
        ]);
    }
}