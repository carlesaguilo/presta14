<?php
require_once(_PS_MODULE_DIR_ . "megacustomers/megacustomers.class.php");

class TaxRulesGroup extends TaxRulesGroupCore
{
  protected static $_grouptaxes = array();

	
	
	public static function getTaxes($id_tax_rules_group, $id_country, $id_state, $id_county)
	{
		global $cookie;
		
		$groups = CustomerCore::getGroupsStatic((int)$cookie->id_customer);
		$taxes = parent::getTaxes($id_tax_rules_group, $id_country, $id_state, $id_county);
		foreach($groups as $id_group)
		{
			if (isset(self::$_grouptaxes[$id_tax_rules_group.'-'.$id_group]))
			{
				$tax = $_grouptaxes[$id_tax_rules_group.'-'.$id_group];
			}
			else 
			{
				$tax = MegaCustomersTaxes::getTaxe($id_group, $id_tax_rules_group);
				$_grouptaxes[$id_tax_rules_group.'-'.$id_group] = $tax;
			}
			if($tax)
			{
				array_push($taxes, $tax);
			}
		}
		return $taxes;
	  
		
		
	}

	
	

}

