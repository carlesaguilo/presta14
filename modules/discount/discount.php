<?php
include _PS_MODULE_DIR_.'discount/discount.class.php';
class Discount extends Module {
	private $_pageHtml = '';
	
	public function __construct() {
		$this->name = 'discount';
		$this->tab = 'Tools';
		$this->version = 1.4;
		parent::__construct();
		$this->page = basename(__FILE__, '.php');
		$this->displayName = $this->l('Discount for new customers');
		$this->description = $this->l('The best way to offer Discounts.');
	}
	
	public function install() {
		//UtilsDiscount::addModuleHook();//
		if(!parent::install() || 
		   !$this->registerHook('createAccount') || 
		   !Configuration::updateValue('MOD_TOOLSDISCOUNT_ACTIVE', '1') || 
		   !Configuration::updateValue('MOD_TOOLSDISCOUNT_MAIL', 'on') || 
		   !$this->installDB())
			return false;
		else
			return true;
	}
	
	public function installDb() {
		Db::getInstance()->ExecuteS('
		CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'customers_discount` (
		  `id_customers_discount` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
		  `discount_name` VARCHAR(32) NULL,
		  `id_discount_type` TINYINT( 1 ) NOT NULL,
		  `value` FLOAT( 9,2 ) NOT NULL, 
		  `quantity` INT NOT NULL, 
		  `offer` INT NOT NULL, 
		  `quantity_per_user` INT NOT NULL, 
		  `cumulable` INT NOT NULL, 
		  `cumulable_reduction` INT NOT NULL, 
		  `date_from` DATETIME NULL, 
		  `date_to` DATETIME NULL, 
		  `minimal` INT NOT NULL,
		  `validity` INT NOT NULL,
		  `active` TINYINT( 1 ) NOT NULL
		) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci;');
		Db::getInstance()->ExecuteS('
		CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'customers_discount_lang` (
		  `id_customers_discount` INT NOT NULL ,
		  `id_lang` TINYINT( 1 ) NOT NULL,
		  `description` VARCHAR( 128 ) NOT NULL
		) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci;');
		Db::getInstance()->ExecuteS('
		CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'customers_discount_category` (
		  `id_customers_discount` INT  NOT NULL,
		  `id_category` INT NOT NULL
		) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci;');	
		Db::getInstance()->ExecuteS('
		CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'customers_discount_affect` (
		  `id_customers_discount` INT NOT NULL,
		  `id_discount` INT NOT NULL
		) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci;');		
		return true;
	}
	
	public function uninstall() {
		if(!parent::uninstall() || 
		   !Configuration::deleteByName('MOD_TOOLSDISCOUNT_ACTIVE') || 
		   !Configuration::deleteByName('MOD_TOOLSDISCOUNT_MAIL') ||
		   !$this->uninstallDb())
		  return false;
		return true;
	}
	
	private function uninstallDb() {
		Db::getInstance()->ExecuteS('DROP TABLE `'._DB_PREFIX_.'customers_discount`');
		Db::getInstance()->ExecuteS('DROP TABLE `'._DB_PREFIX_.'customers_discount_lang`');
		Db::getInstance()->ExecuteS('DROP TABLE `'._DB_PREFIX_.'customers_discount_category`');	
		Db::getInstance()->ExecuteS('DROP TABLE `'._DB_PREFIX_.'customers_discount_affect`');	
		return true;
	}

	private function recurseCategory($indexedCategories, $categories, $current, $id_category = 1, $id_category_default = NULL, $CategorySelected)
	{
		global $done;
		static $irow;
		if (!isset($done[$current['infos']['id_parent']]))
			$done[$current['infos']['id_parent']] = 0;
		$done[$current['infos']['id_parent']] += 1;
	
		$todo = sizeof($categories[$current['infos']['id_parent']]);
		$doneC = $done[$current['infos']['id_parent']];
	
		$level = $current['infos']['level_depth'] + 1;
		$img = $level == 1 ? 'lv1.gif' : 'lv'.$level.'_'.($todo == $doneC ? 'f' : 'b').'.gif';
	
		$this->_pageHtml .= '
		<tr class="'.($irow++ % 2 ? 'alt_row' : '').'">
			<td>
				<input type="checkbox" name="categoryBox[]" class="categoryBox'.($id_category_default != NULL ? ' id_category_default' : '').'" id="categoryBox_'.$id_category.'" value="'.$id_category.'"'.(((in_array($id_category, $indexedCategories) OR (intval(Tools::getValue('id_category')) == $id_category AND !intval($id_obj))) OR Tools::getIsset('adddiscount') OR in_array($id_category, $CategorySelected)) ? ' checked="checked"' : '').' />
			</td>
			<td>
				'.$id_category.'
			</td>
			<td>
				<img src="../img/admin/'.$img.'" alt="" /> &nbsp;<label for="categoryBox_'.$id_category.'" class="t">'.stripslashes(Category::hideCategoryPosition($current['infos']['name'])).'</label>
			</td>
		</tr>';
	
		if (isset($categories[$id_category]))
			foreach ($categories[$id_category] AS $key => $row)
				if ($key != 'infos')
					$this->recurseCategory($indexedCategories, $categories, $categories[$id_category][$key], $key, NULL, $CategorySelected);
	}

  public function getContent()
  {
    global $cookie;
  	$defaultLanguage = intval(Configuration::get('PS_LANG_DEFAULT'));
	$languages = Language::getLanguages();
	$iso = Language::getIsoById($defaultLanguage);

	includeDatepicker(array('date_from', 'date_to'), true);
	$index = array();
	$indexedCategories =  isset($_POST['categoryBox']) ? $_POST['categoryBox'] : ($obj->id ? Discount::getCategories($obj->id) : array());
	$categories = Category::getCategories(intval($cookie->id_lang), false);
	foreach ($indexedCategories AS $k => $row)
		$index[] = $row['id_category'];
	$p = 0;
	$errors = array();
	$editInProgress = 0;
	$ErrorAdd = 0;
	if(Tools::isSubmit('actionForm'))
    {		
		switch(trim(Tools::getValue('actionForm'))) {
			case 'add':
				(Tools::getValue('id_discounttype') == '') ? $errors[$p++] = $this->l('type') : false;
				(Tools::getValue('categoryBox') == '') ? $errors[$p++] = $this->l('category') : false;
				(Tools::getValue('value') == '') ? $errors[$p++] = $this->l('value') : false;
				(Tools::getValue('quantity') == '') ? $errors[$p++] = $this->l('quantity') : false;
				(Tools::getValue('validity') == '') ? $errors[$p++] = $this->l('validity') : false;
				if(count($errors)!=0) {
					$this->_pageHtml .= $this->displayError($this->l('Unable to add this discount.'));
					$ErrorAdd = 1;
				}
				else {
					UtilsDiscount::addCustomersDiscount(
						Tools::getValue('desc'),
						Tools::getValue('id_discounttype'),
						Tools::getValue('value'),
						Tools::getValue('quantity'),
						Tools::getValue('cumulable')*1,
						Tools::getValue('cumulable_reduction'),
						Tools::getValue('date_from'),
						Tools::getValue('date_to'),
						Tools::getValue('minimal'),
						Tools::getValue('activeCustomersDiscount'), 
						Tools::getValue('categoryBox'),
						Tools::getValue('validity'), 
						Tools::getValue('discount_name')
					);
					$this->_pageHtml .= $this->displayConfirmation($this->l('The discount has been added'));
				}
			break;
			case 'delete':
				UtilsDiscount::deleteCustomersDiscount(Tools::getValue('DiscountToEditDelete'));
				$this->_pageHtml .= $this->displayConfirmation($this->l('The discount has been deleted'));		
			break;
			case 'edit':
				$ErrorAdd = 0;
				$editInProgress = 1;
				$CustomersDiscountEdit 			= UtilsDiscount::getCustomersDiscountEdit(intval($cookie->id_lang), Tools::getValue('DiscountToEditDelete'));
				$CustomersDiscountEditLang 		= UtilsDiscount::getCustomersDiscountEditLang(intval($cookie->id_lang), Tools::getValue('DiscountToEditDelete'));
				$CustomersDiscountEditCategory 	= UtilsDiscount::getCustomersDiscountEditCategory(intval($cookie->id_lang), Tools::getValue('DiscountToEditDelete'));
			break;
			case 'update':
				(Tools::getValue('id_discounttype') == '') ? $errors[$p++] = $this->l('type') : false;
				(Tools::getValue('categoryBox') == '') ? $errors[$p++] = $this->l('category') : false;
				(Tools::getValue('value') == '') ? $errors[$p++] = $this->l('value') : false;
				(Tools::getValue('quantity') == '') ? $errors[$p++] = $this->l('quantity') : false;
				(Tools::getValue('validity') == '') ? $errors[$p++] = $this->l('validity') : false;
				if(count($errors)!=0) {
					$this->_pageHtml .= $this->displayError($this->l('Unable to add this discount.'.count($errors)));
				}
				else {	
					UtilsDiscount::updateCustomersDiscount(
							Tools::getValue('desc'),
							Tools::getValue('id_discounttype')*1,
							Tools::getValue('value')*1,
							Tools::getValue('quantity')*1,
							Tools::getValue('cumulable')*1,
							Tools::getValue('cumulable_reduction')*1,
							Tools::getValue('date_from'),
							Tools::getValue('date_to'),
							Tools::getValue('minimal')*1,
							Tools::getValue('activeCustomersDiscount')*1, 
							Tools::getValue('categoryBox'), 
							Tools::getValue('idDiscountToUpdate'), 
							Tools::getValue('validity'), 
							Tools::getValue('discount_name')
					);
					$this->_pageHtml .= $this->displayConfirmation($this->l('The discount has been updated'));			
				}
			break;
			case 'updateMail':
				if (isset($_POST['submitUpdateMail'])) {
					if($this->putUpdatedMail()) {
						Configuration::updateValue('MOD_TOOLSDISCOUNT_MAIL', Tools::getValue('active_mail'));
						$this->_pageHtml .= $this->displayConfirmation($this->l('Mail updated'));
					}
					else {
						$this->_pageHtml .= $this->displayError($this->l('Unable to update mail'));
					}	
				}
			break;
			default:
				$this->_pageHtml .= $this->displayConfirmation($this->l('Error'));
			break;
		}
	}
	else {
		
	}
	$tab_category = array();
	$tab_descriptions = array();
	if(count($CustomersDiscountEditLang)!=0)
		foreach($CustomersDiscountEditLang as $k)
				$tab_descriptions[$k['id_lang']] = $k['description'];
	if(count($CustomersDiscountEditCategory)!=0)
		foreach($CustomersDiscountEditCategory as $k)
				$tab_category[$k['id_category']] = $k['id_category'];
	$discountTypes = UtilsDiscount::getDiscountTypes(intval($cookie->id_lang));
	$this->_pageHtml .= '
	<script type="text/javascript" src="'.__PS_BASE_URI__.'js/tinymce/jscripts/tiny_mce/jquery.tinymce.js"></script>
		<script type="text/javascript">
		function tinyMCEInit(element)
		{
			$().ready(function() {
				$(element).tinymce({
					// Location of TinyMCE script
					script_url : \''.__PS_BASE_URI__.'js/tinymce/jscripts/tiny_mce/tiny_mce.js\',
					// General options
					theme : "advanced",
					plugins : "safari,pagebreak,style,layer,table,advimage,advlink,inlinepopups,preview,media,searchreplace,contextmenu,paste,directionality,fullscreen",
					// Theme options
					theme_advanced_buttons1 : "newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
					theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
					theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,media,|,|,ltr,rtl,|,fullscreen",
					theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,pagebreak",
					theme_advanced_toolbar_location : "top",
					theme_advanced_toolbar_align : "left",
					theme_advanced_statusbar_location : "bottom",
					theme_advanced_resizing : true,
					content_css : "'.__PS_BASE_URI__.'themes/'._THEME_NAME_.'/css/global.css",
					// Drop lists for link/image/media/template dialogs
					template_external_list_url : "lists/template_list.js",
					external_link_list_url : "lists/link_list.js",
					external_image_list_url : "lists/image_list.js",
					media_external_list_url : "lists/media_list.js"
				});
			});
		}
		tinyMCEInit(\'textarea.rte\');
		function AutomaticClick() {
			if(document.getElementById("automaticName").checked==true) {
				document.getElementById("discount_name").disabled = true
				document.getElementById("discount_name").value = ""
			}
			else {
				document.getElementById("discount_name").disabled = false
			}
		}
		</script>
	<script type="text/javascript">id_language = Number('.$defaultLanguage.');</script>
	<script type="text/javascript" src="../modules/discount/js/utils.js"></script>
	<h2>'.$this->displayName.'</h2>
	<form method="post" action="'.$_SERVER['REQUEST_URI'].'" name="FormCustomersDiscount" id="FormCustomersDiscount">';
	$CustomersDiscount = UtilsDiscount::getCustomersDiscount(intval($cookie->id_lang));
	$this->_pageHtml .='<fieldset>
		<legend><img src="'.$this->_path.'logo.gif">'.$this->l('Discount list for new customers').'</legend>
			<table cellpadding="0" class="table" width="100%">
				<TR>
					<Th width="20">'.$this->l('ID').'</Th>
					<th>'.$this->l('Description').'</th>
					<th>'.$this->l('Type').'</th>
					<th>'.$this->l('Value').'</th>
					<th>'.$this->l('Qty').'</th>
					<th>'.$this->l('Left').'</th>
					<th width="20">'.$this->l('Status').'</th>
					<th width="40" colspan="2">'.$this->l('Actions').'</th>
				</TR>
				';
		$lng = 0;
		if(count($CustomersDiscount)!=0)
			foreach ($CustomersDiscount as $CDiscount) {	
				$this->_pageHtml .= '<TR class="'.($lng++ % 2 ? 'alt_row' : '').'"><TD>'.$CDiscount['id_customers_discount'].'</TD><TD>'.$CDiscount['description'].'</TD><TD>'.$CDiscount['name'].'</TD><TD>'.$CDiscount['value'].'</TD><TD width="75">'.$CDiscount['quantity'].'</TD><TD width="75">'.($CDiscount['quantity'] - $CDiscount['offer']).'</TD><TD align="center">'.(($CDiscount['active']==1) ? '<img src="../img/admin/enabled.gif">' : '<img src="../img/admin/disabled.gif">').'</TD><TD width="20"><img src="../img/admin/edit.gif" title="'.$this->l('Edit').'" onClick="editDiscount(\''.$CDiscount['id_customers_discount'].'\')" style="cursor:pointer"></TD><TD width="20"><img src="../img/admin/disabled.gif" title="'.$this->l('Delete').'" onClick="deleteDiscount(\''.$CDiscount['id_customers_discount'].'\')" style="cursor:pointer"></TD></TR>';
				}
	$this->_pageHtml .='</table>';
	$this->_pageHtml .='<input type="hidden" id="DiscountToEditDelete" name="DiscountToEditDelete" value="">';
	$this->_pageHtml .='</fieldset><BR />';
	
	$this->_pageHtml .='<fieldset>
		<legend><img src="../img/admin/add.gif">'.$this->l('Add or Update discount for new customers').'</legend>
				<label>'.$this->l('Type:').' </label>
				<div class="margin-form">
				<select name="id_discounttype" id="id_discounttype">';
		foreach ($discountTypes AS $discountType)
			$this->_pageHtml .='<option value="'.intval($discountType['id_discount_type']).'" "'.(($editInProgress==1) ? (intval($CustomersDiscountEdit[0]['id_discount_type'])==intval($discountType['id_discount_type']) ? " selected" : "") : (intval(Tools::getValue('id_discounttype'))==intval($discountType['id_discount_type']) ? (($ErrorAdd==1) ? " selected": "") : "")).'">'.$discountType['name'].'</option>';
		$this->_pageHtml .='
				</select>		
				</div>
				<label>'.$this->l('Categories:').' </label>
					<div class="margin-form">
							<table cellspacing="0" cellpadding="0" class="table" width="100%">
									<tr>
										<th width="20"><input type="checkbox" name="checkme" class="noborder" onclick="checkDelBoxes(this.form, \'categoryBox[]\', this.checked)" /></th>
										<th width="20">'.$this->l('ID').'</th>
										<th>'.$this->l('Name').'</th>
									</tr>';
		$this->recurseCategory($index, $categories, $categories[0][1], 1, $obj->id, $tab_category);
		$this->_pageHtml .='
							</table>
							<p style="padding:0px; margin:0px 0px 10px 0px;">'.$this->l('Mark all checkbox(es) of categories to which the discount is to be applicated').'<sup> *</sup></p>
						</div>				
				<label>'.$this->l('Description:').' </label>
				<div class="margin-form">';
				foreach ($languages as $language) {
					$description = Tools::getValue('desc');
					$this->_pageHtml .= '
					<div id="desc_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').'; float: left;">
						<input size="60" type="text" name="desc['.$language['id_lang'].']" id="desc_'.$language['id_lang'].'" value="'.(($editInProgress==1) ? $tab_descriptions[$language['id_lang']] : (($ErrorAdd==1) ? $description[$language['id_lang']] : "")).'" /><sup> *</sup>
						<span class="hint" name="help_box">'.$this->l('Invalid characters:').' <>;=#{}<span class="hint-pointer">&nbsp;</span></span>
						<p style="clear: both;">'.$this->l('Will appear in cart next to voucher code').'</p>
					</div>';				
				}
				$this->_pageHtml .= $this->displayFlags($languages, $defaultLanguage, 'desc', 'desc', true);		
	$this->_pageHtml .='
				</div><BR /><BR /><BR />
				<label>'.$this->l('Value:').' </label>
				<div class="margin-form">
					<input type="text" size="15" name="value" id="valeur_discount" value="'.(($editInProgress==1) ? $CustomersDiscountEdit[0]['value'] : (($ErrorAdd==1) ? Tools::getValue('value') : "")).'" onKeyUp="javascript:this.value = this.value.replace(/,/g, \'.\'); " /> <sup>*</sup>
					<p style="clear: both;">'.$this->l('Either the monetary amount or the %, depending on Type selected above').'</p>
				</div>';
	$this->_pageHtml .='<label>'.$this->l('Total quantity:').' </label>
				<div class="margin-form">
					<input type="text" size="15" name="quantity" value="'.(($editInProgress==1) ? $CustomersDiscountEdit[0]['quantity'] : (($ErrorAdd==1) ? Tools::getValue('quantity') : "")).'" /> <sup>*</sup>
					<p style="clear: both;">'.$this->l('Total quantity available (mainly for vouchers open to everyone)').'</p>
				</div>
				<label>'.$this->l('Minimum amount').'</label>
				<div class="margin-form">
					<input type="text" size="15" name="minimal" value="'.(($editInProgress==1) ? $CustomersDiscountEdit[0]['minimal'] : (($ErrorAdd==1) ? Tools::getValue('minimal') : "")).'" onkeyup="javascript:this.value = this.value.replace(/,/g, \'.\'); " /> <sup>*</sup>
					<p style="clear: both;">'.$this->l('Leave blank or 0 if not applicable').'</p>
				</div>
				<label>'.$this->l('Validity').'</label>
				<div class="margin-form">
					<input type="text" size="15" name="validity" value="'.(($editInProgress==1) ? $CustomersDiscountEdit[0]['validity'] : (($ErrorAdd==1) ? Tools::getValue('validity') : "")).'" onkeyup="javascript:this.value = this.value.replace(/,/g, \'.\'); " /> <sup>*</sup>
					<p style="clear: both;">'.$this->l('Discount Validity in number of days').'</p>
				</div>				
				<div class="margin-form">
					<p>
						<input type="checkbox" name="cumulable" id="cumulable_on" value="1" '.(($editInProgress==1) ? (($CustomersDiscountEdit[0]['cumulable']=='1') ? " checked" : "") : ((Tools::getValue('cumulable')=='1') ? (($ErrorAdd==1) ? " checked" : "") : "")).'/>
						<label class="t" for="cumulable_on"> '.$this->l('Cumulative with other vouchers').'</label>
					</p>
				</div>
				<div class="margin-form">
					<p>
						<input type="checkbox" name="cumulable_reduction" id="cumulable_reduction_on" value="1" '.(($editInProgress==1) ? (($CustomersDiscountEdit[0]['cumulable_reduction']=='1') ? " checked" : "") : ((Tools::getValue('cumulable_reduction')=='1') ? (($ErrorAdd==1) ? " checked" : "") : "")).'/>
						<label class="t" for="cumulable_reduction_on"> '.$this->l('Cumulative with price reductions').'</label>
					</p>
				</div>';	
	$this->_pageHtml .='
				<label>'.$this->l('From:').' </label>
				<div class="margin-form">
					<input type="text" size="20" id="date_from" name="date_from" value="'.(($editInProgress==1) ? $CustomersDiscountEdit[0]['date_from'] : (($ErrorAdd==1) ? Tools::getValue('date_from') : "")).'" /> <sup>*</sup>
					<p style="clear: both;">'.$this->l('Start date/time from which voucher can be used').'<br />'.$this->l('Format: YYYY-MM-DD HH:MM:SS').'</p>
				</div>
				<label>'.$this->l('To:').' </label>
				<div class="margin-form">
					<input type="text" size="20" id="date_to" name="date_to" value="'.(($editInProgress==1) ? $CustomersDiscountEdit[0]['date_to'] : (($ErrorAdd==1) ? Tools::getValue('date_to') : "")).'" /> <sup>*</sup>
					<p style="clear: both;">'.$this->l('End date/time at which voucher is no longer valid').'<br />'.$this->l('Format: YYYY-MM-DD HH:MM:SS').'</p>
				</div>
				<label>'.$this->l('Status:').' </label>
				<div class="margin-form">
					<input type="radio" name="activeCustomersDiscount" id="active_on_customersDiscount" value="1" '.(($editInProgress==1) ? (($CustomersDiscountEdit[0]['active']==1) ? " checked" : "") : ((Tools::getValue('activeCustomersDiscount')=='1') ? (($ErrorAdd==1) ? " checked" : "") : "")).'/>
					<label class="t" for="active_on"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" /></label>
					<input type="radio" name="activeCustomersDiscount" id="active_off" value="0" '.(($editInProgress==1) ? (($CustomersDiscountEdit[0]['active']==0) ? " checked" : "") : ((Tools::getValue('activeCustomersDiscount')=='0') ? (($ErrorAdd==1) ? " checked" : "") : "")).'/>
					<label class="t" for="active_off"> <img src="../img/admin/disabled.gif" alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" /></label>
					<p>'.$this->l('Enable or disable voucher').'</p>
				</div>
				<div class="margin-form">';
					if($editInProgress==1) {
					$this->_pageHtml .= '<input type="button" value="'.$this->l('   Update   ').'" name="submitAddCustomersDiscount" class="button" onClick="updateDiscount(\''.$CustomersDiscountEdit[0]['id_customers_discount'].'\')" />
					<input type="hidden" id="idDiscountToUpdate" name="idDiscountToUpdate" value="'.$CustomersDiscountEdit[0]['id_customers_discount'].'">';
					}
					else
					$this->_pageHtml .= '<input type="submit" value="'.$this->l('   Save   ').'" name="submitAddCustomersDiscount" class="button" />';
				$this->_pageHtml .= '</div>
				<div class="small"><sup>*</sup> '.$this->l('Required field').'</div>
';							
	$this->_pageHtml .='</fieldset>
	<input type="hidden" id="actionForm" name="actionForm" value="add">
	</form>';
	$this->_pageHtml .= '<BR />
	<form method="post" action="'.$_SERVER['REQUEST_URI'].'">
	<fieldset>
	<legend><img src="'.$this->_path.'img/mail.gif">'.$this->l('Email information').'</legend>
	<label style="margin-top : -6px">'.$this->l('Send mail').'</label>
	<div class="margin-form">
	  <input type="checkbox" name="active_mail" '.((Configuration::get('MOD_TOOLSDISCOUNT_MAIL')=='on') ? ' checked=""': '').'/>
	</div>				
	<label>'.$this->l('Mail').'</label>
	<div class="margin-form">';
	foreach ($languages as $language)
				{
                    $existing_file = stripslashes($this->_getMailContent($language)) ;
					$this->_pageHtml .= '
					<div id="InformMail_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').';float: left;">
						<textarea class="rte" cols="70" rows="30" id="body_mail_'.$language['id_lang'].'" name="body_mail_'.$language['id_lang'].'">' . $existing_file . '</textarea>
					</div>';
				 }

				$this->_pageHtml .= $this->displayFlags($languages, $defaultLanguage, 'InformMail', 'InformMail', true);
	$this->_pageHtml .= '</div>';
	$this->_pageHtml .= '<div class="clear pspace"></div><div class="margin-form clear"><input type="submit" name="submitUpdateMail" value="'.$this->l('Update the mail').'" class="button" /></div>';	
	$this->_pageHtml .= '<input type="hidden" id="actionForm" name="actionForm" value="updateMail">';
	$this->_pageHtml .= '<script>AutomaticClick()</script>';
	$this->_pageHtml .= '</fieldset></form>';
    if(Tools::isSubmit('submittoolsdiscount'))
    {
      if(Configuration::updateValue('MOD_TOOLSDISCOUNT_ACTIVE', Tools::getValue('active'))) {
		$this->_pageHtml .= $this->displayConfirmation($this->l('Settings Updated'));
	  }
      else
        $this->_pageHtml .= $this->displayError($this->l('Unable to update settings'));
    }
    echo $this->_pageHtml;
  }
	public function _getMailContent( $lang ) {
        $Fichier = dirname(__FILE__)."/mails/".$lang['iso_code']."/InformMail.html";
        $ContenuFichier = @file_get_contents( $Fichier ) ;
        return $ContenuFichier ;
    }	
	
    function putUpdatedMail() {
        $languages = Language::getLanguages();
        foreach ($languages as $language)
            if(!$this->_putNewMailContents($language))
				return false;
		return true;
    }	
	
    function _putNewMailContents( $lang )
    {
        $BodyMail = stripslashes($_REQUEST['body_mail_' . $lang['id_lang']]);
        $Fichier = dirname(__FILE__)."/mails/".$lang['iso_code']."/InformMail.html";
		if(!is_dir(dirname(__FILE__)."/mails/".$lang['iso_code']."/"))
			if(!mkdir(dirname(__FILE__)."/mails/".$lang['iso_code']."/"))
				return false;
		if(!is_file($Fichier))
			if(!error_log("", 3, dirname(__FILE__)."/mails/".$lang['iso_code']."/InformMail.html"))
				return false;
        if(file_put_contents( $Fichier, $BodyMail ) === false)
			return false;
		else
			return true;
    }	
	
	public function hookcreateAccount($param)
	{
		//Code d'affectation de bon de reduction.
		global $cookie;
		$IdCustomer = intval($cookie->id_customer);
		$ListDiscount = UtilsDiscount::getListDiscount(intval($cookie->id_lang), date('Y-m-d H:i:s'));
		$currency = Currency::getCurrent();
		if(count($ListDiscount) != 0) {
			foreach($ListDiscount as $KeyDiscount=>$ValDiscount) {
				$BRCode = UtilsDiscount::getRand();
				Db::getInstance()->autoExecute(
					_DB_PREFIX_.'discount',
					array(
						'id_discount_type'=>$ValDiscount['id_discount_type'],
						'id_customer'=>$IdCustomer,
						'id_currency'=>$currency->id, 
						'name'=>$BRCode,
						'value'=>$ValDiscount['value'],
						'quantity'=>1,
						'quantity_per_user'=>1,
						'cumulable'=>$ValDiscount['cumulable'],
						'cumulable_reduction'=>$ValDiscount['cumulable_reduction'],
						'date_from'=>date('Y-m-d H:i:s'),
						'date_to'=>date('Y-m-d H:i:s', mktime(date('H'), date('m'), date('s'), date('m'), date('d')+$ValDiscount['validity'], date('Y'))),
						'minimal'=>$ValDiscount['minimal'],
						'active'=>1
					),
					'INSERT'
				);
				$BRValidity = date('d/m/Y H:i', mktime(date('H'), date('m'), date('s'), date('m'), date('d')+$ValDiscount['validity'], date('Y')));
				$IdDiscountInsert = Db::getInstance()->Insert_ID();
				$CustomersDiscountLang 	= UtilsDiscount::getCustomersDiscountEditLang(intval($cookie->id_lang), $ValDiscount['id_customers_discount']);
				if(count($CustomersDiscountLang) != 0) {
					foreach($CustomersDiscountLang as $KeyDiscountLang=>$ValDiscountLang) {
						Db::getInstance()->autoExecute(
							_DB_PREFIX_.'discount_lang',
							array(
								'id_discount'=>$IdDiscountInsert,
								'id_lang'=>$ValDiscountLang['id_lang'],
								'description'=>$ValDiscountLang['description']
							),
							'INSERT'
						);
						if($cookie->id_lang == $ValDiscountLang['id_lang'])
							$BRDescription = $ValDiscountLang['description'];
					}
				}
				$CustomersDiscountCategory 	= UtilsDiscount::getCustomersDiscountEditCategory(intval($cookie->id_lang), $ValDiscount['id_customers_discount']);
				if(count($CustomersDiscountCategory) != 0) {
					foreach($CustomersDiscountCategory as $KeyDiscountCategory=>$ValDiscountCategory) {
						Db::getInstance()->autoExecute(
							_DB_PREFIX_.'discount_category',
							array(
								'id_category'=>$ValDiscountCategory['id_category'], 
								'id_discount'=>$IdDiscountInsert
							),
							'INSERT'
						);
					}
				}
				Db::getInstance()->ExecuteS('
				UPDATE '._DB_PREFIX_.'customers_discount SET 
				offer=(offer+1) WHERE id_customers_discount='.$ValDiscount['id_customers_discount'].'
				');
				Db::getInstance()->autoExecute(
					_DB_PREFIX_.'customers_discount_affect',
					array(
						'id_customers_discount'=>$ValDiscount['id_customers_discount'],
						'id_discount'=>$IdDiscountInsert
					),
					'INSERT'
				);				
				if(Configuration::get('MOD_TOOLSDISCOUNT_MAIL')=='on') {
					include_once(dirname(__FILE__).'/mails/'.(Language::getIsoById(intval($cookie->id_lang))).'/lang.php');
					global $_LANGMAILDISCOUNT;
					$SubjectMail = ((is_array($_LANGMAILDISCOUNT) AND array_key_exists('Oferta de Bienvenida', $_LANGMAILDISCOUNT)) ? $_LANGMAILDISCOUNT['Oferta de Bienvenida'] : 'Oferta de Bienvenida');
					switch($ValDiscount['id_discount_type']) {
						case 1 :
							$BRValueMail = $ValDiscount['value']." %";
							break;
						case 2 :
							$BRValueMail = $ValDiscount['value']." &euro;";
							break;
						case 3 :
							$BRValueMail = ((is_array($_LANGMAILDISCOUNT) AND array_key_exists('Free shipping.', $_LANGMAILDISCOUNT)) ? $_LANGMAILDISCOUNT['Free shipping.'] : 'Free shipping.');
							break;
					}
					if (!Mail::Send(intval($cookie->id_lang), 'InformMail', $SubjectMail, 
						array('{email}' => $cookie->email, '{BRValue}' => $BRValueMail, '{BRValidity}' => $BRValidity, '{BRCode}' => $BRCode, '{BRDescription}' => $BRDescription), $cookie->email, $cookie->customer_firstname.' '.$cookie->customer_lastname, NULL, NULL, NULL, NULL, dirname(__FILE__)."/mails/"))			
						$errors[] = Tools::displayError('cannot send email');	
				}
			}
		}
	}
}
?>