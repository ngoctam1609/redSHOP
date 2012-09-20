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
defined ('_JEXEC') or die ('restricted access');

JHTML::_('behavior.tooltip');
JHTML::_('behavior.modal');

require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_redshop'.DS.'helpers'.DS.'category.php');
require_once( JPATH_ROOT.DS.'components'.DS.'com_redshop'.DS.'helpers'.DS.'product.php' );
require_once( JPATH_ROOT.DS.'components'.DS.'com_redshop'.DS.'helpers'.DS.'helper.php' );

$config = new Redconfiguration();
$producthelper = new producthelper();
$redhelper = new redhelper();

$uri =& JURI::getInstance();
$url= $uri->root();
$option = JRequest::getVar('option');
$Itemid = JRequest::getVar('Itemid');
$wishlists = $this->wishlists;
$product_id = JRequest::getInt('product_id');
$user =& JFactory::getUser();
$session = & JFactory::getSession ();
$auth = $session->get ( 'auth' );
?>
<div id="newwishlist" class="wishlist_prompt_header" >
<?php

	$pagetitle  = JText ::_('COM_REDSHOP_LOGIN_NEWWISHLIST');
	?>
	<br />
<h1 class="componentheading<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
      <?php echo $pagetitle; ?>
</h1>
<div>&nbsp;</div>


</div>

<div id="wishlist"   class="wishlist_prompt_text">
<?php
if($user->id || (isset($auth['users_info_id']) && $auth['users_info_id'] > 0))
{	

	$wishreturn = JRoute::_ ( 'index.php?loginwishlist=1&option=com_redshop&view=wishlist&Itemid='.$Itemid, false );
	$mainframe->Redirect($wishreturn);
 } else {
			
$pagetitle = JText::_('COM_REDSHOP_LOGIN_PROMPTWISHLIST');
?>
<br />
<h1 class="componentheading<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
      <?php echo $pagetitle; ?>
</h1>
<div>&nbsp;</div>

	<form name="adminForm" method="post" action="">
	<table class="adminlist">
		<tbody>
			<tr>
				<td colspan="3"  align="center" class="wishlist_prompt_button_wrapper">
					<input type="button" class="wishlist_prompt_button_login" value="<?php echo JText::_('COM_REDSHOP_ADD_TO_LOGINWISHLIST');?>" onclick="window.parent.location.href='index.php?option=com_redshop&view=login&wishlist=1'" />&nbsp;
					<input type="button" class="wishlist_prompt_button_create" value="<?php echo JText::_('COM_REDSHOP_CREATE_LOGINACCOUNT');?>" onclick="window.parent.location.href='index.php?option=com_redshop&view=registration&Itemid=<?php echo $Itemid?>&wishlist=1'" />
				</td>
			</tr>
		</tbody>
	</table>
	<input type="hidden" name="wishlist" value="1" />
	<input type="hidden" name="view" value="wishlist" />
	<input type="hidden" name="boxchecked" value="" />

	<input type="hidden" name="option" value="<?php echo $option;?>" />
	<input type="hidden" name="task" value="viewloginwishlist" />
	</form>
</div>
<?php } ?>

