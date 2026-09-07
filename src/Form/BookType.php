<?php

namespace App\Form;

use App\Entity\Author;
use App\Entity\Book;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class BookType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, ['label' => 'Название книги'])
            ->add('releaseYear', IntegerType::class, ['label' => 'Год выпуска'])
            ->add('description', TextareaType::class, ['label' => 'Описание', 'required' => false])
            ->add('isbn', TextType::class, ['label' => 'ISBN'])
            ->add('authors', EntityType::class, [
                'class' => Author::class,
                'choice_label' => 'fullName',
                'multiple' => true,
                'expanded' => true,
                'label' => 'Авторы'
            ])
            ->add('coverImageFile', FileType::class, [
                'label' => 'Обложка книги',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File(
                        maxSize: '5M',
                        mimeTypes: ['image/jpeg', 'image/png', 'image/webp'],
                        mimeTypesMessage: 'Загрузите изображение формата JPG, PNG или WEBP'
                    )
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Book::class]);
    }
}
