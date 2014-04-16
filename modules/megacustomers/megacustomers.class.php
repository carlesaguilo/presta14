<?php
/*
* 2011-2012 
*
*  @author Jorge Donet Alberola <soporte@mejorarconinternet.com> <jdonet@todoprestashop.com> 
*  V0.1
*/


class MegaCustomersClass extends ObjectModel
{
	public static function installDB()
	{
		if (!file_exists(dirname(__FILE__).'/install.sql'))
			die(Tools::displayError('File install.sql is missing'));
		elseif(!$sql = file_get_contents(dirname(__FILE__).'/install.sql'))
			die(Tools::displayError('File install.sql is not readable'));
		$sql = str_replace(array('PREFIX_', 'ENGINE_TYPE'), array(_DB_PREFIX_, _MYSQL_ENGINE_), $sql);
		$sql = preg_split("/;\s*[\r\n]+/", $sql);
		foreach ($sql as $query)
		{
			if ($query AND sizeof($query) AND !Db::getInstance()->Execute(trim($query)))
			{
				return false;
			}
		}
		return true;
	}
	public static function uninstallDB()
	{
		return $result = Db::getInstance()->Execute('DROP TABLE '._DB_PREFIX_.'megacustomers_groups');

	}
	
	
	
	
}
class MegaCustomer extends ObjectModel
{
	public $id_megacustomer;
	public $id_customer;
	public $config;

	
	protected	$fieldsRequired = array('id_customer');
	protected	$fieldsValidate = array('id_customer' => 'isUnsignedId');

	protected 	$table = 'megacustomers_customers';
	protected 	$identifier = 'id_megacustomer';

	public	function getFields()
	{
	 //	parent::validateFields(false);
	 	//$fields['id_customer'] = (int)($this->id_customer);
		$fields['id_customer'] = (int)($this->id_customer);
		$fields['config'] = base64_encode(serialize($this->config));
		return ($fields);
	}

/**
	 * Build an megaaffiliate
	 *
	 * @param integer $id_affiliate Existing affiliate id to load object (optional)
	 */
	public	function __construct($id_megacustomer = NULL)
	{
		parent::__construct($id_megacustomer);
		if(isset($this->config) && $this->config!='')
			$this->config= array_merge(self::getDefaultConfig(),unserialize(base64_decode($this->config)));
		else
			$this->config= 	self::getDefaultConfig();
	}
	public static function getMegaCustomerById($id_customer)
	{
		$sql = 'SELECT `id_megacustomer` 
				FROM `'._DB_PREFIX_.'megacustomers_customers`
				WHERE `id_customer`='.$id_customer;
											
		$id_megacustomer =  Db::getInstance()->getValue($sql);
		
		if($id_megacustomer)
		{
			return new MegaCustomer((int)$id_megacustomer);
		}
		return false;
	}
	public static function getDefaultConfig()
	{
		return array('amount_month' => 0,
									
									);
	}
	public static function getCustomers()
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT mcc.`id_megacustomer`,mcc.`id_customer`, cu.`email`, cu.`firstname`, cu.`lastname`
		FROM `'._DB_PREFIX_.'customer` cu
		INNER JOIN `'._DB_PREFIX_.'megacustomers_customers` mcc ON (mcc.`id_customer` = cu.`id_customer`)');
	}
	public function totalOrderMonth()
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT sum(total_paid)
		FROM `'._DB_PREFIX_.'orders` 
		WHERE id_customer='.$this->id_customer.' AND MONTH(`date_add`) = MONTH(CURDATE())');
	 
	}
	
	
}
class MegaCustomersGroups extends ObjectModel
{

	public $id_rule;
	public $field;
	public $reference;
	public $operator;
	public $id_group;
	
	protected	$fieldsRequired = array('id_group', 'field', 'reference');
	protected	$fieldsValidate = array('id_group' => 'isUnsignedId');

	protected 	$table = 'megacustomers_groups';
	protected 	$identifier = 'id_rule';

	public	function getFields()
	{
	 //	parent::validateFields(false);
		$fields['id_rule'] = (int)($this->id_rule);
		$fields['field'] = pSQL($this->field);
		$fields['reference'] = pSQL($this->reference);
		$fields['operator'] = pSQL($this->operator);
		$fields['id_group'] = (int)($this->id_group);
		return ($fields);
	}
	public	function __construct($id_group = NULL)
	{
		parent::__construct($id_group);
	}	
	
	
	public static function getMegaCustomersGroups()
	{
		global $cookie;	
		return Db::getInstance()->ExecuteS('SELECT mcg.*, gl.`name` FROM `'._DB_PREFIX_.'megacustomers_groups` as mcg 
											LEFT JOIN `'._DB_PREFIX_.'group_lang` as gl ON (gl.`id_group`=mcg.`id_group` AND gl.`id_lang`='.(int)$cookie->id_lang.')');
	}
	
	
}
class MegaCustomersTaxes extends ObjectModel
{

	public $id_tax_group;
	public $id_group;
	public $id_tax_rules_group;
	public $id_tax;
	
	
	protected	$fieldsRequired = array('id_group', 'id_tax_rules_group', 'tax');
	protected	$fieldsValidate = array('id_group' => 'isUnsignedId','id_tax_rules_group' => 'isUnsignedId');

	protected 	$table = 'megacustomers_taxes';
	protected 	$identifier = 'id_tax_group';

	public	function getFields()
	{
	 //	parent::validateFields(false);
		$fields['id_tax_group'] = (int)($this->id_tax_group);
		$fields['id_group'] = (int)($this->id_group);
		$fields['id_tax_rules_group'] = (int)($this->id_tax_rules_group);
		$fields['id_tax'] = (int)($this->id_tax);
		return ($fields);
	}
	public	function __construct($id_tax_group = NULL)
	{
		parent::__construct($id_tax_group);
	}	
	public static function getTaxeByProduct($id_product,$id_group)
	{
		$sql = 'SELECT mct.`id_tax` 
				FROM `'._DB_PREFIX_.'megacustomers_taxes` as mct
				LEFT JOIN `'._DB_PREFIX_.'product` as p ON (p.`id_tax_rules_group`=mct.`id_tax_rules_group`)
				WHERE p.`id_product`='.$id_product.' AND mct.`id_group`='.$id_group;
											
		$id_tax =  Db::getInstance()->getValue($sql);
		
		if($id_tax)
		{
			return new Tax((int)$id_tax);
		}
		return false;
	}
	public static function getTaxe($id_group,$id_tax_rules_group)
	{
		
		$sql = 'SELECT id_tax FROM `'._DB_PREFIX_.'megacustomers_taxes` WHERE `id_tax_rules_group`='.$id_tax_rules_group.' AND `id_group`='.$id_group;
											
		$id_tax =  Db::getInstance()->getValue($sql);
		
		if($id_tax)
		{
			return new Tax((int)$id_tax);
		}
		return false;
		
	}
	public static function getMegaCustomersTaxes()
	{
		global $cookie;	
		$sql = 'SELECT mct.*, gl.`name` as groupname, trg.`name` as grouptaxname, tl.`name` as taxname  FROM `'._DB_PREFIX_.'megacustomers_taxes` as mct 
											LEFT JOIN `'._DB_PREFIX_.'tax_rules_group` as trg ON (trg.`id_tax_rules_group`=mct.`id_tax_rules_group`)
											LEFT JOIN `'._DB_PREFIX_.'group_lang` as gl ON (gl.`id_group`=mct.`id_group` AND gl.`id_lang`='.(int)$cookie->id_lang.')
											LEFT JOIN `'._DB_PREFIX_.'tax_lang` as tl ON (tl.`id_tax`=mct.`id_tax` AND tl.`id_lang`='.(int)$cookie->id_lang.')
											';
		return Db::getInstance()->ExecuteS($sql);
	}
	
	
}

