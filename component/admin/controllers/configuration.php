<?php
/**
 * @package     RedSHOP.Backend
 * @subpackage  Controller
 *
 * @copyright   Copyright (C) 2008 - 2017 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

jimport('joomla.filesystem.folder');

/**
 * Configuration controller
 *
 * @since  2.0.4
 */
class RedshopControllerConfiguration extends RedshopController
{
	/**
	 * Apply configuration
	 *
	 * @return  void
	 *
	 * @since   1.5
	 */
	public function apply()
	{
		$this->save(1);
	}

	/**
	 * Collect Items from array using specific prefix
	 *
	 * @param   array   $array   Array from which needs to collects items based ok keys.
	 * @param   string  $prefix  Key prefix which needs to be filtered.
	 *
	 * @return  array            Array of values which is collected using prefix.
	 */
	protected function collectItemsUsingPrefix($array, $prefix)
	{
		$keys = array_keys($array);

		$values = array_filter(
				$keys,
				function ($value) use ($prefix)
				{
					return preg_match("/$prefix\d/", $value);
				}
		);

		array_walk(
			$values,
			function (&$value) use ($array)
			{
				$value = $array[$value];
			},
			$array
		);

		return $values;
	}

	/**
	 * Method for save configuration
	 *
	 * @param   int  $apply  Apply or not
	 *
	 * @return  boolean
	 *
	 * @since  1.5
	 */
	public function save($apply = 0)
	{
		$post = $this->input->post->getArray();

		$app                 = JFactory::getApplication();
		$selectedTabPosition = $this->input->get('selectedTabPosition');
		$app->setUserState('com_redshop.configuration.selectedTabPosition', $selectedTabPosition);

		$post['custom_previous_link'] = $this->input->post->get('custom_previous_link', '', 'raw');

		$post['custom_next_link'] = $this->input->post->get('custom_next_link', '', 'raw');

		$post['default_next_suffix'] = $this->input->post->get('default_next_suffix', '', 'raw');

		$post['default_previous_prefix'] = $this->input->post->get('default_previous_prefix', '', 'raw');

		$post['return_to_category_prefix'] = $this->input->post->get('return_to_category_prefix', '', 'raw');

		// Administrator email notifications ids
		if (is_array($post['administrator_email']))
		{
			$post['administrator_email'] = trim(implode(",", $post['administrator_email']));
		}

		// Only check if this email is filled
		if (!empty($post['administrator_email']))
		{
			$emails = explode(',' , $post['administrator_email']);
			if (is_array($emails))
			{
				foreach ($emails as $email)
				{
					if (!filter_var($email, FILTER_VALIDATE_EMAIL))
					{
						$msg= JText::_('COM_REDSHOP_INVALID_EMAIL');
						$this->setRedirect('index.php?option=com_redshop&view=configuration', $msg, 'error');
						return false;
					}
				}
			}
		}

		$msg                   = null;
		/** @var RedshopModelConfiguration $model */
		$model                 = $this->getModel('Configuration');
		$newsletter_test_email = $this->input->getRaw('newsletter_test_email');

		// Only check if this email is filled
		if (!empty($newsletter_test_email))
		{
			if (!filter_var($newsletter_test_email, FILTER_VALIDATE_EMAIL))
			{
				$msg= JText::_('COM_REDSHOP_INVALID_EMAIL');
				$this->setRedirect('index.php?option=com_redshop&view=configuration', $msg, 'error');

				return false;
			}
		}

		$post['country_list'] = implode(',', $this->input->post->get('country_list', array(), 'ARRAY'));

		if (!isset($post['seo_page_short_description']))
		{
			$post['seo_page_short_description'] = 0;
		}

		if (!isset($post['seo_page_short_description_category']))
		{
			$post['seo_page_short_description_category'] = 0;
		}

		if (!isset($post['allow_multiple_discount']))
		{
			$post['allow_multiple_discount'] = 0;
		}

		$post['menuhide'] = implode(',', $this->input->post->get('menuhide', array(), 'ARRAY'));

		if (isset($post['product_download_root']) && !is_dir($post['product_download_root']))
		{
			$msg = "";
			JFactory::getApplication()->enqueueMessage(JText::_('COM_REDSHOP_PRODUCT_DOWNLOAD_DIRECTORY_DOES_NO_EXIST'), 'error');
		}
		elseif ($model->store($post))
		{
			$msg = JText::_('COM_REDSHOP_CONFIG_SAVED');

			if ($newsletter_test_email)
			{
				$model->newsletterEntry($post);
				$msg = JText::sprintf('COM_REDSHOP_NEWSLETTER_SEND_TO_TEST_EMAIL', $newsletter_test_email);
			}

			// Thumb folder deleted and created
			if ($post['image_quality_output'] != IMAGE_QUALITY_OUTPUT
				|| $post['use_image_size_swapping'] != Redshop::getConfig()->get('USE_IMAGE_SIZE_SWAPPING'))
			{
				$this->removeThumbImages();
			}
		}
		else
		{
			$msg = JText::_('COM_REDSHOP_ERROR_IN_CONFIG_SAVE');
		}

		if ($apply)
		{
			$this->setRedirect('index.php?option=com_redshop&view=configuration', $msg);
		}

		else
		{
			$this->setRedirect('index.php?option=com_redshop', $msg);
		}
	}

	/**
	 * Remove all thumbnail images generated by redSHOP
	 *
	 * @return  boolean
	 */
	public function removeThumbImages()
	{
		$thumb_folder = array('product', 'category', 'manufacturer', 'product_attributes', 'property', 'subcolor', 'wrapper', 'shopperlogo');

		for ($i = 0, $in = count($thumb_folder); $i < $in; $i++)
		{
			$unlink_path = REDSHOP_FRONT_IMAGES_RELPATH . $thumb_folder[$i] . '/thumb';

			if (JFolder::exists($unlink_path))
			{
				if (JFolder::delete($unlink_path) !== true)
				{
					return false;
				}
				else
				{
					if (JFolder::create($unlink_path) !== true)
					{
						return false;
					}
					else
					{
						$src = REDSHOP_FRONT_IMAGES_RELPATH . 'index.html';
						JFile::COPY($src, $unlink_path . '/index.html');
					}
				}
			}
		}

		return true;
	}

	/**
	 * Remove images
	 *
	 * @return  boolean
	 */
	public function removeimg()
	{
		ob_clean();
		$imname      = $this->input->getString('imname', '');
		$spath       = $this->input->getString('spath', '');
		$data_id     = $this->input->getInt('data_id', 0);
		$extra_field = extra_field::getInstance();

		if ($data_id)
		{
			$extra_field->deleteExtraFieldData($data_id);
		}

		if (JPATH_ROOT . '/' . $spath . '/' . $imname)
		{
			unlink(JPATH_ROOT . '/' . $spath . '/' . $imname);
		}

		exit;
	}

	/**
	 * Cancel
	 *
	 * @return  void
	 */
	public function cancel()
	{
		$this->setRedirect('index.php?option=com_redshop');
	}

	/**
	 * Reset template
	 *
	 * @return  void
	 */
	public function resetTemplate()
	{
		/** @var RedshopModelConfiguration $model */
		$model = $this->getModel('Configuration');

		$model->resetTemplate();

		$msg = JText::_('COM_REDSHOP_TEMPLATE_HAS_BEEN_RESET');
		$this->setRedirect('index.php?option=com_redshop', $msg);
	}

	/**
	 * Reset Term & Condition
	 *
	 * @return  void
	 */
	public function resetTermsCondition()
	{
		$userHelper = rsUserHelper::getInstance();
		$userHelper->updateUserTermsCondition();

		JFactory::getApplication()->exit;
	}

	/**
	 * Reset Order ID
	 *
	 * @return  void
	 */
	public function resetOrderId()
	{
		RedshopHelperOrder::resetOrderId();

		JFactory::getApplication()->exit;
	}
}
