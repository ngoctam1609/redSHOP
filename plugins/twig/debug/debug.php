<?php
/**
 * @package     Redshop.Library
 * @subpackage  Plugin.Debug
 *
 * @copyright   Copyright (C) 2008 - 2019 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') || die;

JLoader::import('redshop.library');

use Redshop\Plugin\BaseTwigPlugin;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\LoaderInterface;

/**
 * Plugin to activate debug & dump extension in twig.
 *
 * @since  1.0.0
 */
class PlgTwigDebug extends BaseTwigPlugin
{
	/**
	 * Debug is enabled when Joomla debug is enabled.
	 *
	 * @const
	 */
	const DEBUG_AUTO = 'auto';

	/**
	 * Debug is always enabled.
	 *
	 * @const
	 */
	const DEBUG_ALWAYS = 'always';

	/**
	 * Debug is never enabled.
	 *
	 * @const
	 */
	const DEBUG_NEVER = 'never';

	/**
	 * Is debug enabled?
	 *
	 * @var  boolean
	 */
	private $isDebugEnabled;

	/**
	 * Triggered before environment has been loaded.
	 *
	 * @param   Environment      $environment  Loaded environment
	 * @param   LoaderInterface  $loader       Loader going to be sent to environment
	 * @param   array            $options      Options to initialise environment
	 *
	 * @return  void
	 */
	public function onTwigBeforeLoad(Environment $environment, LoaderInterface $loader, &$options)
	{
		$options['debug'] = $this->isDebugEnabled();
	}

	/**
	 * Triggered after environment has been loaded.
	 *
	 * @param   Environment  $environment  Loaded environment
	 * @param   array        $options      Options to initialise environment
	 *
	 * @return  void
	 */
	public function onTwigAfterLoad(Environment $environment, $options = [])
	{
		if ($this->isDebugEnabled())
		{
			$environment->addExtension(new DebugExtension);
		}
	}

	/**
	 * Check if debug is enabled.
	 *
	 * @return  boolean
	 */
	private function isDebugEnabled()
	{
		if (null === $this->isDebugEnabled)
		{
			$this->isDebugEnabled = $this->checkDebugEnabled();
		}

		return $this->isDebugEnabled;
	}

	/**
	 * Check the debug status.
	 *
	 * @return  boolean
	 */
	private function checkDebugEnabled()
	{
		$mode = $this->params->get('mode', self::DEBUG_AUTO);

		if ($mode === self::DEBUG_AUTO)
		{
			return JDEBUG === '1';
		}

		return $mode === self::DEBUG_ALWAYS;
	}
}