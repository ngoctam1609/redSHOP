<?php
/**
 * @package     RedShop
 * @subpackage  Libraries
 *
 * @copyright   Copyright (C) 2008 - 2019 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */


namespace Redshop\Extension;

defined('_JEXEC') || die;

use Joomla\CMS\Profiler\Profiler;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * JProfiler integration for Twig.
 *
 * @since  1.0.0
 */
final class JProfiler extends AbstractExtension
{
	/**
	 * Inject functions.
	 *
	 * @return  array
	 */
	public function getFunctions() : array
	{
		return [
			new TwigFunction('jprofiler', [Profiler::class, 'getInstance'])
		];
	}

	/**
	 * Get the name of this extension.
	 *
	 * @return  string
	 */
	public function getName() : string
	{
		return 'jprofiler';
	}
}
