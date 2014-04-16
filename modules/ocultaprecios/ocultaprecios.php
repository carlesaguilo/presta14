<?php
################################################################################
################################################################################
############   DESARROLLADO POR www.4webs.es    ################################
################################################################################
################################################################################
//11/01/2012


if (!defined('_CAN_LOAD_FILES_'))
	exit;
	
class ocultaprecios extends Module{
	
	public function __construct()
	{
		$this->name = 'ocultaprecios';
		$this->tab = 'front_office_features';
		$this->version = '1.1';// version
		$this->author = 'www.4webs.es';
		$this->need_instance = 0;
		
		parent::__construct();

		$this->displayName = $this->l('Oculta Precios');
		$this->description = $this->l('Muestra los precios solamente a los usuarios logueados');
		$path = dirname(__FILE__);
		if (strpos(__FILE__, 'Module.php') !== false)
			$path .= '/../modules/'.$this->name;
		include_once($path.'/OP_Class.php');
		
	}  // Constructor	
	
	public function install()
	  {
	  	if ( !parent::install() OR !$this->registerHook( 'header' ))
		return false;
		
		if (!Db::getInstance()->Execute('
		CREATE TABLE `'._DB_PREFIX_.'fourwebs_OP` (
		`id_OP` int(10) unsigned NOT NULL auto_increment,
		PRIMARY KEY (`id_OP`))
		ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8'))
			return false;
		
		if (!Db::getInstance()->Execute('
		CREATE TABLE `'._DB_PREFIX_.'fourwebs_OP_lang` (
		`id_OP` int(10) unsigned NOT NULL,
		`id_lang` int(10) unsigned NOT NULL,
		`texto` varchar(255) NOT NULL,
		PRIMARY KEY (`id_OP`, `id_lang`))
		ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8'))
			return false;
		
		if (!Db::getInstance()->Execute('
		INSERT INTO `'._DB_PREFIX_.'fourwebs_OP`(`id_OP`) 
		VALUES(1)'))
			return false;
		
		if (!Db::getInstance()->Execute('
		INSERT INTO `ps_fourwebs_OP_lang`(`id_OP`, `id_lang`, `texto`)
		VALUES
			(1, 1, "You need login to view the prices"),
			(1, 2, "Vous devez vous enregistrer pour voir les prix"),
			(1, 3, "Necesita registrarse para ver el precio")'))
			return false;
		return true;
	  } //Instalador
	
	public function uninstall()
  	{
  		if ( !parent::uninstall() OR !$this->unregisterHook( 'header' ))
    		return false;
		return (Db::getInstance()->Execute('DROP TABLE `'._DB_PREFIX_.'fourwebs_OP`') AND
				Db::getInstance()->Execute('DROP TABLE `'._DB_PREFIX_.'fourwebs_OP_lang`'));
  	}  // Desinstalador
	
	public function getContent()
	{
		$output = '';
		if (Tools::isSubmit('submit'))
		{
			$oculta = new OP_Class(1);
			$oculta->copyFromPost();
			$oculta->update();
			
			$output.=$this->displayConfirmation($this->l('Configuracion actualizada'));
			
		}
		return $output.=$this->displayForm();
	}// fin get content
	
	
	public function displayFlags($languages, $default_language, $ids, $id, $return = false, $use_vars_instead_of_ids = false)
	{
		if (sizeof($languages) == 1)
			return false;
		$output = '
		<div class="displayed_flag">
			<img src="../img/l/'.$default_language.'.jpg" class="pointer" id="language_current_'.$id.'" onclick="toggleLanguageFlags(this);" alt="" />
		</div>
		<div id="languages_'.$id.'" class="language_flags">
			'.$this->l('Elige Idioma:').'<br />';
		foreach ($languages as $language){
			if($use_vars_instead_of_ids)
				$output .= '<img src="../img/l/'.(int)($language['id_lang']).'.jpg" class="pointer" alt="'.$language['name'].'" title="'.$language['name'].'" onclick="changeLanguage(\''.$id.'\', '.$ids.', '.$language['id_lang'].', \''.$language['iso_code'].'\');" />  ';
			else
				$output .= '<img src="../img/l/'.(int)($language['id_lang']).'.jpg" class="pointer" alt="'.$language['name'].'" title="'.$language['name'].'" onclick="changeLanguage(\''.$id.'\', \''.$ids.'\', '.$language['id_lang'].', \''.$language['iso_code'].'\');" /> ';
				
		}
		$output .= '</div>';

		if ($return)
			return $output;
		echo $output;
	}
	
	/*CONFIGURACIÓN MÓDULO*/
	public function displayForm()
	{
		global $cookie;
		/* Languages preliminaries */
		$defaultLanguage = (int)(Configuration::get('PS_LANG_DEFAULT'));
		$languages = Language::getLanguages(false);
		$iso = Language::getIsoById((int)($cookie->id_lang));
		$divLangName = 'title';
		
		$oculta = new OP_Class(1);
		// TinyMCE
		global $cookie;
		$iso = Language::getIsoById((int)($cookie->id_lang));
		$isoTinyMCE = (file_exists(_PS_ROOT_DIR_.'/js/tiny_mce/langs/'.$iso.'.js') ? $iso : 'en');
		$ad = dirname($_SERVER["PHP_SELF"]);
		$this->_html .=  '
			<script type="text/javascript">	
			var iso = \''.$isoTinyMCE.'\' ;
			var pathCSS = \''._THEME_CSS_DIR_.'\' ;
			var ad = \''.$ad.'\' ;
			</script>
			<script type="text/javascript" src="'.__PS_BASE_URI__.'js/tiny_mce/tiny_mce.js"></script>
			<script type="text/javascript" src="'.__PS_BASE_URI__.'js/tinymce.inc.js"></script>';
		$this->_html .= '
		<script type="text/javascript">id_language = Number('.$defaultLanguage.');</script>
		
		<form method="post" action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" enctype="multipart/form-data">
		<fieldset>
			<legend><img src="'.$this->_path.'logo.gif" alt="" title="" />'.$this->l('Oculta Precios').' v.1.1</legend>
			<label>'.$this->l('Texto a mostrar cuando no se está logueado: ').'</label>
			<div class="margin-form">';
			
			foreach ($languages as $language)
				{
					$this->_html .= '
					<div id="title_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').';float: left;">
						<input type="text" name="texto_'.$language['id_lang'].'" id="texto_'.$language['id_lang'].'" size="50" value="'.(isset($oculta->texto[$language['id_lang']]) ? $oculta->texto[$language['id_lang']] : '').'" />
					</div>';
					
				}
				$this->_html .= $this->displayFlags($languages, $defaultLanguage, $divLangName, 'title', true);
			$this->_html .= '<p class="clear">'.$this->l('No está permitido el uso de comillas simples (´), se encuentran reservadas por el módulo.').'</p>
			</div>
			<center><input type="submit" name="submit" value="'.$this->l('Guardar').'" class="button" /></center>
			</fieldset>
		</form>';
	
		return $this->_html;
	}
	
	
	function hookHeader($params)
	{
		global $cookie, $smarty;
		
		$oculta = new OP_Class(1, (int)$cookie->id_lang);
		$smarty->assign(array(
			'texto' => $oculta->texto));
		
		return $this->display(__FILE__, 'header.tpl');
		
	}
}

?>