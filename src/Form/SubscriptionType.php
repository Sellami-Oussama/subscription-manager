<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Provider;
use App\Entity\Subscription;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SubscriptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'attr' => ['class' => 'form-control'],
                'label' => 'Category',
                'placeholder' => 'Select a category'
            ])
            ->add('provider', EntityType::class, [
                'class' => Provider::class,
                'choice_label' => 'name',
                'attr' => ['class' => 'form-control'],
                'label' => 'Provider',
                'placeholder' => 'Select a provider'
            ])
            ->add('name', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Enter subscription name'
                ],
                'label' => 'Name'
            ])
            ->add('price', MoneyType::class, [
                'currency' => 'EUR',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Enter price'
                ],
                'label' => 'Price'
            ])
            ->add('billingCycle', ChoiceType::class, [
                'choices' => [
                    'Monthly' => Subscription::BILLING_CYCLE_MONTHLY,
                    'Quarterly' => Subscription::BILLING_CYCLE_QUARTERLY,
                    'Yearly' => Subscription::BILLING_CYCLE_YEARLY,
                ],
                'attr' => ['class' => 'form-control'],
                'label' => 'Billing Cycle'
            ])
            ->add('renewalDate', DateType::class, [
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control'],
                'label' => 'Renewal Date'
            ])
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'Active' => Subscription::STATUS_ACTIVE,
                    'Cancelled' => Subscription::STATUS_CANCELLED,
                    'Expired' => Subscription::STATUS_EXPIRED,
                ],
                'attr' => ['class' => 'form-control'],
                'label' => 'Status'
            ])
            ->add('notes', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 3,
                    'placeholder' => 'Enter notes'
                ],
                'required' => false,
                'label' => 'Notes'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Subscription::class,
        ]);
    }
}