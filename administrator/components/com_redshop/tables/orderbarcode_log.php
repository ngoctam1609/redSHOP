<?php
/**
 * @package     redSHOP
 * @subpackage  Tables
 *
 * @copyright   Copyright (C) 2008 - 2012 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die('Restricted access');

/**
 * Barcode reder/generator Model
 *
 * @package    redSHOP
 * @version    1.2
 */
// old TableBarcode
class Tableorderbarcode_log extends JTable
{
    /**
     * Primary Key
     *
     * @var int
     */
    public $log_id = null;

    /**
     * @var string
     */
    public $order_id = null;

    public $user_id = null;

    public $barcode = null;

    public $search_date = null;

    /**
     * Constructor
     *
     * @param object Database connector object
     */
    public function __construct(&$db)
    {

        $this->_table_prefix = '#__redshop_';
        parent::__construct($this->_table_prefix . 'orderbarcode_log', 'log_id', $db);
    }
}
