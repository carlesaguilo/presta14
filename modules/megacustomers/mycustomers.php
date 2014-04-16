<?php


/* SSL Management */
$useSSL = true;
require_once(dirname(__FILE__).'/../../config/config.inc.php');
require_once(dirname(__FILE__).'/../../init.php');

include_once(dirname(__FILE__).'/megacustomers.php');

if(Tools::getValue('action'))
{
	$action = Tools::getValue('action');
	if($action=="active")
	{
		
		$activation = false;
		if ( Tools::getIsset('email') && Tools::getIsset('code') )	
		{
			$result = Db::getInstance()->ExecuteS('SELECT * FROM ' . _DB_PREFIX_ . 'customer WHERE email = "' .  pSQL(Tools::getValue('email')) . '" AND passwd = "' . pSQL(Tools::getValue('code')) . '" AND active = 0');
			if (count($result))
			{
				if (Db::getInstance()->Execute('UPDATE ' . _DB_PREFIX_ . 'customer SET active = 1 WHERE email = "' . pSQL(Tools::getValue('email')) . '" AND passwd = "' . pSQL(Tools::getValue('code')) . '" AND active = 0') )
				{
						echo 'Active Account Correctly';
				}
				else
					$this->errors[] = Tools::displayError('Activation failed');
			}
			else
					$this->errors[] = Tools::displayError('Account already activated or unknown');
		}				
	
	}
	return true;
}
if (!$cookie->isLogged())
	Tools::redirect('authentication.php?back=modules/megacustomer/mycustom.php');
		

if(Tools::isSubmit('submitEquivalence'))
{
	$id_group = (int)Configuration::get('MEGACUSTOMER_EQUIVALENCE_GROUP');
	$customer = new Customer((int)$cookie->id_customer);
	if(Tools::getValue('id_equiv')==1)
	{
		
		$customer->addGroups(array($id_group));
		$customer->id_default_group = $id_group;
		$customer->update();	
	}
	else 
	{
		$groups = $customer->getGroups();
		if($groups && sizeof($groups))
		{
			$customer->cleanGroups();
			foreach($groups as $key => $group)
			{
				if($group == $id_group)
				{
					unset($groups[$key]);
				}
			}
			if(sizeof($groups)>0)
			{
				$customer->addGroups($groups);
				$customer->id_default_group = $groups[0];
				$customer->update();	
				
			}
		}
	
	}
}	
	
$ma = new MegaCustomers();

$groups = Customer::getGroupsStatic((int)$cookie->id_customer);

$equivalence = false;
if(is_array($groups))
{
	$group = new Group((int)Configuration::get('MEGACUSTOMER_EQUIVALENCE_GROUP'));
	if(isset($group->id) AND in_array($group->id,$groups))
	{
		$equivalence = true;
	}
}

include(dirname(__FILE__).'/../../header.php');

// Smarty display
$smarty->assign(array(
	'equivalence' => $equivalence,
	
));

echo Module::display(dirname(__FILE__).'/megacustomers.php', 'tpl/myaccountoptions.tpl');


include(dirname(__FILE__).'/../../footer.php'); 