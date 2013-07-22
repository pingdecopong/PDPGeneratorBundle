<?php

namespace pingdecopong\PDPGeneratorBundle\Lib;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SubFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('buttonAction', 'hidden')
            ->add('returnAddress', 'hidden')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'pingdecopong\PDPGeneratorBundle\Lib\SubFormModel'
        ));
    }

    public function getName()
    {
        return 'subform';
    }
}
