<?php

namespace MD\Bundle\CMSBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BannerType extends AbstractType {

    public $placmentData = 1;

    public function buildForm(FormBuilderInterface $builder, array $options) {

        $builder
                ->add('title', null, array('label' => 'Banner Title'))
                ->add('text', 'textarea', array('label' => 'Banner Text', 'required' => false))
                ->add('placement', 'choice', array(
                    'choices' => array(
                        1 => 'Home Page Banner "1920px * 450px"',
                        2 => 'Recipe Banner "468px * 60px"',
                        3 => 'Article Banner "300px * 250px"',
                        4 => 'sidebar Banner "300px * 100px"',
                    ),
                    'data' => $this->placmentData
                ))
                ->add('url', null, array('label' => 'Banner Url'))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'MD\Bundle\CMSBundle\Entity\Banner'
        ));
    }

    public function getName() {
        return 'md_bundle_cmsbundle_bannertype';
    }

}
