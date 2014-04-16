<?php


/* SSL Management */
$useSSL = true;
require_once(dirname(__FILE__).'/../../config/config.inc.php');
require_once(dirname(__FILE__).'/../../init.php');

include_once(dirname(__FILE__).'/megacustomers.php');

$errors = array();
$active = false;

if(Tools::getValue('action'))
{
	$action = Tools::getValue('action');
	if($action=="active")
	{
		
		
		if ( Tools::getIsset('email') && Tools::getIsset('code') )	
		{
			$result = Db::getInstance()->ExecuteS('SELECT * FROM ' . _DB_PREFIX_ . 'customer WHERE email = "' .  pSQL(Tools::getValue('email')) . '" AND passwd = "' . pSQL(Tools::getValue('code')) . '" AND active = 0');
			if (count($result))
			{
				if (Db::getInstance()->Execute('UPDATE ' . _DB_PREFIX_ . 'customer SET active = 1 WHERE email = "' . pSQL(Tools::getValue('email')) . '" AND passwd = "' . pSQL(Tools::getValue('code')) . '" AND active = 0') )
				{
						$active =  true;
				}
				else
					$errors[] = Tools::displayError('Activation failed');
			}
			else
					$errors[] = Tools::displayError('Account already activated or unknown');
		}				
	
	}
}


include(dirname(__FILE__).'/../../header.php');

// Smarty display
$smarty->assign(array(
	'errors' => $errors,
	'active' => $active,
	
));

echo Module::display(dirname(__FILE__).'/megacustomers.php', 'tpl/result.tpl');


include(dirname(__FILE__).'/../../footer.php'); 