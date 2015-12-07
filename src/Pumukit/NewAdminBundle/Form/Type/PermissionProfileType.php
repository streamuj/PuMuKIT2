<?php

namespace Pumukit\NewAdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Pumukit\SchemaBundle\Document\PermissionProfile;
use Symfony\Component\Translation\TranslatorInterface;

class PermissionProfileType extends AbstractType
{
    private $translator;
    private $locale;

    public function __construct(TranslatorInterface $translator, $locale='en')
    {
        $this->translator = $translator;
        $this->locale = $locale;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('default', 'checkbox',
                  array('required' => false,
                        'label' => $this->translator->trans('Default', array(), null, $this->locale)))
            ->add('name', 'text',
                  array('label' => $this->translator->trans('Name', array(), null, $this->locale)))
            ->add('scope', 'choice',
                  array('choices' => PermissionProfile::$scopeDescription,
                        'required' => false,
                        'label' => $this->translator->trans('Scope', array(), null, $this->locale)))
;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                                     'data_class' => 'Pumukit\SchemaBundle\Document\PermissionProfile',
                                     ));
    }

    public function getName()
    {
        return 'pumukitnewadmin_permissionprofile';
    }
}