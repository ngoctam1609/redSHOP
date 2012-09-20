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

jimport('joomla.application.component.model');

class wrapperModelwrapper extends JModel
{
	var $_productid = 0;
	var $_data = null;
	var $_total = null;
	var $_pagination = null;
	var $_table_prefix = null;
	var $_context =null;
	
	function __construct()
	{
		parent::__construct();
		global $mainframe;
		
		$this->_context = 'wrapper_id';
		
	  	$this->_table_prefix = '#__'.TABLE_PREFIX.'_';			
		$limit	= $mainframe->getUserStateFromRequest( $this->_context.'limit', 'limit', $mainframe->getCfg('list_limit'), 0);
		$limitstart = $mainframe->getUserStateFromRequest( $this->_context.'limitstart', 'limitstart', 0 );
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
		 
		$product_id = JRequest::getVar('product_id');
		$this->setProductId((int)$product_id);
	}
	function setProductId($id)
	{
	 	$this->_productid	= $id;
		$this->_data	= null;
	}
		
	function getData()
	{		
		if (empty($this->_data))
		{
			$query = $this->_buildQuery();
			$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
		}
		return $this->_data;
	}
		
	function getTotal()
	{
		if (empty($this->_total))
		{
			$query = $this->_buildQuery();
			$this->_total = $this->_getListCount($query);
		}
		return $this->_total;
	}
	function getPagination()
	{
		if (empty($this->_pagination))
		{
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination( $this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
		}
		return $this->_pagination;
	}
  	
	
	function _buildQuery()
	{
		//$orderby	= $this->_buildContentOrderBy();
		$showall = JRequest::getVar('showall','0');
		$and = '';
		if($showall && $this->_productid!=0)
		{
			$and = 'WHERE FIND_IN_SET('.$this->_productid.',w.product_id) OR wrapper_use_to_all = 1 ';
			
			$query = "SELECT * FROM ".$this->_table_prefix."product_category_xref "
					."WHERE product_id = ".$this->_productid;
			$cat = $this->_getList($query);
			for($i=0;$i<count($cat);$i++) 
			{
				$and .= " OR FIND_IN_SET(".$cat[$i]->category_id.",category_id) ";
			}
		}
		$query = 'SELECT distinct(w.wrapper_id), w.* FROM '.$this->_table_prefix.'wrapper AS w '
//				.'LEFT JOIN '.$this->_table_prefix.'product AS p ON p.product_id = w.product_id '
				.$and
				;
		return $query;
	}
}	?>