<?php
/**
 * @copyright Copyright (C) 2010 redCOMPONENT.com. All rights reserved.
 * @license GNU/GPL, see license.txt or http://www.gnu.org/copyleft/gpl.html
 * Developed by email@recomponent.com - redCOMPONENT.com
 *
 * redSHOP can be downloaded from www.redcomponent.com
 * redSHOP is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License 2
 * as published by the Free Software Foundation.
 *
 * You should have received a copy of the GNU General Public License
 * along with redSHOP; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.view');

class catalog_detailVIEWcatalog_detail extends JView
{
	function display($tpl = null)
	{
		$option = JRequest::getVar('option');

		JToolBarHelper::title(JText::_('COM_REDSHOP_CATALOG_MANAGEMENT_DETAIL' ), 'redshop_catalogmanagement48');

		$document = JFactory::getDocument();

		$document->addStyleSheet ( 'components/'.$option.'/assets/css/colorpicker.css' );
		$document->addStyleSheet ( 'components/'.$option.'/assets/css/layout.css' );
		$document->addScript ('components/'.$option.'/assets/js/validation.js');

		$document->addScript ('components/'.$option.'/assets/js/jquery.js');
		$document->addScript ('components/'.$option.'/assets/js/colorpicker.js');

		$document->addScript ('components/'.$option.'/assets/js/eye.js');
		$document->addScript ('components/'.$option.'/assets/js/utils.js');
		$document->addScript ('components/'.$option.'/assets/js/layout.js?ver=1.0.2');

		$uri = JFactory::getURI();

		$this->setLayout('default');

		$lists = array();

		$detail	= $this->get('data');

		$layout = JRequest::getVar('layout','default');

		$this->setLayout($layout);

		$model=  $this->getModel('catalog_detail');

        $isNew	= ($detail->catalog_id < 1);

		$text = $isNew ? JText::_('COM_REDSHOP_NEW' ) : JText::_('COM_REDSHOP_EDIT' );

		JToolBarHelper::title(   JText::_('COM_REDSHOP_CATALOG' ).': <small><small>[ ' . $text.' ]</small></small>' , 'redshop_catalogmanagement48');

		JToolBarHelper::save();

		if ($isNew)  {
			JToolBarHelper::cancel();
		} else {

			JToolBarHelper::cancel( 'cancel', 'Close' );
		}


		$lists['published'] = JHTML::_('select.booleanlist',  'published', 'class="inputbox"', $detail->published );

		$this->assignRef('lists',		$lists);
		$this->assignRef('detail',		$detail);
		$this->assignRef('request_url',	$uri->toString());

		parent::display($tpl);
	}
}
