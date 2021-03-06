<?php

namespace Pumukit\NewAdminBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class PumukitNewAdminExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('pumukit_new_admin.disable_broadcast_creation', $config['disable_broadcast_creation']);
        $container->setParameter('pumukit_new_admin.licenses', $config['licenses']);
        $container->setParameter('pumukit_new_admin.multimedia_object_label', $config['multimedia_object_label']);
        $container->setParameter('pumukit_new_admin.advance_live_event', $config['advance_live_event']);
        $container->setParameter('pumukit_new_admin.show_menu_place_and_precinct', $config['show_menu_place_and_precinct']);
        $container->setParameter('pumukit_new_admin.advance_live_event_create_default_pic', $config['advance_live_event_create_default_pic']);
        $container->setParameter('pumukit_new_admin.advance_live_event_autocomplete_series', $config['advance_live_event_autocomplete_series']);
        $container->setParameter('pumukit_new_admin.advance_live_event_create_serie_pic', $config['advance_live_event_create_serie_pic']);
        $container->setParameter('liveevent_contact_and_share', $config['liveevent_contact_and_share']);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        if ($container->hasParameter('pumukit2.naked_backoffice_domain')) {
            $arguments = array(
                '%pumukit2.naked_backoffice_domain%',
                '%pumukit2.naked_backoffice_background%',
            );

            if ($container->hasParameter('pumukit2.naked_backoffice_color')) {
                $arguments[] = '%pumukit2.naked_backoffice_color%';
            }

            if ($container->hasParameter('pumukit2.naked_custom_css_url')) {
                $arguments[] = '%pumukit2.naked_custom_css_url%';
            }

            $definition = new Definition('Pumukit\NewAdminBundle\EventListener\NakedBackofficeListener', $arguments);
            $definition->addTag('kernel.event_listener', array('event' => 'kernel.controller', 'method' => 'onKernelController'));
            $container->setDefinition('pumukitnewadmin.nakedbackoffice', $definition);
        }
    }
}
