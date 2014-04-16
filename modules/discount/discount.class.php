<?php
class UtilsDiscount
{
	public static function addModuleHook() {
		$TabModule = Db::getInstance()->ExecuteS('SELECT id_module FROM '._DB_PREFIX_.'module WHERE name = "discount"');
		Db::getInstance()->autoExecute(
			_DB_PREFIX_.'hook_module',
			array(
				'id_module'=>(int)$TabModule[0]['id_module'],
				'id_hook'=>25,
				'position'=>1
			),
			'INSERT'
		);
	}
	
	public static function createDiscount() {
		Db::getInstance()->autoExecute(
			_DB_PREFIX_.'hook_module',
			array(
				'id_module'=>(int)$TabModule[0]['id_module'],
				'id_hook'=>25,
				'position'=>1
			),
			'INSERT'
		);
	}	

	private static $array = null;
	private static $length = 8;
	public static function getRand( $len = null ){
		if( is_null( self :: $array ) )
		  self :: _genArray( );
		if( !is_numeric( $len ) )
		  $len = self :: $length;
		$passwd = '';
		for( $i = $len; $i > 0; $i-- )
		  $passwd .= self :: $array[ rand( 0, count( self :: $array ) -1 ) ];
		return $passwd;
	}
	static public function _genArray( ){
    	self :: $array = array_merge( range( 'A', 'Z' ), range( '0', '9' ) );
  	}

	
	static public function getDiscountTypes($id_lang) {
		return Db::getInstance()->ExecuteS('
		SELECT dtl.id_discount_type, dtl.name 
		FROM '._DB_PREFIX_.'discount_type dt
		LEFT JOIN `'._DB_PREFIX_.'discount_type_lang` dtl ON (dt.`id_discount_type` = dtl.`id_discount_type` AND dtl.`id_lang` = '.intval($id_lang).')');
	}
	
	static public function getCustomersDiscount($id_lang) {
		return Db::getInstance()->ExecuteS('
		SELECT *
		FROM ('._DB_PREFIX_.'customers_discount tb
		LEFT JOIN `'._DB_PREFIX_.'customers_discount_lang` tbl ON (tb.`id_customers_discount` = tbl.`id_customers_discount` AND tbl.`id_lang` = '.intval($id_lang).')) LEFT JOIN '._DB_PREFIX_.'discount_type_lang tbll ON (tb.`id_discount_type` = tbll.`id_discount_type` AND tbll.`id_lang` = '.intval($id_lang).')' );
	}	
	
	static public function getCustomersDiscountEdit($id_lang, $idCustomersDiscount) {
		return Db::getInstance()->ExecuteS('
		SELECT *
		FROM '._DB_PREFIX_.'customers_discount WHERE `id_customers_discount`='.$idCustomersDiscount );
	}	
	static public function getCustomersDiscountEditLang($id_lang, $idCustomersDiscount) {
		return Db::getInstance()->ExecuteS('
		SELECT id_lang, description 
		FROM '._DB_PREFIX_.'customers_discount_lang WHERE `id_customers_discount`='.$idCustomersDiscount );
	}	
	static public function getCustomersDiscountEditCategory($id_lang, $idCustomersDiscount) {
		return Db::getInstance()->ExecuteS('
		SELECT id_category 
		FROM '._DB_PREFIX_.'customers_discount_category WHERE `id_customers_discount`='.$idCustomersDiscount );
	}
	static public function getListDiscount($id_lang, $dateDay) {
		return Db::getInstance()->ExecuteS('
		SELECT *
		FROM '._DB_PREFIX_.'customers_discount WHERE `date_from`<="'.$dateDay.'" AND `date_to`>="'.$dateDay.'" AND `active`=1 AND offer<quantity' );
	}	
			
	public static function updateCustomersDiscount($disc_desc, $disc_type, $disc_value, $disc_quantity, $disc_cumulable, $disc_cumulable_reduction, $disc_date_from, $disc_date_to, $disc_minimal, $disc_active, $disc_category, $idCustomersDiscount, $disc_validity, $disc_name) {
		if(!is_array($disc_desc))	
		  return false;
		if(!is_array($disc_category))	
		  return false;

		Db::getInstance()->Execute('
		UPDATE '._DB_PREFIX_.'customers_discount SET 
			id_discount_type="'.$disc_type.'", 
			discount_name="'.$disc_name.'", 
			value="'.$disc_value.'",
			quantity="'.$disc_quantity.'", 
			cumulable="'.$disc_cumulable.'",
			cumulable_reduction="'.$disc_cumulable_reduction.'", 
			date_from="'.$disc_date_from.'", 
			date_to="'.$disc_date_to.'",  
			minimal="'.$disc_minimal.'", 
			active="'.$disc_active.'", 
			validity="'.$disc_validity.'" WHERE id_customers_discount='.$idCustomersDiscount.'
		');
		
		Db::getInstance()->delete(_DB_PREFIX_.'customers_discount_lang', "id_customers_discount = {$idCustomersDiscount}"); 
		foreach($disc_desc as $id_lang=>$descriptionCustomersDicscount) {
		  Db::getInstance()->autoExecute(
			_DB_PREFIX_.'customers_discount_lang',
			array(
			  'id_customers_discount'=>$idCustomersDiscount,
			  'id_lang'=>$id_lang,
			  'description'=>$descriptionCustomersDicscount
			),
			'INSERT'
		  );
		}
		
		Db::getInstance()->delete(_DB_PREFIX_.'customers_discount_category', "id_customers_discount = {$idCustomersDiscount}");
		foreach($disc_category as $id_cat=>$cat) {
		  Db::getInstance()->autoExecute(
			_DB_PREFIX_.'customers_discount_category',
			array(
			  'id_customers_discount'=>$idCustomersDiscount,
			  'id_category'=>$cat
			),
			'INSERT'
		  );
		}		
	}	

public static function addCustomersDiscount($disc_desc, $disc_type, $disc_value, $disc_quantity, $disc_cumulable, $disc_cumulable_reduction, $disc_date_from, $disc_date_to, $disc_minimal, $disc_active, $disc_category, $disc_validity, $disc_name) {
		if(!is_array($disc_desc))	
		  return false;
		if(!is_array($disc_category))	
		  return false;

		Db::getInstance()->autoExecute(
		  _DB_PREFIX_.'customers_discount',
		  array(
			'id_discount_type'=>$disc_type,
			'discount_name'=>$disc_name,
			'value'=>$disc_value,
			'quantity'=>$disc_quantity,
			'offer'=>0,
			'quantity_per_user'=>1,
			'cumulable'=>$disc_cumulable,
			'cumulable_reduction'=>$disc_cumulable_reduction,
			'date_from'=>$disc_date_from,
			'date_to'=>$disc_date_to, 
			'minimal'=>$disc_minimal,
			'active'=>$disc_active, 
			'validity'=>$disc_validity
		  ),
		  'INSERT'
		);
		$id_CustomersDiscount = Db::getInstance()->Insert_ID();
		foreach($disc_desc as $id_lang=>$descriptionCustomersDicscount) {
		  Db::getInstance()->autoExecute(
			_DB_PREFIX_.'customers_discount_lang',
			array(
			  'id_customers_discount'=>$id_CustomersDiscount,
			  'id_lang'=>$id_lang,
			  'description'=>addslashes($descriptionCustomersDicscount)
			),
			'INSERT'
		  );
		}
		foreach($disc_category as $id_cat=>$cat) {
		  Db::getInstance()->autoExecute(
			_DB_PREFIX_.'customers_discount_category',
			array(
			  'id_customers_discount'=>$id_CustomersDiscount,
			  'id_category'=>$cat
			),
			'INSERT'
		  );
		}		
	}	
		
	static public function deleteCustomersDiscount($idCustomersDiscount){
 		Db::getInstance()->delete(_DB_PREFIX_.'customers_discount', "id_customers_discount = {$idCustomersDiscount}"); 
		Db::getInstance()->delete(_DB_PREFIX_.'customers_discount_lang', "id_customers_discount = {$idCustomersDiscount}"); 
		Db::getInstance()->delete(_DB_PREFIX_.'customers_discount_category', "id_customers_discount = {$idCustomersDiscount}"); 
	}
}
?>