<?php
defined('_JEXEC') or die;

use Joomla\CMS\Extension\PluginInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Event\DispatcherInterface;
use GHSVS\Plugin\EditorsXtd\InsertTagsGhsvs\Extension\InsertTagsGhsvs;

return new class () implements ServiceProviderInterface
{
	public function register(Container $container)
	{
		$container->set(
			PluginInterface::class,
			function (Container $container) {
				$dispatcher = $container->get(DispatcherInterface::class);

				$plugin     = new InsertTagsGhsvs(
					$dispatcher,
					(array) PluginHelper::getPlugin('editors-xtd', 'inserttagsghsvs')
				);
				$plugin->setApplication(Factory::getApplication());

				return $plugin;
			}
		);
	}
};
