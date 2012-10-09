<?php
/**
 * @package     redSHOP
 * @subpackage  Tables
 *
 * @copyright   Copyright (C) 2008 - 2012 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die('Restricted access');

class Tableproduct_subscribe_detail extends JTable
{
    public $product_subscribe_id = 0;

    public $order_id = 0;

    public $order_item_id = 0;

    public $product_id = 0;

    public $subscription_id = 0;

    public $user_id = 0;

    public $start_date = 0;

    public $end_date = 0;

    public function __construct(& $db)
    {
        $this->_table_prefix = '#__redshop_';

        parent::__construct($this->_table_prefix . 'product_subscribe_detail', 'product_subscribe_id', $db);
    }
}
