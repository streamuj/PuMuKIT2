<?php

namespace Pumukit\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class DirectType extends AbstractType
{

  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
      ->add('name')
      ->add('description')
      ->add('url')
      ->add('direct_type_id', 'number')
      ->add('resolution_width', 'number')
      ->add('resolution_height', 'number')
      ->add('qualities')
      ->add('ip_source')
      ->add('source_name')
      ->add('index_play', 'checkbox', array('required'=>false))
      ->add('broadcasting', 'checkbox', array('required'=>false))
      ->add('save', 'submit');
  }
  

  public function setDefaultOptions(OptionsResolverInterface $resolver)
  {
    $resolver->setDefaults(array(
        'data_class' => 'Pumukit\DirectBundle\Document\Direct'
    ));
  }

  public function getName()
  {
    return 'pumukitadmin_direct';
  }
}