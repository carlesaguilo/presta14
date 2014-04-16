<?php
if (!defined('_PS_VERSION_'))
	exit;
class TMLanguages extends Module
{
	function __construct()
	{
		$this->name = 'tmlanguages';
		$this->tab = 'front_office_features';
		$this->version = 1.4;
		$this->author = 'TM';
		$this->need_instance = 0;
		parent::__construct();
		$this->displayName = $this->l('TM Language block');
		$this->description = $this->l('Adds a block for selecting a language');
	}
	function install()
	{
		return (parent::install() AND $this->registerHook('footer'));
	}
	function hookFooter($params)
	{
		global $smarty;
		$languages = Language::getLanguages();
		if (!count($languages))
			return;
		$link = new Link();
		if ((int)Configuration::get('PS_REWRITING_SETTINGS'))
		{
			$default_rewrite = array();
			$phpSelf = isset($_SERVER['PHP_SELF']) ? substr($_SERVER['PHP_SELF'], strlen(__PS_BASE_URI__)) : '';
			if ($phpSelf == 'product.php' AND $id_product = (int)Tools::getValue('id_product'))
			{
				$rewrite_infos = Product::getUrlRewriteInformations((int)$id_product);
				foreach ($rewrite_infos AS $infos)
					$default_rewrite[$infos['id_lang']] = $link->getProductLink((int)$id_product, $infos['link_rewrite'], $infos['category_rewrite'], $infos['ean13'], (int)$infos['id_lang']);
			}
			if ($phpSelf == 'category.php' AND $id_category = (int)Tools::getValue('id_category'))
			{
				$rewrite_infos = Category::getUrlRewriteInformations((int)$id_category);
				foreach ($rewrite_infos AS $infos)
					$default_rewrite[$infos['id_lang']] = $link->getCategoryLink((int)$id_category, $infos['link_rewrite'], $infos['id_lang']);
			}
			if ($phpSelf == 'cms.php' AND ($id_cms = (int)Tools::getValue('id_cms') OR $id_cms_category = (int)Tools::getValue('id_cms_category')))
			{
				$rewrite_infos = (isset($id_cms) AND !isset($id_cms_category)) ? CMS::getUrlRewriteInformations($id_cms) : CMSCategory::getUrlRewriteInformations($id_cms_category);
				foreach ($rewrite_infos AS $infos)
				{
					$arr_link = (isset($id_cms) AND !isset($id_cms_category)) ?
						$link->getCMSLink($id_cms, $infos['link_rewrite'], NULL, $infos['id_lang']) :
						$link->getCMSCategoryLink($id_cms_category, $infos['link_rewrite'], $infos['id_lang']);
					$default_rewrite[$infos['id_lang']] = $arr_link;
				}
			}
			if (count($default_rewrite))
				$smarty->assign('lang_rewrite_urls', $default_rewrite);
		}
		$smarty->assign('languages', $languages);
		return $this->display(__FILE__, 'tmlanguages.tpl');
	}
}


