<?php
/**
 * @package     RedShop
 * @subpackage  Cept
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// Load the Step Object Page
$I = new AcceptanceTester($scenario);
$I->wantTo('Test Mail Centers Manager in Administrator');
$I->doAdministratorLogin();
$className = 'AcceptanceTester\MailCenterManagerJoomla3Steps';
$I = new $className($scenario);
$name = 'Testing Mail' . rand(100, 1000);
$subject = 'Subject' . rand(10, 100);
$bcc = 'BCC Test' . rand(100, 1000);
$mailSection = 'Ask question about product';
$newName = 'Updated ' . $name;
$I->addMail($name, $subject, $bcc, $mailSection);
$I->searchMail($name);
$I->editMail($name, $newName);
$I->searchMail($newName);
$I->changeMailState($newName);
$I->verifyState('unpublished', $I->getMailState($newName));
$I->deleteMailTemplate($newName);
$I->searchMail($newName, 'Delete');