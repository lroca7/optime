<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use App\Entity\Category;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\NotBlank;

use Symfony\Component\Validator\Constraints\Length;

class ProductFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('code', TextType::class, [
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Length(['min' => 2,
                    'max' => 10]),
                    new Assert\Regex(array(
                        'pattern' => '/^[a-zA-Z0-9]+$/',
                        'message' => 'The code is not valid.'
                        )
                    )
                )
            ])
            ->add('name', TextType::class, [
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Length(['min' => 4])
                )
            ])
            ->add('description', TextType::class, [
                'constraints' => array(
                    new Assert\NotBlank()
                )
            ])
            ->add('brand', TextType::class, [
                'constraints' => array(
                    new Assert\NotBlank()
                )
            ])
            ->add('price', IntegerType::class, [
                'constraints' => array(
                    new Assert\NotBlank(), 
                    new Assert\Regex(array(
                        'pattern' => '/^[1-9][0-9]\d*$/',
                        'message' => 'The prices is not valid.'
                        )
                    )
                )
            ])
        ;

        $builder->add('category', EntityType::class, [
            'class' => Category::class,
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('c')
                    ->orderBy('c.name', 'ASC');
            },
            'choice_label' => 'name',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
