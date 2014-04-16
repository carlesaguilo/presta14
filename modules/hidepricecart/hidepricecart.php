<?php



class Hidepricecart extends Module

{

	function __construct()

	{

		$this->name = 'hidepricecart';

		if(_PS_VERSION_ > "1.5.0.0"){

		$this->tab = 'front_office_features';

		$this->author = 'RSI';

		$this->need_instance = 1;

		}

		elseif

		(_PS_VERSION_ > "1.4.0.0" && _PS_VERSION_ < "1.5.0.0"){

				$this->tab = 'front_office_features';

		$this->author = 'RSI';

		$this->need_instance = 1;

		}

		else

		{

		$this->tab = 'Tools';

		}

		$this->version = 1.0;



		parent::__construct();



		$this->displayName = $this->l('Hide price and cart button');

		$this->description = $this->l('HIde price and cart for non register users- www.catalogo-onlinersi.com.ar');

	}



	function install()

	{

		parent::install();

		$this->registerHook('header');

			$this->registerHook('footer');

		$this->registerHook('extraLeft');

			if (!Configuration::updateValue('HIDEPRICECART_SKIP_CAT', 1))

			return false;

			if (!Configuration::updateValue('HIDEPRICECART_COLOR', 'ffffff'))

			return false;

			if (!Configuration::updateValue('HIDEPRICECART_WIDTH', '129'))

			return false;

			if (!Configuration::updateValue('HIDEPRICECART_HEIGHT', '.ajax_add_to_cart_button,#product_list li .ajax_add_to_cart_button,#primary_block p.buttons_bottom_block a, #primary_block p.buttons_bottom_block input'))

			return false;

			if (!Configuration::updateValue('HIDEPRICECART_ALIGN', 'ffffff'))

			return false;

			if (!Configuration::updateValue('HIDEPRICECART_NUMBER', '5'))

			return false;

			if (!Configuration::updateValue('HIDEPRICECART_PRICES', '13'))

			return false;

			if (!Configuration::updateValue('HIDEPRICECART_TITLES', '#center_column .products_block span.price, #center_column .products_block a.exclusive, #center_column .products_block span.exclusive,ul#product_list li .price,#primary_block form#buy_block p.price,#primary_block form#buy_block p#old_price,.price_container,.price, .price-shipping, .price-wrapping'))

			return false;

			if (!Configuration::updateValue('HIDEPRICECART_SORT', 0))

			return false;

		

	



	 return true  ;

	}

public function getContent()

	{

		$output = '<h2>'.$this->displayName.'</h2>';

		if (Tools::isSubmit('submitBlockCuber'))

		{

		

			$skipcat = Tools::getValue('skipcat');

			$color = Tools::getValue('color');

			$number = Tools::getValue('number');

			$width = Tools::getValue('width');

			$height = Tools::getValue('height');

			$align = Tools::getValue('align');

				$sort = Tools::getValue('sort');

			$prices = Tools::getValue('prices');

			$align = Tools::getValue('align');

			$titles = Tools::getValue('titles');

		

				Configuration::updateValue('HIDEPRICECART_NBR', $nbr);

				Configuration::updateValue('HIDEPRICECART_COLOR', $color);

				Configuration::updateValue('HIDEPRICECART_ALIGN', $align);

				Configuration::updateValue('HIDEPRICECART_WIDTH', $width);

				Configuration::updateValue('HIDEPRICECART_HEIGHT', $height);

				Configuration::updateValue('HIDEPRICECART_NUMBER', $number);

				Configuration::updateValue('HIDEPRICECART_PRICES', $prices);

				Configuration::updateValue('HIDEPRICECART_TITLES', $titles);

				Configuration::updateValue('HIDEPRICECART_SORT', $sort);

				

		if (!empty($skipcat))

				Configuration::updateValue('HIDEPRICECART_SKIP_CAT', implode(',',$skipcat));



	

			if (isset($errors) AND sizeof($errors))

				$output .= $this->displayError(implode('<br />', $errors));

				

			else

				$output .= $this->displayConfirmation($this->l('Settings updated'));

		}

		return $output.$this->displayForm();

	}

	function recurseCategory($categories, $current, $id_category = 1, $selectids_array)

	{

		global $currentIndex;		



		echo '<option value="'.$id_category.'"'.(in_array($id_category,$selectids_array) ? ' selected="selected"' : '').'>'.

		str_repeat('&nbsp;', $current['infos']['level_depth'] * 5) . preg_replace('/^[0-9]+\./', '', stripslashes($current['infos']['name'])) . '</option>';

		if (isset($categories[$id_category]))

			foreach ($categories[$id_category] AS $key => $row)

				$this->recurseCategory($categories, $categories[$id_category][$key], $key, $selectids_array);

	}



	public function displayForm()

	{

		global $cookie,$currentIndex;

		$output = '

		<link rel="stylesheet" href="../modules/blockcuber/css/colorpicker.css" type="text/css" />

	

		<script type="text/javascript" src="../modules/blockcuber/js/colorpicker.js"></script>

		<script type="text/javascript" src="../modules/blockcuber/js/eye.js"></script>

		<script type="text/javascript" src="../modules/blockcuber/js/utils.js"></script>

		<script type="text/javascript" src="../modules/blockcuber/js/layout.js?ver=1.0.2"></script>

		<form action="'.$_SERVER['REQUEST_URI'].'" method="post">

			<fieldset><legend><img src="'.$this->_path.'logo.gif" alt="" title="" />'.$this->l('Settings').'</legend>

			<!--	<label>'.$this->l('Number of product displayed').'</label>

				<div class="margin-form">

					<input type="text" size="5" name="nbr" value="'.Tools::getValue('nbr', Configuration::get('HIDEPRICECART_NBR')).'" />

					<p class="clear">'.$this->l('The number featured products displayed on homepage (default: 10)').'</p>

					

				

		</div>

		<label>'.$this->l('Title Color').'</label>

				<div class="margin-form">

					

				<div id="customWidget">

						<input type="text" maxlength="6"  name="color" size="6" id="colorpickerField4" value="'.Tools::getValue('color', Configuration::get('HIDEPRICECART_COLOR')).'" />	<p class="clear">'.$this->l('Color of the text (default fff)').'</p>

					

					</div>

					

				

		</div>

		

		<label>'.$this->l('Price color').'</label>

				<div class="margin-form">

				

				<div id="customWidget">

						<input type="text" maxlength="6"  name="align" size="6" id="colorpickerField3" value="'.Tools::getValue('align', Configuration::get('HIDEPRICECART_ALIGN')).'" />	<p class="clear">'.$this->l('Color of the text (default fff)').'</p>

					

					</div>

					

				

		</div>	-->

		<label>'.$this->l('Price class button').'</label>

				<div class="margin-form">

					

					

				<textarea name="titles" id="titles" cols="45" rows="5">'.Tools::getValue('titles', Configuration::get('HIDEPRICECART_TITLES')).'</textarea>	

				

		</div>

	

		

		<label>'.$this->l('Add to cart class button').'</label>

				<div class="margin-form">

			

					<textarea name="height" id="height" cols="45" rows="5">'.Tools::getValue('height', Configuration::get('HIDEPRICECART_HEIGHT')).'</textarea>		

				

		</div>

	<p>'.$this->l('Leave blank for disable').'</p>

					

				<center><input type="submit" name="submitBlockCuber" value="'.$this->l('Save').'" class="button" /></center><br/>	

					<center>	<a href="../modules/hidepricecart/moduleinstall.pdf">README</a></center><br/>	

			<center>	<a href="../modules/hidepricecart/termsandconditions.pdf">TERMS</center></a><br/>	

			

		</form>

		';

		return $output;

	}

	/**

	* Returns module content

	*

	* @param array $params Parameters

	* @return string Content

	*/

	function hookExtraLeft($params)

	{

		global $smarty;

$drag= $_SERVER['REQUEST_URI'];

$smarty->assign(array(

			

			'drag' => $drag));



		return $this->display(__FILE__, 'hidepricecart.tpl');

	}

	function hookTop($params)

	{

		global $smarty;





		return $this->display(__FILE__, 'hidepricecart.tpl');

	}

	function hookHeader($params)

	{

		global $smarty,$cookie;

		if(_PS_VERSION_ > "1.4.0.0" && _PS_VERSION_ < "1.5.0.0"){

		Tools::addCSS(__PS_BASE_URI__.'css/jquery.jgrowl.css', 'all');

		Tools::addJS(__PS_BASE_URI__.'js/jquery/jquery.jgrowl-1.2.1.min.js');

		}

		if(_PS_VERSION_> "1.5..0")

		{

			$this->context->controller->addCSS(__PS_BASE_URI__.'css/jquery.jgrowl.css', 'all');

			$this->context->controller->addJS(__PS_BASE_URI__.'js/jquery/jquery.jgrowl-1.2.1.min.js');

			}

		return $this->display(__FILE__, 'hidepricecart-header.tpl');

	}

	function hookFooter($params)

	{

		global $smarty, $cookie;

global $smarty;

$titles = Configuration::get('HIDEPRICECART_TITLES');

$height = Configuration::get('HIDEPRICECART_HEIGHT');

$smarty->assign(array(

			

			'priceh' => $titles,

			'addh' => $height,

			));



		return $this->display(__FILE__, 'hidepricecart-footer.tpl');

	}



}



?>