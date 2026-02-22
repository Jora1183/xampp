<?php
/**
 ------------------------------------------------------------------------
 SOLIDRES - Accommodation booking extension for Joomla
 ------------------------------------------------------------------------
 * @author    Solidres Team <contact@solidres.com>
 * @website   https://www.solidres.com
 * @copyright Copyright (C) 2013 Solidres. All Rights Reserved.
 * @license   GNU General Public License version 3, or later
 ------------------------------------------------------------------------
 */

\defined('_JEXEC') or die;

use Joomla\CMS\Dispatcher\ComponentDispatcherFactoryInterface;
use Joomla\CMS\Extension\ComponentInterface;
use Joomla\CMS\Extension\Service\Provider\ComponentDispatcherFactory;
use Joomla\CMS\Extension\Service\Provider\MVCFactory;
use Joomla\CMS\Extension\MVCComponent;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\Component\Router\RouterFactoryInterface;
use Joomla\CMS\Extension\Service\Provider\RouterFactory;
use Joomla\CMS\Dispatcher\LegacyComponentDispatcher;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

/**
 * The Solidres service provider.
 *
 * @since  3.4.1
 */
return new class () implements ServiceProviderInterface {
    /**
     * Registers the service provider with a DI container.
     *
     * @param   Container  $container  The DI container.
     *
     * @return  void
     *
     * @since   3.4.1
     */
    public function register(Container $container)
    {
        $container->registerServiceProvider(new MVCFactory('\\Solidres\\Component\\Solidres'));
        $container->registerServiceProvider(new RouterFactory('\\Solidres\\Component\\Solidres'));

        $container->set(
            ComponentInterface::class,
            function (Container $container) {
                $component = new MVCComponent($container->get(ComponentDispatcherFactoryInterface::class));
                $component->setMVCFactory($container->get(MVCFactoryInterface::class));

                return $component;
            }
        );
        
        $container->set(
            ComponentDispatcherFactoryInterface::class,
            function (Container $container) {
                return new class implements ComponentDispatcherFactoryInterface {
                    public function createDispatcher(\Joomla\CMS\Application\CMSApplicationInterface $application, ?\Joomla\Input\Input $input = null): \Joomla\CMS\Dispatcher\DispatcherInterface
                    {
                        return new LegacyComponentDispatcher($application, $input);
                    }
                };
            }
        );
    }
};
