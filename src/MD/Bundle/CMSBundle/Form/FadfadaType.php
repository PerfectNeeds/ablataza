<?php

namespace MD\Bundle\CMSBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use MD\Bundle\CMSBundle\Entity\Fadfada;

class FadfadaType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('text')
                ->add('maritalStatus', 'choice', array(
                    'choices' => array(
                        Fadfada::MARITAL_STATUS_MARRIED => 'متجوزه',
                        Fadfada::MARITAL_STATUS_ENGAGED => 'مخطوبه',
                        Fadfada::MARITAL_STATUS_FRIENDSHIP => 'مصاحبه',
                        Fadfada::MARITAL_STATUS_SINGEL => 'سينجل',
                        Fadfada::MARITAL_STATUS_OTHER => 'اخري',
                    ),
                ))
                ->add('publish', NULL, array('required' => FALSE))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'MD\Bundle\CMSBundle\Entity\Fadfada'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'md_bundle_cmsbundle_fadfada';
    }

}
