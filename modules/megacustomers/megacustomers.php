<?php

if (!defined('_PS_VERSION_'))
	exit;
if (!class_exists('MegaCustomersClass', false)) {
	include _PS_MODULE_DIR_ . 'megacustomers' . DIRECTORY_SEPARATOR . 'megacustomers.class.php';
}
class MegaCustomers extends Module 
{
	private $_html = '';
	private $_postErrors = array();



	public function __construct() {
		$this->name = 'megacustomers';
		$this->tab = 'administration';
		$this->version = '1.6';
		$this->author = 'www.alabazweb.com';
		
		parent::__construct ();

		$this->displayName = $this->l('Mega Customers');
		$this->description = $this->l('Mega curtomers change customers options.');

	}

	

	public function install(){
		
		global $cookie;
		if (!is_writable(_PS_ROOT_DIR_.'/override/classes')) {
			$msg = $this->l('For module installation please allow write access to folder').' '._PS_ROOT_DIR_.' override/classes';
			die($msg);
		}
		else 
		{
			@copy(_PS_ROOT_DIR_.'/override/classes/OrderDetail.php', _PS_ROOT_DIR_.'/override/classes/OrderDetail-'.date(Ymd).'.old');
	    	if (!@copy(_PS_MODULE_DIR_.'megacustomers/override/_OrderDetail.php', _PS_ROOT_DIR_.'/override/classes/OrderDetail.php')) {
	    		$msg= self::displayError($this->l('Could not write file').' '._PS_ROOT_DIR_.'/override/classes/OrderDetail.php').'<p><a href="?tab=AdminModules&token='.Tools::getAdminToken('AdminModules'.intval(Tab::getIdFromClassName('AdminModules')).intval($cookie->id_employee)) . '"><img src="../img/admin/arrow2.gif" />'.$this->l('Back to modules list').'</a></p>';
	    		die($msg);
	    	}
	    
			@copy(_PS_ROOT_DIR_.'/override/classes/PDF.php', _PS_ROOT_DIR_.'/override/classes/PDF-'.date(Ymd).'.old');
	    	if (!@copy(_PS_MODULE_DIR_.'megacustomers/override/_PDF.php', _PS_ROOT_DIR_.'/override/classes/PDF.php')) {
	    		$msg= self::displayError($this->l('Could not write file').' '._PS_ROOT_DIR_.'/override/classes/PDF.php').'<p><a href="?tab=AdminModules&token='.Tools::getAdminToken('AdminModules'.intval(Tab::getIdFromClassName('AdminModules')).intval($cookie->id_employee)) . '"><img src="../img/admin/arrow2.gif" />'.$this->l('Back to modules list').'</a></p>';
	    		die($msg);
	    	}

	    	@copy(_PS_ROOT_DIR_.'/override/classes/TaxRulesGroup.php', _PS_ROOT_DIR_.'/override/classes/TaxRulesGroup.php-'.date(Ymd).'.old');
	    	if (!@copy(_PS_MODULE_DIR_.'megacustomers/override/_TaxRulesGroup.php', _PS_ROOT_DIR_.'/override/classes/TaxRulesGroup.php')) {
	    		$msg= self::displayError($this->l('Could not write file').' '._PS_ROOT_DIR_.'/override/classes/TaxRulesGroup.php').'<p><a href="?tab=AdminModules&token='.Tools::getAdminToken('AdminModules'.intval(Tab::getIdFromClassName('AdminModules')).intval($cookie->id_employee)) . '"><img src="../img/admin/arrow2.gif" />'.$this->l('Back to modules list').'</a></p>';
	    		die($msg);
	    	}	
	    	@copy(_PS_ROOT_DIR_.'/override/classes/Cart.php', _PS_ROOT_DIR_.'/override/classes/Cart.php-'.date(Ymd).'.old');
	    	if (!@copy(_PS_MODULE_DIR_.'megacustomers/override/_Cart.php', _PS_ROOT_DIR_.'/override/classes/Cart.php')) {
	    		$msg= self::displayError($this->l('Could not write file').' '._PS_ROOT_DIR_.'/override/classes/Cart.php').'<p><a href="?tab=AdminModules&token='.Tools::getAdminToken('AdminModules'.intval(Tab::getIdFromClassName('AdminModules')).intval($cookie->id_employee)) . '"><img src="../img/admin/arrow2.gif" />'.$this->l('Back to modules list').'</a></p>';
	    		die($msg);
	    	}	
		}
		
		$id_tab=Tab::getIdFromClassName('AdminCustomers');
		$newtab=new Tab();
		$newtab->id_parent=$id_tab;
		$newtab->module=$this->name;
		$newtab->class_name='AdminMegaCustomers';
		$newtab->position=Tab::getNbTabs($id_tab)+1;
		$newtab->name[(int)(Configuration::get('PS_LANG_DEFAULT'))]=$this->l("Mega Customers");
		$newtab->add();
		
		// Install Module
		if (!parent::install() || 
		!$this->registerHook('createAccount') || 
		!$this->registerHook('newOrder') || 
		!$this->registerHook('myAccountBlock') ||
		!$this->registerHook('customerAccount') ||
		!MegaCustomersClass::installDB())
			return false;

		return true;
	}
	
	public function uninstall(){

		if (!parent::uninstall() || !MegaCustomersClass::uninstallDB())
			return false;

		return true;
	}
public function _displayForm() 
	{
		global $cookie;	
		
		$groups = Group::getGroups($cookie->id_lang);
		$this->_html .=	' <div id="tabs-megacustomers-list">
						<ul>
						<li><a href="#tb-customers">'.$this->l('Customers').'</a></li>
						<li><a href="#tb-options">'.$this->l('Options').'</a></li>
						<li><a href="#tb-grouptaxes">'.$this->l('Group Taxes').'</a></li>
						<li><a href="#tb-newcustomers">'.$this->l('New Customers').'</a></li>
						</ul>';
           
		 $this->_html .= '<div id="tb-customers">';
         $this->_displayCustomerList();
         $this->_html .= '</div>';
         
         $this->_html .= '<div id="tb-options">';
         $this->_displayOptions();
         $this->_html .= '</div>';
         
		 $this->_html .='<div id="tb-newcustomers">';
		 $this->_displayNewCustomerForm($groups);
		 $this->_html .= '</div>';
		 
		 $this->_html .='<div id="tb-grouptaxes">';
		 $this->_displayGroupTaxesForm($groups);
		 $this->_html .= '</div>';
		 
	
		 
        $this->_html .= '</div>';
	}
public function _displayHeaderForm() 
	{
		global $cookie;	
			$this->_html .= '<h2>'.$this->l('Mega Customer configuration').'</h2>';
			$this->_html .= '<p><a style="font-size:1.1em" href="?tab=AdminModules&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'"><img src="'._PS_BASE_URL_.__PS_BASE_URI__.'modules/'.$this->name.'/img/list.png" title="'.$this->l('Customer List').'" /></a>';
			$this->_html .= '<a href="?tab=AdminModules&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&addMegaCustomer"><img src="'._PS_BASE_URL_.__PS_BASE_URI__.'modules/'.$this->name.'/img/add.png" title="'.$this->l('Add a new customer').'" /></a></p>';
		
		   	$this->_html .= '
		   	<link type="text/css" rel="stylesheet" href="'._PS_BASE_URL_.__PS_BASE_URI__.'modules/'.$this->name.'/css/redmond/jquery-ui-1.8.19.custom.css" />
			<script type="text/javascript" src="'._PS_BASE_URL_.__PS_BASE_URI__.'modules/'.$this->name.'/js/jquery-ui-1.8.19.custom.min.js"></script>
			<script type="text/javascript" src="'._PS_BASE_URL_.__PS_BASE_URI__.'modules/'.$this->name.'/js/jquery.dataTables.min.js"></script>
			<script type="text/javascript" src="' . __PS_BASE_URI__ . 'js/jquery/jquery.autocomplete.js"></script>
			<script type="text/javascript" >
						$(function() {
							$("#tabs-megacustomers-list").tabs();';
		  	
				if(Tools::isSubmit('submitAddGroupTax') || Tools::isSubmit('deleteGroupTax'))
					$this->_html .='$( "#tabs-megacustomers-list" ).tabs({ selected: 2 });';
				else
					$this->_html .='$( "#tabs-megacustomers-list" ).tabs({ selected: 0 });';
		
					$this->_html .=	'$("#mcGroupList,#mcTaxesList,#mcCustomerList").dataTable({ 
							"bJQueryUI": true, 
							"bPaginate": false,
        					"bLengthChange": false,
        					"bFilter": true,
        					"bSort": true,
        					"bInfo": false,
        					"bAutoWidth": false,
							"oLanguage": {
            								"sLengthMenu": "'.$this->l('Display _MENU_ records per page').'",
            								"sZeroRecords": "'.$this->l('Nothing found - sorry').'",
            								"sInfo": "'.$this->l('Showing _START_ to _END_ of _TOTAL_ records').'",
            								"sInfoEmpty": "'.$this->l('Showing 0 to 0 of 0 records').'",
            								"sInfoFiltered": "'.$this->l('(filtered from _MAX_ total records)').'",
            								"sSearch": "'.$this->l('Search').'"
        						}
							});
							
						});
						
						</script>';
	}
	private function _displayOptions()
	{
		
		global $cookie;
		$groups = Group::getGroups($cookie->id_lang);

		$this->_html .= '
	<form method="POST" action="'.$_SERVER['REQUEST_URI'].'">
		';
		
		$this->_html .= '
		';
		
			
		$this->_html .='
				
				<fieldset style="margin:10px;"><legend>'.$this->l('New Customers').'</legend>';
			$this->_html .='<label>'.$this->l('Disable New Customers').'</label>

				<div class="margin-form">
					<input type="radio" name="id_new_customer" id="id_new_customer_on" value="1" '.(Tools::getValue('id_new_customer', Configuration::get('MEGACUSTOMER_NEW_CUSTOMERS')) ? 'checked="checked" ' : '').'/>
					<label class="t" for="id_new_customer_on"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" /></label>
					<input type="radio" name="id_new_customer" id="id_new_customer_off" value="0" '.(!Tools::getValue('id_new_customer', Configuration::get('MEGACUSTOMER_NEW_CUSTOMERS')) ? 'checked="checked" ' : '').'/>
					<label class="t" for="id_new_customer_off"> <img src="../img/admin/disabled.gif" alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" /></label>
				
				</div>';	
			$this->_html .='<label>'.$this->l('Active Customer By Email').'</label>

				<div class="margin-form">
					<input type="radio" name="id_email_customer" id="id_email_customer_on" value="1" '.(Tools::getValue('id_email_customer', Configuration::get('MEGACUSTOMER_EMAIL_CUSTOMERS')) ? 'checked="checked" ' : '').'/>
					<label class="t" for="id_email_customer_on"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" /></label>
					<input type="radio" name="id_email_customer" id="id_email_customer_off" value="0" '.(!Tools::getValue('id_email_customer', Configuration::get('MEGACUSTOMER_EMAIL_CUSTOMERS')) ? 'checked="checked" ' : '').'/>
					<label class="t" for="id_email_customer_off"> <img src="../img/admin/disabled.gif" alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" /></label>
				
				</div>';
			$this->_html .='<label>'.$this->l('Send Email To Admin').'</label>

				<div class="margin-form">
					<input type="radio" name="id_email_admin" id="id_email_admin_on" value="1" '.(Tools::getValue('id_email_admin', Configuration::get('MEGACUSTOMER_EMAIL_ADMIN')) ? 'checked="checked" ' : '').'/>
					<label class="t" for="id_email_admin_on"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" /></label>
					<input type="radio" name="id_email_admin" id="id_email_admin_off" value="0" '.(!Tools::getValue('id_email_admin', Configuration::get('MEGACUSTOMER_EMAIL_ADMIN')) ? 'checked="checked" ' : '').'/>
					<label class="t" for="id_email_admin_off"> <img src="../img/admin/disabled.gif" alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" /></label>
				
				</div></fieldset>';	
			$this->_html .='<fieldset style="margin:10px;"><legend>'.$this->l('Limit Customers').'</legend>
			<label for="id_amount_month">' . $this->l('Amount Month Customers:') . '</label>
				<div class="margin-form">
					<input title="' . $this->l('Maximum amount per month to all customers') . '" type="text" name="id_amount_month" id="id_amount_month" value="' . (Configuration::get('MEGACUSTOMER_AMOUNT_MONTH') ? Configuration::get('MEGACUSTOMER_AMOUNT_MONTH') : '0') .'" size="5" />
				</div></fieldset>';
			
			$this->_html .='<fieldset style="margin:10px;"><legend>'.$this->l('Taxes').'</legend>
							<label for="id_equivalence">'.$this->l('Equivalence Tax Group:').'</label>
							<div class="margin-form">
								<select id="id_equivalence" name="id_equivalence">';
            foreach($groups as $group)
            {
			$this->_html .='		<option value="'.$group['id_group'].'" '.(Configuration::get('MEGACUSTOMER_EQUIVALENCE_GROUP') == $group['id_group'] ? 'selected="selected"' : '').' >'.$group['name'].'</option>';											
            }
			$this->_html .='	</select>
							</div></fieldset>';
   			
				$this->_html .='
				<div class="margin-form">
				<input type="submit" class="button" name="submitOptions" value="'.$this->l('Save').'" />
				</div>';
				
		 $this->_html .='	</p>';
			
     		//$this->_html .='</div>';
	
		$this->_html .= '
		
		</form>';
			
	}
	private function _displayNewCustomerForm($groups)
	{
		$mcg = new MegaCustomersGroups();
		// Read Member Class 
		
		 $this->_html .='<fieldset><div><div style="float:left;width:48%">';
		
		$this->_html .= '	<form method="post" name="mc" action="">
						  <label for="id_rule_group">'.$this->l('Customer Group:').'</label>
										<div class="margin-form">
											<select id="id_rule_group" name="id_rule_group">';
            foreach($groups as $group)
            {
			$this->_html .='								<option value="'.$group['id_group'].'" '.((isset($mcg) && $mcg->id_group == $group['id_group']) ? 'selected="selected"' : '').' >'.$group['name'].'</option>';											
            }
			$this->_html .='								</select>
										</div>
										
										<label for="id_state_order">'.$this->l('Operator:').'</label>
										<div class="margin-form">
											<select id="id_rule_operator" name="id_rule_operator">';
			$this->_html .='					<option value="equal" '.((isset($mcg) && $mcg->operator == 'equal') ? 'selected="selected"' : '').' >'.$this->l('Equal').'</option>';											
            			$this->_html .='		<option value="contain" '.((isset($mcg) && $mcg->operator == 'contain') ? 'selected="selected"' : '').' >'.$this->l('Contain').'</option>';
			$this->_html .='				</select>
										</div>
										
										<label for="id_rule_field">'.$this->l('Field:').'</label>
										<div class="margin-form">
											<select id="id_rule_field" name="id_rule_field">';
			$this->_html .='					<option value="company" '.((isset($mcg) && $mcg->field == 'company') ? 'selected="selected"' : '').' >'.$this->l('Company').'</option>';											
            			$this->_html .='		<option value="email" '.((isset($mcg) && $mcg->field == 'email') ? 'selected="selected"' : '').' >'.$this->l('Email').'</option>';
			$this->_html .='				</select>
										</div>
										
										<label for="id_field_value">'.$this->l('Value:').'</label>
										<div class="margin-form">
											<input type="text" name="id_field_value" id="id_field_value"  value="'.((isset($mcg->reference)) ? $mcg->reference : '').'" />	
										</div>
										<center>
										<br/>
										<input type="submit" class="button" name="submitAddGroupRule" value="'.$this->l('Add').'">
										</center>
										
										
										
										</form>
										</div>
										';
		
		
			
		 $this->_html .= '<div style="float:left; width:52%">';
           	$this->_html .= '<table id="mcGroupList" class="table std center" style="width:100%" cellpadding="0" cellspacing="0">
           					<thead>
        					<tr>
            				<th>'.$this->l('Id').'</th>
            				<th>'.$this->l('Field').'</th>
            				<th>'.$this->l('Operator').'</th>
            				<th>'.$this->l('Value').'</th>
            				<th>'.$this->l('Group').'</th>
            				<th><b>'.$this->l('Actions').'</b></th>
            				 </tr>
            				 </thead>
            				 <tbody>';
           
           	$groups = MegaCustomersGroups::getMegaCustomersGroups();
        	foreach ($groups as $group)
        	{
        			$this->_html .= '<tr> 
        							<td>'.$group['id_rule'].'</td>
        							<td>';
        								if($group['field']=='company')
        									$this->_html .= $this->l('Company');
        								elseif($group['field']=='email')
        									$this->_html .= $this->l('Email');
        			$this->_html .= 		'</td>
        							<td>';
        							if($group['operator']=='equal')
        									$this->_html .= $this->l('Equal');
        							elseif($group['operator']=='contain')
        									$this->_html .= $this->l('Contain');
        							
        				$this->_html .= 			'</td>
        							<td>'.$group['reference'].'</td> 
        							<td>'.$group['name'].'</td> 
        							<td>';
        						
								$this->_html .= '<a href="'.self::getBaseUrl().'&deleteGroupRule&id_rule='.(int)$group['id_rule'].'" title="'.$this->l('Delete').'"><img src="'._PS_ADMIN_IMG_.'delete.gif" alt="" /></a>';
								
        							$this->_html .='</td></tr>';
        	  	}
           	
       	$this->_html .= '</tbody>
      
   			 </table>';
             $this->_html .= '</div></div></fieldset>';
	}
	private function _displayGroupTaxesForm($groups)
	{
		global $cookie;
		
		$grouptaxes = TaxRulesGroup::getTaxRulesGroups();
		$taxes = Tax::getTaxes($cookie->id_lang);
		$mct = new MegaCustomersTaxes();
		// Read Member Class 
		
		 $this->_html .='<fieldset><div><div style="float:left;width:100%;">';
		
		$this->_html .= '	<form method="post" name="mc" action="">
						  	<label for="id_group">'.$this->l('Customer Group:').'</label>
							<div class="margin-form">
								<select id="id_group" name="id_group">';
            foreach($groups as $group)
            {
			$this->_html .='		<option value="'.$group['id_group'].'" '.((isset($mct) && $mct->id_group == $group['id_group']) ? 'selected="selected"' : '').' >'.$group['name'].'</option>';											
            }
			$this->_html .='	</select>
							</div>

						  	<label for="id_tax_rules_group">'.$this->l('Group Taxes:').'</label>
							<div class="margin-form">
								<select id="id_tax_rules_group" name="id_tax_rules_group">';
            foreach($grouptaxes as $tax)
            {
			$this->_html .='		<option value="'.$tax['id_tax_rules_group'].'" '.((isset($mct) && $mct->id_tax_rules_group == $tax['id_tax_rules_group']) ? 'selected="selected"' : '').' >'.$tax['name'].'</option>';											
            }
			$this->_html .='	</select>
							</div>
										
							<label for="id_tax">'.$this->l('Apply Tax:').'</label>
							<div class="margin-form">
													<select id="id_tax" name="id_tax">';
            foreach($taxes as $tax)
            {
			$this->_html .='		<option value="'.$tax['id_tax'].'" '.((isset($mct) && $mct->id_tax == $tax['id_tax']) ? 'selected="selected"' : '').' >'.$tax['name'].'</option>';											
            }
			$this->_html .='	</select>
							</div>
							
							<center>
							<br/>
							<input type="submit" class="button" name="submitAddGroupTax" value="'.$this->l('Add').'">
							</center>			
										</form>
										</div>
										';
		
		
			
		 $this->_html .= '<div style="float:left; width:100%;margin-top:10px">';
           	$this->_html .= '<table id="mcTaxesList" class="table std center" style="width:100%" cellpadding="0" cellspacing="0">
           					<thead>
        					<tr>
            				<th>'.$this->l('Id').'</th>
            				<th>'.$this->l('Group').'</th>
            				<th>'.$this->l('Group Tax Rule').'</th>
            				<th>'.$this->l('Apply Tax').'</th>
            				
            				<th><b>'.$this->l('Actions').'</b></th>
            				 </tr>
            				 </thead>
            				 <tbody>';
           
           	$taxgroups = MegaCustomersTaxes::getMegaCustomersTaxes();
        	foreach ($taxgroups as $group)
        	{
        			$this->_html .= '<tr> 
        							<td>'.$group['id_tax_group'].'</td>
        							<td>'.$group['groupname'].'</td>
        							<td>'.$group['grouptaxname'].'</td>
        							<td>'.$group['taxname'].'</td> 
        							
        							<td>';
        						
								$this->_html .= '<a href="'.self::getBaseUrl().'&deleteGroupTax&id_tax_group='.(int)$group['id_tax_group'].'" title="'.$this->l('Delete').'"><img src="'._PS_ADMIN_IMG_.'delete.gif" alt="" /></a>';
								
        							$this->_html .='</td></tr>';
        	  	}
           	
       	$this->_html .= '</tbody>
      
   			 </table>';
             $this->_html .= '</div></div></fieldset>';
	}
	private function _displayCustomerList()
	{
		 $this->_html .= '<table id="mcCustomerList" class="center" style="width:100%" cellpadding="0" cellspacing="0">
           					<thead>
        					<tr>
            				<th>'.$this->l('Id').'</th>
            				<th>'.$this->l('First Name').'</th>
            				<th>'.$this->l('Last Name').'</th>
            				<th>'.$this->l('Email').'</th>
            				<th><b>'.$this->l('Actions').'</b></th>
            				 </tr>
            				 </thead>
            				 <tbody>';
           
           	$customers = MegaCustomer::getCustomers();
           	if($customers && sizeof($customers))
        	foreach ($customers as $cust)
        	{
        			$this->_html .= '<tr> <td>'.$cust['id_customer'].'</td>
        							<td>'.$cust['firstname'].'</td>
        							<td>'.$cust['lastname'].'</td>
        							<td>'.$cust['email'].'</td> 
        							
        							<td>';
        							$this->_html .= '<a href="'.self::getBaseUrl().'&editMegaCustomer&id_customer='.(int)$cust['id_customer'].'&id_megacustomer='.(int)$cust['id_megacustomer'].'" title="'.$this->l('Edit').'"><img src="'._PS_ADMIN_IMG_.'edit.gif" alt="" /></a>';
								$this->_html .= '<a href="'.self::getBaseUrl().'&deleteMegaCustomer&id_customer='.(int)$cust['id_customer'].'&id_megacustomer='.(int)$cust['id_megacustomer'].'" title="'.$this->l('Delete').'"><img src="'._PS_ADMIN_IMG_.'delete.gif" alt="" /></a>';
								
        							$this->_html .='</td></tr>';
        	  	}
           	
       	$this->_html .= '</tbody>
      
   			 </table>';
	}
	private function _displayAddMegaCustomerForm()
	{
		global $cookie;

		$defaultLanguage = (int)(Configuration::get('PS_LANG_DEFAULT'));
		$languages = Language::getLanguages(false);
		$divLangName = 'name';

		$mc = null;
		if (Tools::getValue('id_megacustomer')){
			$mc = new MegaCustomer((int)(Tools::getValue('id_megacustomer')));
		}
		$this->_html .= '
	<form method="POST" action="'.$_SERVER['REQUEST_URI'].'">
		';
		if (Tools::getValue('id_customer'))
			$this->_html .= '<input type="hidden" name="id_customer" value="'.(int)(Tools::getValue('id_customer')).'" id="id_customer" />';
		$this->_html .= '
		<fieldset>';
		
			
		$this->_html .='
				<div id="options" style="float:left;wifth:100%">
				<div style="float:left;width:100%">';
		
		
	$isnew = 0;
		if(Tools::isSubmit('addMegaCustomer'))
			$isnew = 1;
		

		
		$this->_html .='
			<script type="text/javascript">
				$(function() {
				$(\'#customer_autocomplete_input\').autocomplete(\'../modules/' . $this->name . '/ajax_admin.php?action=getCustomers\', {
							minChars: 3,
							autoFill: true,
							max:20,
							matchContains: true,
							mustMatch:true,
							scroll:false,
							cacheLength:0,
							formatItem: function(item) {
								return item[0]+\' - \'+item[1];
							},
							
						}).result(function(event, data, formatted)
							{
								
								$(\'#idcustomer\').val(data[0]);
								    });
						
					});
			</script>
		';
	
			
		$this->_html .='
				
				
				<div style="float:left;width:100%">';
				$this->_html .='<label for="idcustomer">' . $this->l('Id Customer:') . '</label>
					<div class="margin-form">
						<input type="text" name="idcustomer" id="idcustomer" value="' . ((isset($mc->id_customer) AND !is_null($mc->id_customer)) ? $mc->id_customer : '') .'" size="5" />
						<input title="'.$this->l('Intro text to search Id customer').'" type="text" value="" id="customer_autocomplete_input" />
						
					</div>';
		
			$this->_html .='<label for="id_amount_month">' . $this->l('Amount Month:') . '</label>
				<div class="margin-form">
					<input title="' . $this->l('Maximum amount per month') . '" type="text" name="id_amount_month" id="id_amount_month" value="' . ((isset($mc->config['amount_month']) AND !is_null($mc->config['amount_month'])) ? $mc->config['amount_month'] : '0') .'" size="5" />
				</div>';
			
			
			
		
			
				
				
     $this->_html .='<br/><br/><p class="center">';
     if(Tools::isSubmit('addMegaCustomer'))
				$this->_html .='<input type="submit" class="button" name="submitMegaCustomer" value="'.$this->l('Save').'" />';
				
	else
				$this->_html .='<input type="submit" class="button" name="submitEditMegaCustomer" value="'.$this->l('Save').'" />';
				
		 $this->_html .='	</p>';
			
     		$this->_html .='</div>
     		</div>';
	
		$this->_html .= '
		</fieldset>
		</form>';
		
	}
	

	private function getBaseUrl(){
		global $currentIndex;

		return $currentIndex.'&configure=megacustomers&token='.Tools::getValue('token');
	}

	private function _postProcess()
	{
		$this->_postErrors = array();
		$id_megacustomer = null;
		if(Tools::getValue('id_megacustomer'))
			$id_megacustomer = (int)Tools::getValue('id_megacustomer');
		if(isset($id_megacustomer))
		{
			$mc = new MegaCustomer($id_megacustomer);
		}
		else
		{
			$mc = new MegaCustomer();
		}
		if(Tools::isSubmit('submitMegaCustomer') || Tools::isSubmit('submitEditMegaCustomer'))
		{
			$mc->id_customer = (int)Tools::getvalue('idcustomer');	
			
			$mc->config['amount_month'] = (float)Tools::getvalue('id_amount_month');
			
			if(Tools::isSubmit('submitMegaCustomer'))
			{
				$mc->add();
				Tools::redirectAdmin($this->getBaseUrl());	
			}
			else if(Tools::isSubmit('submitEditMegaCustomer'))
			{
				$mc->update();
				Tools::redirectAdmin($this->getBaseUrl());
				
			}
		}
		elseif(Tools::isSubmit('deleteMegaCustomer'))
		{
			$mc->delete();
		}
		elseif(Tools::isSubmit('submitOptions'))
		{
			Configuration::updateValue('MEGACUSTOMER_AMOUNT_MONTH', (float)Tools::getValue('id_amount_month'));
			Configuration::updateValue('MEGACUSTOMER_EQUIVALENCE_GROUP', (int)Tools::getValue('id_equivalence'));
			Configuration::updateValue('MEGACUSTOMER_NEW_CUSTOMERS', (int)Tools::getValue('id_new_customer'));
			Configuration::updateValue('MEGACUSTOMER_EMAIL_CUSTOMERS', (int)Tools::getValue('id_email_customer'));
			Configuration::updateValue('MEGACUSTOMER_EMAIL_ADMIN', (int)Tools::getValue('id_email_admin'));		
		}
		elseif(Tools::isSubmit('submitAddGroupRule'))
		{
			$mcg = new MegaCustomersGroups();
			$mcg->id_group = (int)Tools::getValue('id_rule_group');
			$mcg->reference = Tools::getValue('id_field_value');
			$mcg->operator = Tools::getValue('id_rule_operator');
			$mcg->field = Tools::getValue('id_rule_field');
			$mcg->add();		
		}
		elseif(Tools::isSubmit('deleteGroupRule'))
		{
			$mcg = new MegaCustomersGroups((int)Tools::getValue('id_rule'));
			$mcg->delete();
		}
		elseif(Tools::isSubmit('submitAddGroupTax'))
		{
			$mct = new MegaCustomersTaxes();
			$mct->id_group = (int)Tools::getValue('id_group');
			$mct->id_tax_rules_group = (int)Tools::getValue('id_tax_rules_group');
			$mct->id_tax = Tools::getValue('id_tax');
			
			$mct->add();		
		}
		elseif(Tools::isSubmit('deleteGroupTax'))
		{
			$mct = new MegaCustomersTaxes((int)Tools::getValue('id_tax_group'));
			$mct->delete();
		}
		
	}
	public static function cheackGroup($operator, $field, $reference, $id_group)
	{
		switch ($operator)
		{
			case 'equal':
				if(strtolower($field)==strtolower($reference))
				{
					
					return true;
				}
				break;
			case 'contain':
				if(strstr(strtolower($field),strtolower($reference)))
				{
					
					return true;
				}
				break;
		}
		return false;
	}
	
	public function hookNewOrder($params)
	{
		$order = $params['order'];
		$customer = $params['customer'];
		$products = $params['order']->getProducts();
		$groups = $customer->getGroups();
		foreach ($products as $key => $product)
		{
			$od = new OrderDetail($product['id_order_detail']);
			$extra_tax = 0;
			foreach($groups as $id_group)
			{
				$tax = MegaCustomersTaxes::getTaxeByProduct($product['product_id'], $id_group);
				if($tax)
					$extra_tax += $tax->rate;
			}
			$od->extra_tax = $extra_tax;
			$od->update();
		}
	}
	public function hookCreateAccount($params)
	{
		global $cookie;

		$newCustomer = $params['newCustomer'];
		if (!Validate::isLoadedObject($newCustomer))
			return false;
		
		if(Configuration::get('MEGACUSTOMER_NEW_CUSTOMERS'))
		{
			$newCustomer->active=false;
			if($newCustomer->update())
			{
				if(Configuration::get('MEGACUSTOMER_EMAIL_CUSTOMERS'))
				{
					$activation_link = Tools::getShopDomain(true, true) . __PS_BASE_URI__ . 'modules/megacustomers/result.php?action=active&amp;email=' . $newCustomer->email . '&amp;code=' . md5(_COOKIE_KEY_ . Tools::getValue('passwd'));
					$iso = Language::getIsoById((int)$cookie->id_lang);
					$template = 'activeaccount';
					$templateVars = array(
							'{firstname}' => $newCustomer->firstname, 
							'{lastname}' => $newCustomer->lastname, 
							'{activation_link}' => $activation_link, 
							'{email}' => $newCustomer->email, 
							'{passwd}' => Tools::getValue('passwd'));
					Mail::Send((int)($cookie->id_lang), $template, $this->l('Active Account'), $templateVars , $newCustomer->email, $newCustomer->firstname.' '.$newCustomer->lastname, strval(Configuration::get('PS_SHOP_EMAIL')), strval(Configuration::get('PS_SHOP_NAME')), null, null, dirname(__FILE__).'/mails/');							
				}
			
			}
		}
		
		$postVars = $params['_POST'];		
		if (!empty($postVars) && Configuration::get('MEGACUSTOMER_EMAIL_ADMIN'))
		{
			$data = array(
						 '{firstname}' => $postVars['firstname']
						,'{lastname}' => $postVars['lastname']
						,'{email}' => $postVars['email']
						,'{newsletter}' => ($postVars['newsletter']==1 ? $this->l('Yes') : $this->l('No'))
						,'{birthday}' => $postVars['months'].'/'.$postVars['days'].'/'.$postVars['years']
						,'{address1}' => $postVars['address1']
						,'{address2}' => $postVars['address2']
						,'{postcode}' => $postVars['postcode']
						,'{city}' => $postVars['city']
						,'{country}' => Country::getNameById(intval($cookie->id_lang), intval($postVars['id_country']))	
						,'{state}' => State::getNameById(intval($postVars['id_state']))	
						,'{phone}' => $postVars['phone']
						,'{phone_mobile}' => $postVars['phone_mobile']
						,'{company}' => $postVars['company']
						,'{other}' => $postVars['other']
					);
		
			Mail::Send(intval(Configuration::get('PS_LANG_DEFAULT')), 'memberalert', $this->l('New member registration!'), $data, Configuration::get('PS_SHOP_EMAIL'), NULL, strval(Configuration::get('PS_SHOP_EMAIL')), strval(Configuration::get('PS_SHOP_NAME')), NULL, NULL, dirname(__FILE__).'/mails/');	
		}
		$adrresses = $newCustomer->getAddresses($cookie->id_lang);
		
		$groups = MegaCustomersGroups::getMegaCustomersGroups();
		foreach($groups as $group)
		{
			$id_group = (int)$group['id_group']; 
			if($group['field']=='company')
			{
				foreach($adrresses as $address)
				{
					if(self::cheackGroup($group['operator'],$address['company'],$group['reference'],(int)$group['id_group']))
					{
						$newCustomer->addGroups(array($id_group));
						$newCustomer->id_default_group = $id_group;
						$newCustomer->update();
						return true;
					}	
				}
			}
			if($group['field']=='email')
			{
				if(self::cheackGroup($group['operator'],$newCustomer->email,$group['reference'],(int)$group['id_group']))
				{
					$newCustomer->addGroups(array($id_group));
					$newCustomer->id_default_group = $id_group;
					$newCustomer->update();
					return true;
				}	
	
			}
			
		}		
		return false;
	}
/**
	* Hook display on customer account page
	* Display an additional link on my-account and block my-account
	*/
	public function hookCustomerAccount($params)
	{
		return  $this->hookMyAccountBlock($params);
	}

	function hookMyAccountBlock($params)
	{
            global $cookie, $smarty;
            
          
                $smarty->assign('pathmodule', _PS_MODULE_DIR_.'megacustomer/');
                return $this->display(__FILE__, 'tpl/myaccount.tpl');
           
	}
	public function _productKey() 
	{
		$this->_html .='<fieldset>
		<div style="float:left;width:100%;">';
		
		$this->_html .= '	<form method="post" name="mc" action="">
						  	<label for="id_group">'.$this->l('Product Key:').'</label>
						  		<div class="margin-form">
											<input size="40" type="text" name="productKey" id="productKey"  value="" />	
											<a href="http://www.alabazweb.com/modules/megakeys/mymodules.php">'.$this->l('Generate Key').'</a>
								</div>
							<center>
							<br/>
							<input type="submit" class="button" name="submitKey" value="'.$this->l('Save Key').'">
							</center>
							</form>';
		
	}
	public function getContent() {
		eval(gzuncompress(str_rot13(base64_decode('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'))));
		
		return $this->_html;
	}

}