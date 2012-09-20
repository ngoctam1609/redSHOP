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

jimport( 'joomla.application.component.view' );

class stockimage_detailVIEWstockimage_detail extends JView
{
	function display($tpl = null)
	{
		$option = JRequest::getVar('option','','request','string');
		
		JToolBarHelper::title(   JText::_('COM_REDSHOP_STOCKIMAGE_MANAGEMENT_DETAIL' ), 'redshop_stockroom48' );

		$document = & JFactory::getDocument();
		$uri 		=& JFactory::getURI();
		$this->setLayout('default');

		$lists = array();

		$detail	=& $this->get('data');
		$isNew		= ($detail->stock_amount_id < 1);
		$text = $isNew ? JText::_('COM_REDSHOP_NEW' ) : JText::_('COM_REDSHOP_EDIT' );
		JToolBarHelper::title(   JText::_('COM_REDSHOP_STOCKIMAGE' ).': <small><small>[ ' . $text.' ]</small></small>' , 'redshop_stockroom48'  );

		//create the toolbar
		JToolBarHelper::save();
		if ($isNew)  {
			JToolBarHelper::cancel();
		} else {
			JToolBarHelper::cancel( 'cancel', 'Close' );
		}
		$model=  $this->getModel('stockimage_detail');
		
		$stock_option = $model->getStockAmountOption();
		$stockroom_name = $model->getStockRoomList();
		$op = array();
		$op[0]->value = 0;
		$op[0]->text = JText::_('COM_REDSHOP_SELECT');
		$stockroom_name = array_merge($op,$stockroom_name);

		$lists['stock_option'] 	= JHTML::_('select.genericlist',$stock_option,  'stock_option', 'class="inputbox" size="1" ', 'value', 'text', $detail->stock_option );
		$lists['stockroom_id'] 	= JHTML::_('select.genericlist',$stockroom_name,  'stockroom_id', 'class="inputbox" size="1" ', 'value', 'text', $detail->stockroom_id );

		$this->assignRef('lists',			$lists);
		$this->assignRef('detail',		$detail);
		$this->assignRef('request_url',	$uri->toString());

		parent::display($tpl);
	}
}	?>