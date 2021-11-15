<?php

namespace MD\Bundle\CMSBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ContentType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('brief', 'textarea', array('required' => false,
                    'attr' => array('class' => 'tinymce', 'data-theme' => 'sample')))
                ->add('Description', 'textarea', array('required' => false,
                    'attr' => array('class' => 'tinymce', 'data-theme' => 'sample')))
        ;
    }

    public function getName() {
        return '';
    }

}
