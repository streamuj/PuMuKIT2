<?php

namespace Pumukit\NewAdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;

class UNESCOBasicType extends AbstractType
{
    private $translator;
    private $locale;

    public function __construct(TranslatorInterface $translator, $locale = 'en')
    {
        $this->translator = $translator;
        $this->locale = $locale;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
                'id',
                TextType::class,
                array(
                    'required' => false,
                    'attr' => array(
                        'style' => 'resize:vertical;',
                        'aria-label' => $this->translator->trans('ID', array(), null, $this->locale),
                    ),
                    'label' => $this->translator->trans('ID', array(), null, $this->locale),
                )
            )->add(
                'seriesTitle',
                TextType::class,
                array(
                    'required' => false,
                    'attr' => array(
                        'aria-label' => $this->translator->trans(
                            'Series Title',
                            array(),
                            null,
                            $this->locale
                        ),
                    ),
                    'label' => $this->translator->trans('Series Title', array(), null, $this->locale),
                )
            )->add(
                'keyword',
                TextType::class,
                array(
                    'required' => false,
                    'attr' => array('aria-label' => $this->translator->trans('Keyword', array(), null, $this->locale)),
                    'label' => $this->translator->trans('Keyword', array(), null, $this->locale),
                )
            );
    }

    public function getName()
    {
        return 'pumukitnewadmin_unesco_basicform';
    }
}
