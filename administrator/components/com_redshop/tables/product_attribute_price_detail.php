<?php
/**
 * @package     redSHOP
 * @subpackage  Tables
 *
 * @copyright   Copyright (C) 2008 - 2012 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die('Restricted access');

class Tableproduct_attribute_price_detail extends JTable
{
    public $price_id = null;

    public $section_id = 0;

    public $section = null;

    public $product_price = 0;

    public $product_currency = null;

    public $cdate = 0;

    public $shopper_group_id = 0;

    public $price_quantity_start = 0;

    public $price_quantity_end = 0;

    public $discount_price = 0;

    public $discount_start_date = 0;

    public $discount_end_date = 0;

    public function __construct(& $db)
    {
        $this->_table_prefix = '#__redshop_';

        parent::__construct($this->_table_prefix . 'product_attribute_price_detail', 'price_id', $db);
    }
}

