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

defined ( '_JEXEC' ) or die ( 'Restricted access' );

jimport ( 'joomla.application.component.controller' );
////////// include extra field class  /////////////////////////////////////
require_once( JPATH_COMPONENT.DS.'helpers'.DS.'extra_field.php' );
////////// include extra field class  /////////////////////////////////////

class manufacturer_detailController extends JController {
	function __construct($default = array()) {
		parent::__construct ( $default );
		$this->registerTask ( 'add', 'edit' );
	}
	function edit() {
		JRequest::setVar ( 'view', 'manufacturer_detail' );
		JRequest::setVar ( 'layout', 'default' );
		JRequest::setVar ( 'hidemainmenu', 1 );
		parent::display ();
	
	}
	function apply() 
	{
       $this->save(1);
	}
	function save($apply=0) {	
		
		$post = JRequest::get ( 'post',JREQUEST_ALLOWRAW );
		$manufacturer_desc = JRequest::getVar( 'manufacturer_desc', '', 'post', 'string', JREQUEST_ALLOWRAW );
		$post["manufacturer_desc"]=$manufacturer_desc;		
		
		$option = JRequest::getVar ('option');
		
		$cid = JRequest::getVar ( 'cid', array (0 ), 'post', 'array' );
		
		$post ['manufacturer_id'] = $cid [0];
		
		$model = $this->getModel ( 'manufacturer_detail' );
		
		if ($row=$model->store ( $post )) {

 			$field = new extra_field();
			$field->extra_field_save($post,"10", $row->manufacturer_id); /// field_section 6 :Userinformations
			
		
			$msg = JText::_('COM_REDSHOP_MANUFACTURER_DETAIL_SAVED' );
		
		} else {
			
			$msg = JText::_('COM_REDSHOP_ERROR_SAVING_MANUFACTURER_DETAIL' );
		}
		
		if($apply==1){
			$this->setRedirect ( 'index.php?option=' . $option . '&view=manufacturer_detail&task=edit&cid[]='.$row->manufacturer_id, $msg );
			//option=com_redshop&view=manufacturer_detail&task=edit&cid[]=1

		} else {
			$this->setRedirect ( 'index.php?option=' . $option . '&view=manufacturer', $msg );
		}
		
		
	}
	function remove() {
		
		$option = JRequest::getVar ('option');
		
		$cid = JRequest::getVar ( 'cid', array (0 ), 'post', 'array' );
		
		if (! is_array ( $cid ) || count ( $cid ) < 1) {
			JError::raiseError ( 500, JText::_('COM_REDSHOP_SELECT_AN_ITEM_TO_DELETE' ) );
		}
		
		$model = $this->getModel ( 'manufacturer_detail' );
		if (! $model->delete ( $cid )) {
			echo "<script> alert('" . $model->getError ( true ) . "'); window.history.go(-1); </script>\n";
		}
		$msg = JText::_('COM_REDSHOP_MANUFACTURER_DETAIL_DELETED_SUCCESSFULLY' );
		$this->setRedirect ( 'index.php?option='.$option.'&view=manufacturer',$msg );
	}
	function publish() {
		
		$option = JRequest::getVar ('option');
		
		$cid = JRequest::getVar ( 'cid', array (0 ), 'post', 'array' );
		
		if (! is_array ( $cid ) || count ( $cid ) < 1) {
			JError::raiseError ( 500, JText::_('COM_REDSHOP_SELECT_AN_ITEM_TO_PUBLISH' ) );
		}
		
		$model = $this->getModel ( 'manufacturer_detail' );
		if (! $model->publish ( $cid, 1 )) {
			echo "<script> alert('" . $model->getError ( true ) . "'); window.history.go(-1); </script>\n";
		}
		$msg = JText::_('COM_REDSHOP_MANUFACTURER_DETAIL_PUBLISHED_SUCCESSFULLY' );
		$this->setRedirect ( 'index.php?option='.$option.'&view=manufacturer',$msg );
	}
	function unpublish() {
		
		$option = JRequest::getVar ('option');
		
		$cid = JRequest::getVar ( 'cid', array (0 ), 'post', 'array' );
		
		if (! is_array ( $cid ) || count ( $cid ) < 1) {
			JError::raiseError ( 500, JText::_('COM_REDSHOP_SELECT_AN_ITEM_TO_UNPUBLISH' ) );
		}
		
		$model = $this->getModel ( 'manufacturer_detail' );
		if (! $model->publish ( $cid, 0 )) {
			echo "<script> alert('" . $model->getError ( true ) . "'); window.history.go(-1); </script>\n";
		}
		$msg = JText::_('COM_REDSHOP_MANUFACTURER_DETAIL_UNPUBLISHED_SUCCESSFULLY' );
		$this->setRedirect ( 'index.php?option='.$option.'&view=manufacturer',$msg );
	}
	function cancel() {
		
		$option = JRequest::getVar ('option');
		$msg = JText::_('COM_REDSHOP_MANUFACTURER_DETAIL_EDITING_CANCELLED' );
		$this->setRedirect ( 'index.php?option='.$option.'&view=manufacturer',$msg );
	}
	function copy(){
		
		$option = JRequest::getVar ('option');
		
		$cid = JRequest::getVar ( 'cid', array (0 ), 'post', 'array' );
		
		$model = $this->getModel ( 'manufacturer_detail' );
				
		if ($model->copy($cid)) {
			
			$msg = JText::_('COM_REDSHOP_MANUFACTURER_DETAIL_COPIED' );
		
		} else {
			
			$msg = JText::_('COM_REDSHOP_ERROR_COPING_MANUFACTURER_DETAIL' );
		}
		
		$this->setRedirect ( 'index.php?option=' . $option . '&view=manufacturer', $msg );
	}
	
	/**
	 * logic for orderup manufacturer
	 *
	 * @access public
	 * @return void
	 */
	function orderup()
	{
	    $option = JRequest::getVar('option');

		$model = $this->getModel('manufacturer_detail');
		$model->move(-1);
 		//$model->orderup();
		$msg = JText::_('COM_REDSHOP_NEW_ORDERING_SAVED' );
		$this->setRedirect ( 'index.php?option='.$option.'&view=manufacturer',$msg );
	}
	/**
	 * logic for orderdown manufacturer
	 *
	 * @access public
	 * @return void
	 */
	function orderdown()
	{
		$option = JRequest::getVar('option');
		$model = $this->getModel('manufacturer_detail');
		$model->move(1);
		//$model->orderdown();
		$msg = JText::_('COM_REDSHOP_NEW_ORDERING_SAVED' );
		$this->setRedirect ( 'index.php?option='.$option.'&view=manufacturer',$msg );
	}
	
	/**
	 * logic for save an order
	 *
	 * @access public
	 * @return void
	 */
	function saveorder()
	{
		$option = JRequest::getVar('option');
		 
		$cid 	= JRequest::getVar( 'cid', array(), 'post', 'array' );
		$order 	= JRequest::getVar( 'order', array(), 'post', 'array' );
		
		JArrayHelper::toInteger($cid);
		JArrayHelper::toInteger($order);

		$model = $this->getModel('manufacturer_detail');
		$model->saveorder($cid);

		$msg = JText::_('COM_REDSHOP_MANUFACTURER_DETAIL_SAVED' );
		$this->setRedirect ( 'index.php?option='.$option.'&view=manufacturer',$msg );
	}

}
