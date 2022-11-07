<?php

namespace App\Form;

use App\Entity\Produits;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
class ProduitFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom_produit',TextType::class,array('label'=>false,'attr'=>array('placeholder'=>'Saisir le nom de Produit')))
            ->add('description')
            ->add('prix')
            ->add('qte')
            ->add('reference')
            // choiceType <==> <select></select>
            // choices pour ajouter les différents options de select
            // Cat1 c'est la clé c'est l'affichage dans la page twig
            // 1 c'est la valeur envoyé vers la table de bD après le click sur le bouton de type submit
            ->add('categorie',choiceType::class,array('choices'=>array('Cat1'=>'1','Cat2'=>'2')))
            ->add('photo',fileType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Produits::class,
        ]);
    }
}
