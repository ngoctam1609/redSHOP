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
defined('_JEXEC') or die('Restricted access');
JHTMLBehavior::modal();
// Getting the configuration in redshop.js.php

require_once  JPATH_ROOT.DS.'components'.DS.'com_redshop'.DS.'helpers'.DS.'redshop.js.php' ;

global $Redconfiguration;
$Redconfiguration = new Redconfiguration();
$Redconfiguration->defineDynamicVars();

// Getting the configuration
require_once JPATH_ADMINISTRATOR.DS.'components'.DS.'com_redshop'.DS.'helpers'.DS.'category.php';

// get product helper
require_once  JPATH_ROOT.DS.'components'.DS.'com_redshop'.DS.'helpers'.DS.'product.php' ;

 // get default helper
require_once  JPATH_ROOT.DS.'components'.DS.'com_redshop'.DS.'helpers'.DS.'helper.php' ;

/**
* This class sets all Parameters.
* Must first call the MOS function, something like:
* $params = mosParseParams( $module->params );
* and send the $params variable to this class (productScroller)
* @param $params the results from mosParseParams( $module->params );
* @example $scroller = new productScroller($params);
*/
if( !class_exists('redFeatureproduct'))
{ // Prevent double class declaration

	class redFeatureproduct
	{
		/**
		* @var $NumberOfProducts
		*/
		var $NumberOfProducts = 5;

		/**
  		* // newest [asc], oldest [desc], random [rand]
 		* @var $SortMethod
  		*/
		var $ScrollSortMethod = 'random';

		var $params = null;
		/**
        * set mammeters
        */
		function redFeatureproduct (&$params,$module_id)
		{
			$this->params = $params;
			// standard mammeters
			$this->show_product_name        =  $params->get('show_product_name', "yes");
			$this->product_title_max_chars  =  $params->get('product_title_max_chars', "10");
			$this->product_title_end_suffix =  $params->get('product_title_end_suffix', "....");
			$this->show_addtocart           =  $params->get('show_addtocart', "yes");
			$this->show_vatprice 			=  $params->get('show_vatprice', "0");
			$this->show_price               =  $params->get('show_price', "yes");
			$this->thumbwidth               =  $params->get('thumbwidth', "100");
			$this->thumbheight              =  $params->get('thumbheight', "100");
			$this->scrollerheight			=  $params->get('scrollerheight',"200");
			$this->scrollerwidth			=  $params->get('scrollerwidth',"700");
			$this->show_discountpricelayout =  $params->get('show_discountpricelayout', "100");
			// Limit by NoP
			$this->NumberOfProducts         =  $params->get('NumberOfProducts', $this->NumberOfProducts);
			$this->ScrollSortMethod			=  $params->get('ScrollSortMethod', $this->ScrollSortMethod);
			$this->module_id				=  $module_id;
		}

		/**
		* Display Product Data
		*/
		function displayredFeature (&$rows)
		{
			global $Redconfiguration;
			$uri = JUri::getInstance();
			$url = $uri->root();
			$user =JFactory::getUser();
			$producthelper = new producthelper();
			$redhelper = new redhelper();
			$Itemid = JRequest::getVar('Itemid');
			$view = JRequest::getCmd('view','category');

			$document = JFactory::getDocument();
			JHTML::Stylesheet('jquery.css', 'modules/mod_redfeaturedproduct/css/');
			//$document->addStyleSheet("modules/mod_redfeaturedproduct/css/skin.css");
			JHTML::Stylesheet('skin_002.css', 'modules/mod_redfeaturedproduct/css/');
			//$document->addScript("modules/mod_redfeaturedproduct/js/jquery-1.js");

			if ($view == 'category')
			{
				if(!$GLOBALS['product_price_slider'])
				{
					JHTML::Script('jquery.tools.min.js', 'components/com_redshop/assets/js/',false);
				}
			}else
			{
				JHTML::Script('fetchscript.js', 'components/com_redshop/assets/js/',false);
				JHTML::Script('attribute.js', 'components/com_redshop/assets/js/',false);
				JHTML::Script('common.js', 'components/com_redshop/assets/js/',false);
				JHTML::Script('jquery.tools.min.js', 'components/com_redshop/assets/js/',false);
			}
			JHTML::Script('jquery.js', 'modules/mod_redfeaturedproduct/js/',false);
			JHTML::Script('recreativo.js', 'modules/mod_redfeaturedproduct/js/',false);

			echo $this->params->get( 'pretext', "");

			if(count($rows)>0)
			{
				$rightarrow = $this->scrollerwidth + 20;	?>
				<div style='height:<?php echo $this->scrollerheight;?>px;'>
				<div><div class='red_product-skin-produkter'>
					<div style='display: block;' class='red_product-container red_product-container-horizontal'>
						<div style='display: block;' class='red_product-prev red_product-prev-horizontal'></div>
						<div style='display: block;left:<?php echo $rightarrow;?>px;' class='red_product-next red_product-next-horizontal'></div>
						<div style='width:<?php echo $this->scrollerwidth;?>px;' class='red_product-clip red_product-clip-horizontal'>
							<ul id='produkt_carousel' class='red_product-list red_product-list-horizontal'>
		<?php 	$i = 0;
				foreach($rows as $row)
				{
					$ItemData = $producthelper->getMenuInformation(0,0,'','product&pid='.$row->product_id);
					if(count($ItemData)>0)
					{
						$Itemid = $ItemData->id;
					}else
					{
						$Itemid = $redhelper->getItemid($row->product_id);
					}

					$cid	= JRequest::getInt('cid');
					if(!$cid)
					{
						$cid = $producthelper->getCategoryProduct($row->product_id);
					}
					$link 	= JRoute::_( 'index.php?option=com_redshop&view=product&pid='.$row->product_id.'&cid='.$cid.'&Itemid='.$Itemid);
					$prod_img="";
					if(is_file(REDSHOP_FRONT_IMAGES_RELPATH."product/".$row->product_full_image))
					$prod_img=$url."components/com_redshop/helpers/thumb.php?filename=product/".$row->product_full_image."&newxsize=".$this->thumbwidth."&newysize=".$this->thumbheight;
					else if(is_file(REDSHOP_FRONT_IMAGES_RELPATH."product/".$row->product_thumb_image))
					$prod_img=$url."components/com_redshop/helpers/thumb.php?filename=product/".$row->product_thumb_image."&newxsize=".$this->thumbwidth."&newysize=".$this->thumbheight;
					else
					$prod_img=REDSHOP_FRONT_IMAGES_ABSPATH."noimage.jpg";
					$thum_image = "<a href='".$link."' title='' ><img style='width:".$this->thumbwidth."px;height:".$this->thumbheight."px;' src='".$prod_img."'></a>";
					?>

					<li red_productindex='<?php echo $i;?>' class='red_product-item red_product-item-horizontal'>
   						<div class='listing-item'>
   						<div class='product-shop'>
				<?php	if($this->show_product_name == 'yes')
						{
           					$pname = $Redconfiguration->maxchar ( $row->product_name , $this->product_title_max_chars, $this->product_title_end_suffix );
							echo "<a href='".$link."' title='".$row->product_name."'>".$pname."</a>";
						}
						if(!$row->not_for_sale && $this->show_price == 'yes')
	            		{
	            			$productArr 			 = $producthelper->getProductNetPrice($row->product_id);
							if($this->show_vatprice == '0' || $this->show_vatprice == 0)
							{
								$product_price 			 = $productArr['product_main_price'];
								$product_price_discount  = $productArr['productPrice'] + $productArr['productVat'];
							}else{
								$product_price 			 = $productArr['product_price_novat'];
								$product_price_discount  = $productArr['productPrice'];
							}
							if(SHOW_PRICE && !USE_AS_CATALOG && (!DEFAULT_QUOTATION_MODE || (DEFAULT_QUOTATION_MODE && SHOW_QUOTATION_PRICE)))
							{
								if(!$product_price)
								{
									$product_price_dis = $producthelper->getPriceReplacement($product_price);
								} else {
									$product_price_dis = $producthelper->getProductFormattedPrice($product_price);
								}
								$disply_text = "<div class='mod_redproducts_price'>".$product_price_dis."</div>";
								if($row->product_on_sale && $product_price_discount > 0)
								{
									if($product_price > $product_price_discount)
									{
										$disply_text = "";
										$s_price = $product_price - $product_price_discount;
										if($this->show_discountpricelayout)
										{
											echo "<div id='mod_redoldprice' class='mod_redoldprice'><span style='text-decoration:line-through;'>".$producthelper->getProductFormattedPrice($product_price)."</span></div>";
											echo "<div id='mod_redmainprice' class='mod_redmainprice'>".$producthelper->getProductFormattedPrice($product_price_discount)."</div>";
											echo "<div id='mod_redsavedprice' class='mod_redsavedprice'>".JText::_('COM_REDSHOP_PRODCUT_PRICE_YOU_SAVED').' '.$producthelper->getProductFormattedPrice($s_price)."</div>";
										} else {
											echo "<div class='mod_redproducts_price'>".$producthelper->getProductFormattedPrice($product_price_discount)."</div>";
										}
									}
								}
								echo $disply_text;
							} /*else {
								$product_price_dis = $producthelper->getPriceReplacement($product_price);
								echo "<div class='mod_redproducts_price'>".$product_price_dis."</div>";
							}*/
            			}	?>
            			</div></div><div class='product-image' style='width:<?php echo $this->thumbwidth;?>px;height:<?php echo $this->thumbheight;?>px;'><?php echo $thum_image;?></div>
     			<?php 	if($this->show_addtocart == 'yes')
						{
							$addtocart_data = $producthelper->replaceCartTemplate($row->product_id,0,0,0,"",false,array(),0,0,0,$this->module_id);
							echo "<div class='form-button'>".$addtocart_data."</div>";
						}	?>
						</li>
				<?php 	$i++;
					}	?>
					</ul>
                    </div>
                </div>
            </div>
        </div></div>
	<?php 	}
			else
			{
				echo "<div>".JText::_("COM_REDSHOP_NO_FEATURED_PRODUCTS_TO_DISPLAY")."</div>";
			}
		} // end displayredScroller

		/**
		* Helper DB function
		*/
		function getredFeaturedProduct( $limit=0, $how=null )
		{
			$database = JFactory::getDBO();
			$qlimit = "";
			$orderby = "";
			if($limit>0)
			{
				$qlimit = "LIMIT $limit";
			}
			switch( $how )
			{
				case 'random':
					$orderby = "ORDER BY RAND() ";
					break;
				case 'newest':
					$orderby = "ORDER BY publish_date DESC ";
					break;
				case 'oldest':
					$orderby = "ORDER BY publish_date ASC ";
					break;
				default:
					$orderby = "ORDER BY publish_date DESC ";
					break;
			}
			$query = "SELECT *  FROM #__redshop_product "
					."WHERE published = 1 "
					."AND product_special = 1 "
					.$orderby
					.$qlimit;
			$database->setQuery( $query );
			$rows = $database->loadObjectList();
			return $rows;
		}
	}
} // end class productScroller
$module_id = "mod_".$module->id;
// start of Product Scroller Script
$featured = new redFeatureproduct($params,$module_id);
/**
* Load Products
**/
$rows = $featured->getredFeaturedProduct( $featured->NumberOfProducts, $featured->ScrollSortMethod);
/**
* Display Product Scroller
**/
$featured->displayredFeature($rows);
?>