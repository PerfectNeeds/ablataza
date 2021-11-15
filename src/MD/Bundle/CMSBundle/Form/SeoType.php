<?php

namespace MD\Bundle\CMSBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SeoType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('title', null, array('label' => 'HTML Title', 'required' => false))
                ->add('metaTag', 'textarea', array('label' => 'HTML Meta Tag', 'required' => false))
                ->add('slug', null, array('label' => 'HTML Slug', 'attr' => array('class' => 'slug')))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'MD\Bundle\CMSBundle\Entity\Seo'
        ));
    }

    public function getName() {
        return 'seoType';
    }

}
