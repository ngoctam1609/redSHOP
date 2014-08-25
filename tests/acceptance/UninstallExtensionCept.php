<?php
/**
 * @package     RedShop
 * @subpackage  Cept
 * @copyright   Copyright (C) 2012 - 2014 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
// Load the Step Object Page
$I = new AcceptanceTester\LoginSteps($scenario);

$I->wantTo('Want to Uninstall Extension');
$I->doAdminLogin("Function to Login to Admin Panel");

$I = new AcceptanceTester\UninstallExtensionSteps($scenario);
$I->wantTo('Uninstall Extension');
$I->uninstallExtension('redSHOP');
