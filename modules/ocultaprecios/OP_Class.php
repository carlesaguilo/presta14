<?php
################################################################################
################################################################################
############   DESARROLLADO POR www.4webs.es    ################################
################################################################################
################################################################################
//11/01/2012

class		OP_Class extends ObjectModel
{
	/** @var integer oculta id*/
	public		$id = 1;
	
	/** @var string texto*/
	public		$texto;
	
	protected 	$table = 'fourwebs_OP';
	protected 	$identifier = 'id_OP';

	protected 	$fieldsValidateLang = array(
		'texto' => 'isGenericName');
	
	/**
	  * Check then return multilingual fields for database interaction
	  *
	  * @return array Multilingual fields
	  */
	public function getTranslationsFieldsChild()
	{
		parent::validateFieldsLang();

		$fieldsArray = array('texto');
		$fields = array();
		$languages = Language::getLanguages(false);
		$defaultLanguage = (int)(Configuration::get('PS_LANG_DEFAULT'));
		foreach ($languages as $language)
		{
			$fields[$language['id_lang']]['id_lang'] = (int)($language['id_lang']);
			$fields[$language['id_lang']][$this->identifier] = (int)($this->id);
			foreach ($fieldsArray as $field)
			{
				if (!Validate::isTableOrIdentifier($field))
					die(Tools::displayError());
				if (isset($this->{$field}[$language['id_lang']]) AND !empty($this->{$field}[$language['id_lang']]))
					$fields[$language['id_lang']][$field] = pSQL($this->{$field}[$language['id_lang']], true);
				elseif (in_array($field, $this->fieldsRequiredLang))
					$fields[$language['id_lang']][$field] = pSQL($this->{$field}[$defaultLanguage], true);
				else
					$fields[$language['id_lang']][$field] = '';
			}
		}
		return $fields;
	}
	
	public function copyFromPost()
	{
		/* Classical fields */
		foreach ($_POST AS $key => $value)
			if (key_exists($key, $this) AND $key != 'id_'.$this->table)
				$this->{$key} = $value;

		/* Multilingual fields */
		if (sizeof($this->fieldsValidateLang))
		{
			$languages = Language::getLanguages(false);
			foreach ($languages AS $language)
				foreach ($this->fieldsValidateLang AS $field => $validation)
					if (isset($_POST[$field.'_'.(int)($language['id_lang'])]))
						$this->{$field}[(int)($language['id_lang'])] = $_POST[$field.'_'.(int)($language['id_lang'])];
		}
	}
	
	public function getFields()
	{
		parent::validateFields();
		$fields['id_OP'] = (int)($this->id);
		return $fields;
	}
}
