<?php

namespace MD\Bundle\CMSBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RecipeType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('title')
                ->add('youtubeUrl', NULL, array('required' => FALSE))
                ->add('peopleNo')
                ->add('preparationTime', NULL, array('label' => "Preparation Time (minutes)"))
                ->add('cookingTime', NULL, array('label' => "Cooking Time (minutes)"))
                ->add('publish', NULL, array('required' => FALSE))
                ->add('draft', NULL, array('required' => FALSE))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'MD\Bundle\CMSBundle\Entity\Recipe'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'md_bundle_cmsbundle_recipe';
    }

}
