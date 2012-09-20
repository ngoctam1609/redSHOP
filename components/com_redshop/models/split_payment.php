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
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.model' );

require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'order.php' );

class split_paymentModelsplit_payment extends JModel
{
	var $_id = null;
	var $_data = null;
	var $_table_prefix = null;
	
	function __construct()
	{
		parent::__construct();

		$this->_table_prefix = '#__redshop_';
	}

	function getordersdetail($oid)
	{
		$query = "SELECT * FROM  ".$this->_table_prefix."orders WHERE order_id = '".$oid."' ";
		$this->_db->setQuery($query);
		$order_detail = $this->_db->loadObject();
		return $order_detail;	
	}
	
	function getuseraccountinfo($uid)
	{
		$query = 'SELECT uf.*,u.email FROM '.$this->_table_prefix.'users_info as uf, #__users as u WHERE user_id='.$uid.' AND uf.user_id=u.id';
		$this->_db->setQuery( $query );
		return $this->_db->loadObject();
	}
	function orderplace() {
		
		global $mainframe;
		$post = JRequest::get ( 'post' );
		$option = JRequest::getVar ( 'option' );
		$Itemid = JRequest::getVar ( 'Itemid' );
		$task = JRequest::getVar ( 'task' );
		$user =& JFactory::getUser();
		$order_functions = new order_functions();
		/*$session =&JFactory::getSession();
   		$issplit=$session->get('issplit') ;
   		 		
		require_once (JPATH_COMPONENT . DS . 'helpers' . DS . 'extra_field.php');
		$field = new extraField ( );
		
		if (GOOGLE_ANA_TRACKER_KEY != "") {
			require_once (JPATH_COMPONENT . DS . 'helpers' . DS . 'google_analytics.php');
		}
		
		$adminpath = JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_redshop';
		require_once ($adminpath . DS . 'helpers' . DS . 'shipping.php');
		$objshipping = new shipping ( );
		
		include_once (JPATH_COMPONENT . DS . 'helpers' . DS . 'product.php');
		$producthelper = new producthelper ( );
		
		require_once (JPATH_COMPONENT . DS . 'helpers' . DS . 'extra_field.php');
		$field = new extraField ( );
		
		require_once (JPATH_COMPONENT . DS . 'helpers' . DS . 'product.php');
		$ps = new producthelper ( );*/
		
		$adminpath = JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_redshop';
		$user = JFactory::getUser ();
		/*$users_info_id = JRequest::getVar ( 'users_info_id' );
		$shipping_rate_id = JRequest::getVar ( 'shipping_rate_id' );*/
		$payment_method_id = JRequest::getVar ( 'payment_method_id' );
		$ccinfo = JRequest::getVar ( 'ccinfo' );
		$order_number =  JRequest::getVar ( 'order_number' );
		$remaningtopay =  JRequest::getVar ( 'remaningtopay' );
		$order_id =  JRequest::getInt ( 'order_id' );
		$order_total =  JRequest::getInt ( 'order_total' );
		$oid = JRequest::getInt('oid');

		$orderdits = $this->getordersdetail($oid);
		
		 $_SESSION['ccdata']['order_payment_name'] = JRequest::getVar('order_payment_name'); 
		// VISA, AMEX, DISCOVER....
		$_SESSION['ccdata']['creditcard_code'] = JRequest::getVar('creditcard_code'); 
		$_SESSION['ccdata']['order_payment_number'] = JRequest::getVar('order_payment_number');  
		$_SESSION['ccdata']['order_payment_expire_month'] = JRequest::getVar('order_payment_expire_month');  
		$_SESSION['ccdata']['order_payment_expire_year'] = JRequest::getVar('order_payment_expire_year');  
		// 3-digit Security Code (CVV)
		$_SESSION['ccdata']['credit_card_code'] = JRequest::getVar('credit_card_code');  
		
		$d ["order_payment_trans_id"] = '';
		$tmporder_total = $remaningtopay;
		/*if($issplit)
		{
			$tmporder_total = $order_total/2;
		}
		 */
		//JRequest::setVar ( 'order_ship', $order_shipping [3] );
		
		$paymentmethod = $order_functions->getPaymentMethodInfo($payment_method_id);
		$paymentmethod = $paymentmethod[0];
		
		JRequest::setVar ( 'paymentmethod', $paymentmethod );

	 
		if ($paymentmethod->plugin == "bank_transfer")
		{
			$order_status = 'ABT';
			$order_paymentstatus = JText::_('COM_REDSHOP_PAYMENT_STA_PAID');
			$order_status_full = 'Awaiting bank transfer';
			
			$query = "UPDATE ".$this->_table_prefix."orders set order_payment_status = '".$order_paymentstatus."', split_payment=0  where order_id = ".$oid;
			$this->_db->setQuery($query);
			$this->_db->query();
			$return=JRoute::_('index.php?option='.$option.'&view=order_detail&oid='.$oid.'&Itemid='.$Itemid);
		}
		
		$data['amount'] = 0;
		if ($paymentmethod->is_creditcard == 1) 
		{
			$validpayment = $this->validatepaymentccinfo();
			
			if(!$validpayment[0]) 
			{
				$msg =  $validpayment[1];
				$link = 'index.php?option=' . $option . '&view=split_payment&Itemid=' . $Itemid . '&ccinfo=' . $ccinfo . '&payment_method_id=' . $payment_method_id. '&oid=' . $oid;
				$mainframe->Redirect ( $link, $msg );
			} 
					 
			
			$paymentpath = $adminpath . DS . 'helpers' . DS . 'payments' . DS . $paymentmethod->plugin . DS . $paymentmethod->plugin . '.php';
			include_once ($paymentpath);
			
			$payment_class = new $paymentmethod->payment_class ( );
			
			//function process_payment($order_number, $order_total, &$d)
			$payment = $payment_class->process_payment ( $order_number, $tmporder_total, $d );
			

			if (! $payment) 
			{
				$msg = "Payment Failure" . $d ["order_payment_log"];
				$link = 'index.php?option=' . $option . '&view=split_payment&Itemid=' . $Itemid . '&ccinfo=' . $ccinfo . '&payment_method_id=' . $payment_method_id. '&oid=' . $oid;
				$mainframe->Redirect ( $link, $msg );
				JRequest::setVar ( 'payment_status_log', '-' . $d ["order_payment_log"] );
			} 
			else 
			{
				$order_status = 'ACCP';
				$order_status_full = 'Awaiting credit card payment';
				$data['amount'] = $tmporder_total;
				if($d ["order_payment_log"]=='SUCCESS')
				{
					// If partial payment success, then update the payment and status					
					$rowpayment = & $this->getTable ( 'order_payment' );
		
					if (! $rowpayment->bind ( $post )) 
					{
						$this->setError ( $this->_db->getErrorMsg () );
						return false;
					}
		
					$rowpayment->order_id = $oid;
					
					$rowpayment->payment_method_id = $payment_method_id;
					$rowpayment->order_payment_code = $_SESSION ['ccdata'] ['creditcard_code'];
					$rowpayment->order_payment_number = base64_encode ( $_SESSION ['ccdata'] ['order_payment_number'] );
					$rowpayment->order_payment_amount = $tmporder_total;		
					$rowpayment->order_payment_expire = $_SESSION ['ccdata'] ['order_payment_expire_month'] . ' ' . $_SESSION ['ccdata'] ['order_payment_expire_year'];
					$rowpayment->order_payment_name = $paymentmethod->payment_method_name;
					$rowpayment->order_payment_trans_id = $d ["order_payment_trans_id"];
					
					if (! $rowpayment->store ()) 
					{
						$this->setError ( $this->_db->getErrorMsg () );
						return false;
					}
					$order_paymentstatus = JText::_('COM_REDSHOP_PAYMENT_STA_PAID');
					$msg = JText::_('COM_REDSHOP_PARTIAL_PAYMENT_DONE');
					
					$query = "UPDATE ".$this->_table_prefix."orders set order_payment_status = '".$order_paymentstatus."', split_payment=0  where order_id = ".$oid;
					$this->_db->setQuery($query);
					$this->_db->query();
					
					$userinfo = $this->getuseraccountinfo($user->id);
					// Add Economic integration
					$return=JRoute::_('index.php?option='.$option.'&view=order_detail&oid='.$oid.'&Itemid='.$Itemid);
				}
				else
				{
					$order_paymentstatus = JText::_('COM_REDSHOP_PAYMENT_STA_PARTIAL_PAID');
					$msg = JText::_('COM_REDSHOP_PARTIAL_PAYMENT_FAILURE');
					$return=JRoute::_('index.php?option='.$option.'&view=order_detail&oid='.$oid.'&Itemid='.$Itemid);
				}				
			}
		
		}		
		$mainframe->Redirect($return,$msg);
	}

	/*function getpaymentmethodinfo($id) 
	{
		$query = "SELECT * FROM " . $this->_table_prefix . "payment_method WHERE published = '1' AND payment_method_id = '" . $id . "'";
		$this->_db->setQuery ( $query );
		return $this->_db->loadObject ();
	}*/
	
	function validatepaymentccinfo() 
	{
		$validpayment [0] = 1;
		$validpayment [1] = '';
		
		// $_SESSION['ccdata'] = $ccdata;
		// The Data should be in the session
		if (! isset ( $_SESSION ['ccdata'] )) { //Not? Then Error
			$validpayment [0] = 0;
			$validpayment [1] = JText::_('COM_REDSHOP_CHECKOUT_ERR_NO_CCDATA' );
			return $validpayment;
		}
		
		if (! $_SESSION ['ccdata'] ['order_payment_number']) {
			$validpayment [0] = 0;
			$validpayment [1] = JText::_('COM_REDSHOP_CHECKOUT_ERR_NO_CCNR_FOUND' );
			return $validpayment;
		}
		
		if (! $_SESSION ['ccdata'] ['order_payment_expire_month']) {
			$validpayment [0] = 0;
			$validpayment [1] = JText::_('COM_REDSHOP_CHECKOUT_ERR_NO_MON_FOUND' );
			return $validpayment;
		}
		
		$ccerror = '';
		$ccerrortext = '';
		
		if (! $this->checkCreditCard ( $_SESSION ['ccdata'] ['order_payment_number'], $_SESSION ['ccdata'] ['creditcard_code'], $ccerror, $ccerrortext )) {
			$validpayment [0] = 0;
			$validpayment [1] = $ccerrortext;
			return $validpayment;
		}
		
		return $validpayment;
	
	}	
	function checkCreditCard($cardnumber, $cardname, &$errornumber, &$errortext) {
		
		// Define the cards we support. You may add additional card types.
		

		//  Name:      As in the selection box of the form - must be same as user's
		//  Length:    List of possible valid lengths of the card number for the card
		//  prefixes:  List of possible prefixes for the card
		//  checkdigit Boolean to say whether there is a check digit
		

		// Don't forget - all but the last array definition needs a comma separator!
		

		$cards = array (array ('name' => 'amex', //American Express
'length' => '15', 'prefixes' => '34,37', 'checkdigit' => true ), array ('name' => 'Diners Club Carte Blanche', 'length' => '14', 'prefixes' => '300,301,302,303,304,305', 'checkdigit' => true ), array ('name' => 'diners', //Diners Club
'length' => '14,16', 'prefixes' => '36,54,55', 'checkdigit' => true ), array ('name' => 'Discover', 'length' => '16', 'prefixes' => '6011,622,64,65', 'checkdigit' => true ), array ('name' => 'Diners Club Enroute', 'length' => '15', 'prefixes' => '2014,2149', 'checkdigit' => true ), array ('name' => 'JCB', 'length' => '16', 'prefixes' => '35', 'checkdigit' => true ), array ('name' => 'Maestro', 'length' => '12,13,14,15,16,18,19', 'prefixes' => '5018,5020,5038,6304,6759,6761', 'checkdigit' => true ), array ('name' => 'MC', //MasterCard
'length' => '16', 'prefixes' => '51,52,53,54,55', 'checkdigit' => true ), array ('name' => 'Solo', 'length' => '16,18,19', 'prefixes' => '6334,6767', 'checkdigit' => true ), array ('name' => 'Switch', 'length' => '16,18,19', 'prefixes' => '4903,4905,4911,4936,564182,633110,6333,6759', 'checkdigit' => true ), array ('name' => 'Visa', 'length' => '13,16', 'prefixes' => '4', 'checkdigit' => true ), array ('name' => 'Visa Electron', 'length' => '16', 'prefixes' => '417500,4917,4913,4508,4844', 'checkdigit' => true ), array ('name' => 'LaserCard', 'length' => '16,17,18,19', 'prefixes' => '6304,6706,6771,6709', 'checkdigit' => true ) );
		
		$ccErrorNo = 0;
		
		$ccErrors [0] =JText::_('COM_REDSHOP_CHECKOUT_ERR_NO_UNKNOWN_CCTYPE' ); 
		$ccErrors [1] = JText::_('COM_REDSHOP_CHECKOUT_ERR_NO_CARD_PROVIDED' );
		$ccErrors [2] = JText::_('COM_REDSHOP_CHECKOUT_ERR_NO_CARD_INVALIDFORMAT' );
		$ccErrors [3] =JText::_('COM_REDSHOP_CHECKOUT_ERR_NO_CARD_INVALIDNUMBER' ); 
		$ccErrors [4] =JText::_('COM_REDSHOP_CHECKOUT_ERR_NO_CARD_WRONGLENGTH' ); 
		
		// Establish card type
		$cardType = - 1;
		for($i = 0; $i < sizeof ( $cards ); $i ++) {
			
			// See if it is this card (ignoring the case of the string)
			if (strtolower ( $cardname ) == strtolower ( $cards [$i] ['name'] )) {
				$cardType = $i;
				break;
			}
		}
		
		// If card type not found, report an error
		if ($cardType == - 1) {
			$errornumber = 0;
			$errortext = $ccErrors [$errornumber];
			return false;
		}
		
		// Ensure that the user has provided a credit card number
		if (strlen ( $cardnumber ) == 0) {
			$errornumber = 1;
			$errortext = $ccErrors [$errornumber];
			return false;
		}
		
		// Remove any spaces from the credit card number
		$cardNo = str_replace ( ' ', '', $cardnumber );
		
		// Check that the number is numeric and of the right sort of length.
		if (! eregi ( '^[0-9]{13,19}$', $cardNo )) {
			$errornumber = 2;
			$errortext = $ccErrors [$errornumber];
			return false;
		}
		
		// Now check the modulus 10 check digit - if required
		if ($cards [$cardType] ['checkdigit']) {
			$checksum = 0; // running checksum total
			$mychar = ""; // next char to process
			$j = 1; // takes value of 1 or 2
			

			// Process each digit one by one starting at the right
			for($i = strlen ( $cardNo ) - 1; $i >= 0; $i --) {
				
				// Extract the next digit and multiply by 1 or 2 on alternative digits.      
				$calc = $cardNo {$i} * $j;
				
				// If the result is in two digits add 1 to the checksum total
				if ($calc > 9) {
					$checksum = $checksum + 1;
					$calc = $calc - 10;
				}
				
				// Add the units element to the checksum total
				$checksum = $checksum + $calc;
				
				// Switch the value of j
				if ($j == 1) {
					$j = 2;
				} else {
					$j = 1;
				}
				;
			}
			
			// All done - if checksum is divisible by 10, it is a valid modulus 10.
			// If not, report an error.
			if ($checksum % 10 != 0) {
				$errornumber = 3;
				$errortext = $ccErrors [$errornumber];
				return false;
			}
		}
		
		// The following are the card-specific checks we undertake.
		

		// Load an array with the valid prefixes for this card
		$prefix = split ( ',', $cards [$cardType] ['prefixes'] );
		
		// Now see if any of them match what we have in the card number  
		$PrefixValid = false;
		for($i = 0; $i < sizeof ( $prefix ); $i ++) {
			$exp = '^' . $prefix [$i];
			if (ereg ( $exp, $cardNo )) {
				$PrefixValid = true;
				break;
			}
		}
		
		// If it isn't a valid prefix there's no point at looking at the length
		if (! $PrefixValid) {
			$errornumber = 3;
			$errortext = $ccErrors [$errornumber];
			return false;
		}
		
		// See if the length is valid for this card
		$LengthValid = false;
		$lengths = split ( ',', $cards [$cardType] ['length'] );
		for($j = 0; $j < sizeof ( $lengths ); $j ++) {
			if (strlen ( $cardNo ) == $lengths [$j]) {
				$LengthValid = true;
				break;
			}
		}
		
		// See if all is OK by seeing if the length was valid. 
		if (! $LengthValid) {
			$errornumber = 4;
			$errortext = $ccErrors [$errornumber];
			return false;
		}
		;
		
		// The credit card is in the required format.
		return true;
	}
	
	function validateCC($cc_num, $type) {
		
		if ($type == "American") {
			$denum = "American Express";
		} elseif ($type == "Dinners") {
			$denum = "Diner's Club";
		} elseif ($type == "Discover") {
			$denum = "Discover";
		} elseif ($type == "Master") {
			$denum = "Master Card";
		} elseif ($type == "Visa") {
			$denum = "Visa";
		}
		
		if ($type == "American") {
			$pattern = "/^([34|37]{2})([0-9]{13})$/"; //American Express
			if (preg_match ( $pattern, $cc_num )) {
				$verified = true;
			} else {
				$verified = false;
			}
		
		} elseif ($type == "Dinners") {
			$pattern = "/^([30|36|38]{2})([0-9]{12})$/"; //Diner's Club
			if (preg_match ( $pattern, $cc_num )) {
				$verified = true;
			} else {
				$verified = false;
			}
		
		} elseif ($type == "Discover") {
			$pattern = "/^([6011]{4})([0-9]{12})$/"; //Discover Card
			if (preg_match ( $pattern, $cc_num )) {
				$verified = true;
			} else {
				$verified = false;
			}
		
		} elseif ($type == "Master") {
			$pattern = "/^([51|52|53|54|55]{2})([0-9]{14})$/"; //Mastercard
			if (preg_match ( $pattern, $cc_num )) {
				$verified = true;
			} else {
				$verified = false;
			}
		
		} elseif ($type == "Visa") {
			$pattern = "/^([4]{1})([0-9]{12,15})$/"; //Visa
			if (preg_match ( $pattern, $cc_num )) {
				$verified = true;
			} else {
				$verified = false;
			}
		
		}
		
		if ($verified == false) {
			//Do something here in case the validation fails
			echo "Credit card invalid. Please make sure that you entered a valid <em>" . $denum . "</em> credit card ";
		
		} else { //if it will pass...do something
			echo "Your <em>" . $denum . "</em> credit card is valid";
		}
	
	}
}