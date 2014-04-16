<?php

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');



$action = Tools::getValue('action',false);

switch($action) {
	case 'getCustomers':
		$query = Tools::getValue('q', false);
		if (!$query OR $query == '' OR strlen($query) < 1)
			die();
		if($pos = strpos($query, ' (email:'))
			$query = substr($query, 0, $pos);

		
		$items = Db::getInstance()->ExecuteS('
		SELECT `id_customer`, `email`, `firstname`, `lastname`
		FROM `'._DB_PREFIX_.'customer`
		WHERE (email LIKE \'%'.pSQL($query).'%\' OR lastname LIKE \'%'.pSQL($query).'%\')');
		if ($items)
			foreach ($items AS $item)
		echo (int)($item['id_customer']).'|'.(!empty($item['email']) ? ' ('.$item['email'].')' : '').$item['firstname'].' '.$item['lastname']."\n";

	break;
	
			
		
	
}
?>