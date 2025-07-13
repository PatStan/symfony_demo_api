<?php

namespace App\Form;

use App\Entity\Subscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SubscriberType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('emailAddress', EmailType::class, [
                'required' => true,
            ])
            ->add('firstName', TextType::class, [
                'required' => false,
            ])
            ->add('lastName', TextType::class, [
                'required' => false,
            ])
            ->add('dateOfBirth', DateType::class, [
                'widget' => 'single_text',
                'required' => true,
            ])
            ->add('marketingConsent', CheckboxType::class, [
                'required' => false,
                'label' => 'I agree to receive marketing communications',
            ]);

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($options) {
            $form = $event->getForm();
            $data = $event->getData();

            if (isset($data['marketingConsent']) && $data['marketingConsent']) {
                $form->add('lists', ChoiceType::class, [
                    'choices' => array_flip($options['lists']),
                    'multiple' => true,
                    'expanded' => true,
                    'mapped' => false,
                    'required' => false,
                    'label' => 'Select marketing lists',
                    'choice_label' => fn($choice, $key, $value) => $choice,
                ]);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Subscriber::class,
            'lists' => [],
            'marketing_consent' => false,
        ]);
    }
}
