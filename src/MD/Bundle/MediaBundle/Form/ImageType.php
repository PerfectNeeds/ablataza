<?php

namespace MD\Bundle\MediaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ImageType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->
                add('files', 'file', array(
                    "required" => FALSE,
                    "attr" => array(
                        "multiple" => "multiple",
                        "accept" => "image/*",
                    )
                ))
                ->getForm();
    }

    public function getName() {
        return '';
    }

}
