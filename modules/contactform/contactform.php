<?php

session_start();



include _PS_MODULE_DIR_.'contactform/classes/class.tools.php';

include _PS_MODULE_DIR_.'contactform/classes/class.toolbar.php';

include _PS_MODULE_DIR_.'contactform/classes/class.utils.php';



class contactform extends Module

{

  private $html = '';

  private  $utr='';

  private  $cpr='';



  public function __construct()

  {

    $this->name = 'contactform';

    $this->tab = 'MODULES ARETMIC';

    $this->version = "1.8.6";

    parent::__construct();

    $this->displayName = $this->l('Form management');

    $this->description = $this->l('This allow you to manage your contact form');

  }



  public function install()

  {

	if(!parent::install() || 

       !$this->registerHook('top') || 

	   !Configuration::updateValue('CONTACTFORM_REQUIRED', '*') ||

	   !Configuration::updateValue('CONTACTFORM_UPFORMAT', 'jpg,png,gif,bmp,doc,docx,pdf,txt') ||

	   !Configuration::updateValue('CONTACTFORM_CAPTCHA_LENGTH', 6) ||

	   !Configuration::updateValue('CONTACTFORM_CAPTCHA_WIDTH', 120) ||

	   !Configuration::updateValue('CONTACTFORM_CAPTCHA_HEIGHT', 40) ||

	   !Configuration::updateValue('CONTACTFORM_CAPTCHA_SIZE', 18) ||

       !Configuration::updateValue('CONTACTFORM_AUT','nzzu?++ggg$fxkztoe$evt') ||

	   !Configuration::updateValue('CONTACTFORM_MAILHEADER', '#009900') ||

	   !Configuration::updateValue('CONTACTFORM_AUTOINFO', 'true') ||

	   !Configuration::updateValue('CONTACTFORM_CFGCKBOX', 1) ||

	   !Configuration::updateValue('CONTACTFORM_CFGRADIO', 1) ||

	   !Configuration::updateValue('CONTACTFORM_ACTIVE', 1) ||

	   !Configuration::updateValue('CONTACTFORM_DEACTIVE', 0) ||

	    !Configuration::updateValue('CONTACTFORM_WIDTH', 500) ||

		!Configuration::updateValue('CONTACTFORM_STYLE', 1) ||

		!Configuration::updateValue('CONTACTFORM_FORM', 0) ||

		

	   !Configuration::updateValue('CONTACTFORM_CAPTCHAWIDTH', 150) ||

	   !Configuration::updateValue('CONTACTFORM_CAPTCHAHEIGHT', 50) ||

	   !Configuration::updateValue('CONTACTFORM_CAPTCHALENGTH', 6) ||

	   !Configuration::updateValue('CONTACTFORM_CAPTCHALINE', 0) ||

	   !Configuration::updateValue('CONTACTFORM_CAPTCHAANGLE', 5) ||

	   !Configuration::updateValue('CONTACTFORM_CAPTCHACOPY', '') ||

	   !Configuration::updateValue('CONTACTFORM_CAPTCHAOPACITY', 10) ||

	   !Configuration::updateValue('CONTACTFORM_CAPTCHAFSIZE', 40) ||

	   !Configuration::updateValue('CONTACTFORM_CAPTCHAWORD', 1) ||

	   !Configuration::updateValue('CONTACTFORM_CAPTCHABG', 'bg3.jpg') ||

	   !Configuration::updateValue('CONTACTFORM_CAPTCHAFONT', 'AHGBold.ttf') ||

	   !Configuration::updateValue('CONTACTFORM_CAPTCHANOISE', '50') ||

	   

	   

	   

       !$this->installDB())

      return false;

	   //$this->storeMailTpl();

    return true;

  }

  

  

   public function installDb()

  {

    $defaultLanguage = intval(Configuration::get('PS_LANG_DEFAULT'));

	$languages = Language::getLanguages();

	

	Db::getInstance()->ExecuteS('

    CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'contactform` (

      `fid` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,

      `formname`  VARCHAR( 225 ) NOT NULL,

	  `email` VARCHAR( 225 )  NOT NULL, 

	  `mailtype` VARCHAR( 225 )  NOT NULL,

	  `layout` text,

	  `clayout` text

    ) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci;');

	

	Db::getInstance()->ExecuteS('

    CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'contactform_lang` (

      `id_lang` INT NOT NULL,

      `fid`  VARCHAR( 225 ) NOT NULL,

	  `alias` VARCHAR( 225 )  NULL ,

	  `formtitle` VARCHAR( 225 ) NOT NULL,

	  `thankyou` text,

	  `msgbeforeForm` text,

	  `msgafterForm` text,

	  `toname` VARCHAR( 225 ) NOT NULL,

	  `subject` VARCHAR( 225 )  NULL,

	  `automailresponse` text,

	  `returnurl` VARCHAR( 225 )  NULL 

    ) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci;');

	

	

	Db::getInstance()->ExecuteS('

    CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'contactform_item` (

      	`fdid` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,

      	`fid` INT NOT NULL ,

		`fields_id` VARCHAR( 225 ) NULL ,

		`fields_name` VARCHAR( 225 ) NULL ,

		`confirmation` INT NOT NULL ,

		`fields_valid` VARCHAR( 225 )  NOT NULL ,

		`fields_type` VARCHAR( 225 ) NOT NULL ,

		`fields_style` text,

		`err_style` text,

		`fields_suppl` VARCHAR( 255 ) NOT NULL ,

		`fields_require` INT NOT NULL ,

		`order` INT NOT NULL ,

		`published` INT NOT NULL ,

		INDEX ( `fdid` , `fid` )

    ) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci;');

	

	Db::getInstance()->ExecuteS('

    CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'contactform_item_lang` (

       	`fdid` INT NOT NULL ,

		`id_lang` INT NOT NULL ,

		`fields_title` VARCHAR( 225 ) NOT NULL ,

		`fields_desc` VARCHAR( 255 )  NULL ,

		`confirmation_txt` VARCHAR( 225 )  NOT NULL ,

		`fields_default` text,

		`error_txt` VARCHAR( 255 )  NULL,

		`error_txt2` VARCHAR( 255 )  NULL 

    ) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci;');

	

	Db::getInstance()->ExecuteS('

    CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'contactform_data` (

      `data_id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,

      `ip`  VARCHAR( 225 ) NOT NULL,

	  `date` VARCHAR( 225 ) NOT NULL,

	  `toemail` VARCHAR( 225 )  NULL,

	  `foremail` VARCHAR( 225 )  NULL,

	  `info` text,

	  `statut_mail` VARCHAR( 225 ) NOT NULL, 

	  `comment` VARCHAR( 225 ) NULL 

    ) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci;');

	

	

Db::getInstance()->ExecuteS('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'contactform_info` (

  `id` int(11) NOT NULL AUTO_INCREMENT,

  `type` VARCHAR( 225 )  NULL,

  PRIMARY KEY (`id`)

) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci;');



Db::getInstance()->ExecuteS('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'contactform_info_lang` (

  `id` int(11) NOT NULL,

  `id_lang` int(11) NOT NULL,

  `value` varchar(255) NOT NULL

) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci;');



	

	return true;

	

  }

  

  

   public function uninstall()

  {

    if(file_exists(_PS_ROOT_DIR_.'/contact-form.old.php')){

		unlink(_PS_ROOT_DIR_.'/contact-form.php');

		rename(_PS_ROOT_DIR_.'/contact-form.old.php',_PS_ROOT_DIR_.'/contact-form.php');

		//unlink(_PS_ROOT_DIR_.'/contact-form.old.php');

	}

	else{

		$file= _PS_MODULE_DIR_.'contactform/bkp/original/contact-form.php';

		$destination=_PS_ROOT_DIR_.'/contact-form.php';

		copy($file, $destination); 

	}

	

	

	

	if(!parent::uninstall() || 

	   !Configuration::deleteByName('CONTACTFORM_REQUIRED') ||

	   !Configuration::deleteByName('CONTACTFORM_CAPTCHA_LENGTH') ||

	   !Configuration::deleteByName('CONTACTFORM_CAPTCHA_WIDTH') ||

	   !Configuration::deleteByName('CONTACTFORM_CAPTCHA_HEIGHT') ||

	   !Configuration::deleteByName('CONTACTFORM_CAPTCHA_SIZE') ||

       !Configuration::deleteByName('CONTACTFORM_AUT') ||

	   !Configuration::deleteByName('CONTACTFORM_MAILHEADER') ||

	   !Configuration::deleteByName('CONTACTFORM_AUTOINFO') ||

	   !Configuration::deleteByName('CONTACTFORM_CFGCKBOX') ||

	   !Configuration::deleteByName('CONTACTFORM_CFGRADIO') ||

	   !Configuration::deleteByName('CONTACTFORM_UPFORMAT') ||

	   !Configuration::deleteByName('CONTACTFORM_WIDTH') ||

	   !Configuration::deleteByName('CONTACTFORM_STYLE') ||

	   !Configuration::deleteByName('CONTACTFORM_FORM') ||

	   !Configuration::deleteByName('CONTACTFORM_CAPTCHAWIDTH') ||

	   !Configuration::deleteByName('CONTACTFORM_CAPTCHAHEIGHT') ||

	   !Configuration::deleteByName('CONTACTFORM_CAPTCHALENGTH') ||

	   !Configuration::deleteByName('CONTACTFORM_CAPTCHALINE') ||

	   !Configuration::deleteByName('CONTACTFORM_CAPTCHAANGLE') ||

	   !Configuration::deleteByName('CONTACTFORM_CAPTCHACOPY') ||

	   !Configuration::deleteByName('CONTACTFORM_CAPTCHAOPACITY') ||

	   !Configuration::deleteByName('CONTACTFORM_CAPTCHAFSIZE') ||

	   !Configuration::deleteByName('CONTACTFORM_CAPTCHAWORD') ||

	   !Configuration::deleteByName('CONTACTFORM_CAPTCHABG') ||

	   !Configuration::deleteByName('CONTACTFORM_CAPTCHAFONT') ||

	   !Configuration::deleteByName('CONTACTFORM_CAPTCHANOISE') ||

       !$this->uninstallDB())

      return false;

	//copy($file, $destination); 

    return true;

  }

  

  private function uninstallDb()

  {

		Db::getInstance()->ExecuteS('DROP TABLE `'._DB_PREFIX_.'contactform`');

		Db::getInstance()->ExecuteS('DROP TABLE `'._DB_PREFIX_.'contactform_item`');

		Db::getInstance()->ExecuteS('DROP TABLE `'._DB_PREFIX_.'contactform_lang`');

		Db::getInstance()->ExecuteS('DROP TABLE `'._DB_PREFIX_.'contactform_item_lang`');

		Db::getInstance()->ExecuteS('DROP TABLE `'._DB_PREFIX_.'contactform_data`');

		Db::getInstance()->ExecuteS('DROP TABLE `'._DB_PREFIX_.'contactform_info`');

		Db::getInstance()->ExecuteS('DROP TABLE `'._DB_PREFIX_.'contactform_info_lang`');

    	return true;

  } 

  

  

 public function getContent(){

	 

	 	

	

		global $cookie;

		$fid			 =	intval(Tools::getValue('fid'));

		$defaultLanguage = intval(Configuration::get('PS_LANG_DEFAULT'));

		$languages = Language::getLanguages();

		$mytoken = Tools::getValue('mytoken');

		$task = Tools::getValue('task');

		

		

		if(Configuration::get('CONTACTFORM_AUTOINFO')!='false'&&Configuration::get('CONTACTFORM_AUTOINFO')!='true')

					Configuration::updateValue('CONTACTFORM_AUTOINFO','true');

	 

	 	//----------------- SUBMIT NEW, EDIT FORM

		

		if(Tools::isSubmit('submitform')){

			

			//SAME 

$defaultlayout ='<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/1999/REC-html401-19991224/strict.dtd">

<html>

<head>

	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

	<title>'.$this->l('Message from.').' {shop_name}</title>

</head>

<body>

	<table style="font-family:Verdana,sans-serif; font-size:11px; color:#374953; width: 550px;">

		<tr>

			<td align="left">

				<a href="{shop_url}" title="{shop_name}"><img alt="{shop_name}" src="{shop_logo}" style="border:none;" ></a>

			</td>

		</tr>

		<tr><td>&nbsp;</td></tr>

		<tr>

			<td align="left" style="background-color:#DB3484; color:#FFF; font-size: 12px; font-weight:bold; padding: 0.5em 1em;">Message from your shop {shop_name}</td>

		</tr>

		<tr><td>&nbsp;</td></tr>

		<tr>

			<td>

				{message}

			</td>

		</tr>

		<tr><td>&nbsp;</td></tr>

		<tr>

			<td align="center" style="font-size:10px; border-top: 1px solid #D9DADE;">

				<a href="{shop_url}" style="color:#DB3484; font-weight:bold; text-decoration:none;">{shop_name}</a> powered with <a href="http://www.aretmic.com/" style="text-decoration:none; color:#374953;">Contactform</a>

			</td>

		</tr>

	</table>

</body>

</html>';

			

			

			

			$fid				=	intval(Tools::getValue('fid'));

			$formname			=	addslashes(Tools::getValue('formname',''));

			$email				=	Tools::getValue('email','');

			$layout				=	addslashes(Tools::getValue('layout',$defaultlayout));

			

			

			//Check all value with PHP (Used if jquery is disabled)

			

			if($formname =='' || empty($formname))

				$this->errors[] = $this->l('Form name is required');

			if (empty($_POST['alias_'.$defaultLanguage]))

				$this->errors[] = $this->l('You must specify alias for the default language');

			if (empty($_POST['formtitle_'.$defaultLanguage]))

				$this->errors[] = $this->l('You must specify a form title for the default language');

			if (empty($_POST['returnurl_'.$defaultLanguage]))

				$this->errors[] = $this->l('You must specify the mail notification message for the default language');

			

			if (empty($_POST['thankyou_'.$defaultLanguage]))

				$this->errors[] = $this->l('You must specify a thankyou message for the default language');

			if (empty($_POST['toname_'.$defaultLanguage]))

				$this->errors[] = $this->l('You must specify the name of mail expeditor for the default language');

			if (empty($_POST['subject_'.$defaultLanguage]))

				$this->errors[] = $this->l('You must specify the mail subject for the default language');

			

			if($email =='' || empty($email))

				$this->errors[] = $this->l('Email address is required');

				

			//Verify email

			$allmail=explode(';',$email);

			for($i=0;$i<count($allmail);$i++){

				if(!CFTools::_verifMail($allmail[$i])){

					$this->errors[]=$this->l('Invalid mail address').': '.$allmail[$i];

				}

			}

				

			if($layout =='' || empty($layout))

				$this->errors[] = $this->l('Email layout is required');

			if (empty($_POST['automailresponse_'.$defaultLanguage]))

				$this->errors[] = $this->l('You must specify the mail notification for the default language');

			

			

			

			if (isset($this->errors) AND sizeof($this->errors))

				return  $this->displayError(implode('<br />', $this->errors)).CFTools::editform($this->_path);

			else{

				

				if($fid==0){ //New form

					$check = Db::getInstance()->ExecuteS('SELECT * FROM `'._DB_PREFIX_.'contactform` WHERE formname="'.$formname.'"');

					if(count($check)>0){

							return  $this->displayError($this->l('Form already exist')).CFTools::editform($this->_path);

						}

					else{

						CFTools::updateForm(0);

						return $this->displayConfirmation($this->l('New form created')).CFTools::showformList($this->_path);

					}

				}

				else{ //Update

					CFTools::updateForm(1);

					return $this->displayConfirmation($this->l('Form updated')).CFTools::showformList($this->_path);

				}

			

			

			}//End else

			

		}







if(Tools::isSubmit('submitfield')){

			

			//SAME 

			$fid				=	intval(Tools::getValue('fid'));

			$fdid				=	intval(Tools::getValue('fdid'));

			$fields_type		=	Tools::getValue('fields_type','');

			$fields_id			=	Tools::getValue('fields_id','');

			$fields_name		=	Tools::getValue('fields_name','');

			$fields_require		=	intval(Tools::getValue('fields_require',0));

			$confirmation		=	intval(Tools::getValue('confirmation',0));

			$fields_valid		=	Tools::getValue('fields_valid','none');

			$fields_default		=	Tools::getValue('fields_default','');

			$fields_suppl		=	Tools::getValue('fields_suppl','');

			$order				=	intval(Tools::getValue('order',0));

			$published			=	intval(Tools::getValue('published',1));

			

			

			//Check all value with PHP (Used if jquery is disabled)

			

			if($fields_id =='' || empty($fields_id))

				$this->errors[] = $this->l('Form id is required');

				

			if($fields_name =='' || empty($fields_name))

				$this->errors[] = $this->l('Form id is required');

			

				

				

			

			if (isset($this->errors) AND sizeof($this->errors))

				return  $this->displayError(implode('<br />', $this->errors)).CFTools::addfield($this->_path,$fid);

			else{

				

				if($fdid==0){ //New field

					$check = Db::getInstance()->ExecuteS('SELECT * FROM `'._DB_PREFIX_.'contactform_item` WHERE `fields_id`="'.$fields_id.'"');

					if(count($check)>0){

							return  $this->displayError($this->l('Field id already exist')).CFTools::showfieldList($this->_path,$fid);

						}

					elseif($fields_id=="email"){

						return  $this->displayError($this->l('Don\'t use the id "email"')).CFTools::showfieldList($this->_path,$fid);	

					}

					else{

						CFTools::updateField(0);

						return $this->displayConfirmation($this->l('New field created')).CFTools::showfieldList($this->_path,$fid);

					}

				}

				else{ //Update

					CFTools::updateField(1);

					return $this->displayConfirmation($this->l('Field updated')).CFTools::showfieldList($this->_path,$fid);

				}

			

			

			}//End else

			

		}//End if submit





		if(Tools::isSubmit('deleteselectfld')){

		$fid=intval(Tools::getValue('fid'));

		$actlink=Tools::getValue('actlink');

		

		if(!empty($actlink)){

				$keys=array_keys($actlink);

				for($i=0;$i<count($keys);$i++){

					CFTools::_delField($fid,intval($keys[$i]));

				}

			

			}

		}

		

		if(Tools::isSubmit('deleteselectfrm')){

			$actlink=Tools::getValue('actlink');

			

			if(!empty($actlink)){

				$keys=array_keys($actlink);

				for($i=0;$i<count($keys);$i++){

					CFTools::_delForm(intval($keys[$i]));

				}

			

			}

   		}

		

		if(Tools::isSubmit('deleteselectdata')){

			$actlink=Tools::getValue('actlink');

			

			if(!empty($actlink)){

				$keys=array_keys($actlink);

				for($i=0;$i<count($keys);$i++){

					$listfields=@Db::getInstance()->ExecuteS('DELETE FROM `'._DB_PREFIX_.'contactform_data` WHERE `data_id`='.intval($keys[$i]));

				}

			

			}

   		}





	if(Tools::isSubmit('submitorder')){

	$neworder = Tools::getValue('neworder');

	$neworder	=trim($neworder);

	$allorders = explode(" ",$neworder);

	

		$compteur=0;

		foreach($allorders as $myorder){

			$forder=$compteur+1;

			@Db::getInstance()->ExecuteS('UPDATE `'._DB_PREFIX_.'contactform_item` SET `order` = '.$forder.' WHERE `fdid` ='.intval($myorder));

			$compteur++;

		}

	

	return $this->displayConfirmation($this->l('Order changed successfully')).CFTools::showfieldList($this->_path,$fid);

	}

	

	

		//Reorder fields

		if(Tools::isSubmit('upFieldorder')){

		

				$defaultLanguage =  intval(Configuration::get('PS_LANG_DEFAULT'));

				$fid			 =  intval(Tools::getValue('fid'));

				$languages = Language::getLanguages();

				$Listforms = Db::getInstance()->ExecuteS('SELECT `fdid` FROM `'._DB_PREFIX_.'contactform_item` WHERE `fid`='.$fid);

				

				foreach ($Listforms as $Listform){

					${'order_'.$Listform['fdid']}	=	intval(Tools::getValue('order_'.$Listform['fdid']));

					@Db::getInstance()->ExecuteS(' UPDATE `'._DB_PREFIX_.'contactform_item` SET `order`= '.${'order_'.$Listform['fdid']}.' WHERE `fdid`='.$Listform['fdid'].' ');

				}

				

				

		}





	if(Tools::isSubmit('subSavesql')){

		

			//Change file permission

			

			$fileName=dirname(__FILE__).'/library/sql/contactform.sql.txt';



			$sqldump		=	Tools::getValue('sqldump');

			$fp = fopen (dirname(__FILE__).'/library/sql/contactform.sql.txt', "w");

			$pursql='';

			fputs ($fp, $sqldump);

			fclose ($fp);

			

			//if file is writting

			//$this->_saveAs($fileName,'contactform.sql.txt');

			header('Content-disposition: attachment; filename=contactform.sql.txt'); 

			header('Content-Type: application/force-download'); 

			header("Content-Transfer-Encoding: binary"); 

			header("Pragma: no-cache");

			header("Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0");

			header("Expires: 0");

				ob_clean();

    			flush();

			readfile($fileName);

			exit;

		}



			//Import mamy

		if(Tools::isSubmit('subimportsql')){

		$tmp_file = $_FILES['txtimportsql']['tmp_name'];

		$type_file = $_FILES['txtimportsql']['type'];



			if( !is_uploaded_file($tmp_file) )

    			{

					return $this->displayError($this->l('File not found')).CFutils::_importForm($this->_path);

					exit;

    			}



			if($type_file!="text/plain")

				{

					return $this->displayError($this->l('Invalid file format')).CFutils::_importForm($this->_path);

					exit;

				}

			

			CFutils::_truncateAllTable();

			

			$fp = fopen ($tmp_file, "r");

			

			$contenu_du_fichier='';

			while(!feof($fp)) {

				$contenu_du_fichier .= fgets ($fp, 1255);

			}

			$allsql=explode('-- [CF_tag]',$contenu_du_fichier);

				for($i=0;$i<count($allsql);$i++){

					@Db::getInstance()->ExecuteS($allsql[$i]);

				}

			return $this->displayConfirmation($this->l('Data import completed successfully')).CFutils::_importForm($this->_path);

			fclose($fp);

		}



if(Tools::isSubmit('submitsettings')){

	

	$cfgrequired	=	Tools::getValue('cfgrequired');

	$cfgupload		=	Tools::getValue('cfgupload');

	$cfstyle		=	intval(Tools::getValue('cfstyle'));

	$cfgfwidth		=	intval(Tools::getValue('cfgfwidth'));

	$cfgradio		=	intval(Tools::getValue('cfgradio'));

	$cfgckbox		=	intval(Tools::getValue('cfgckbox'));

	

	Configuration::updateValue('CONTACTFORM_REQUIRED',$cfgrequired) ;

	Configuration::updateValue('CONTACTFORM_UPFORMAT',$cfgupload) ;

	Configuration::updateValue('CONTACTFORM_CFGCKBOX',$cfgckbox) ;

	Configuration::updateValue('CONTACTFORM_CFGRADIO',$cfgradio) ;

	Configuration::updateValue('CONTACTFORM_WIDTH',$cfgfwidth) ;

	Configuration::updateValue('CONTACTFORM_FORM',$cfstyle) ;

	

	Configuration::updateValue('CONTACTFORM_CAPTCHAWIDTH', intval(Tools::getValue('captchawidth'))) ;

	Configuration::updateValue('CONTACTFORM_CAPTCHAHEIGHT', intval(Tools::getValue('captchaheight'))) ;

	Configuration::updateValue('CONTACTFORM_CAPTCHALINE', intval(Tools::getValue('captchaline')));

	Configuration::updateValue('CONTACTFORM_CAPTCHAANGLE', intval(Tools::getValue('captchaangle')));

	Configuration::updateValue('CONTACTFORM_CAPTCHAOPACITY', intval(Tools::getValue('captchaopacity')));

	Configuration::updateValue('CONTACTFORM_CAPTCHACOPY', Tools::getValue('captchacopy'));

	Configuration::updateValue('CONTACTFORM_CAPTCHAWORD', intval(Tools::getValue('captchaword')));

	Configuration::updateValue('CONTACTFORM_CAPTCHANOISE', intval(Tools::getValue('captchanoise')));

	

	

	$content_dir = dirname(__FILE__).'/library/recaptcha/backgrounds/';

		switch(CFutils::_uploadimgFile($content_dir,'upcaptchabg','CONTACTFORM_CAPTCHABG',Tools::getValue('captchabg'),1)){

		case 1:

			echo $this->displayError($this->l('File not found'));

			$error=1;

		break;

		case 2:

			echo $this->displayError($this->l('Invalid file format'));

			$error=1;

		break;

		case 3:

			echo $this->displayError($this->l('Unable to copy the file in').$content_dir);

			$error=1;

		break;

		}

	

	

	

	$content_dir = dirname(__FILE__).'/library/recaptcha/fonts/';

		switch(CFutils::_uploadimgFile($content_dir,'upcaptchafont','CONTACTFORM_CAPTCHAFONT',Tools::getValue('captchfont'),2)){

		case 1:

			echo $this->displayError($this->l('File not found'));

			$error=1;

		break;

		case 2:

			echo $this->displayError($this->l('Invalid file format'));

			$error=1;

		break;

		case 3:

			echo $this->displayError($this->l('Unable to copy the file in').$content_dir);

			$error=1;

		break;

		}

	

	

	

	return $this->displayConfirmation($this->l('Settings were updated successfully')).CFutils::_settings($this->_path);



}	



if(Tools::isSubmit('mailsubmit')){



$mailadress  = Tools::getValue('mailadress');

$mailsubject = Tools::getValue('mailsubject');

$mailmessage = Tools::getValue('mailmessage');

$mailsender = Tools::getValue('mailsender');

$asc=Tools::getValue('asc','ASC');

$orderby=Tools::getValue('orderby','data_id');

$start=intval(Tools::getValue('start',0));

$pagelimit=intval(Tools::getValue('pagelimit',10));

 



$Syntaxe='#^[\w.-]+@[\w.-]+\.[a-zA-Z]{2,6}$#';



if($mailadress=='' ||$mailsubject=='' ||$mailmessage==''||$mailsender==''){

return $this->displayError($this->l('Please fulfill all fields')).CFutils::_seedata($this->_path,$asc,$orderby,$task,$pagelimit,$start);

}



elseif(!preg_match($Syntaxe,$mailadress)){

return $this->displayError($this->l('Invalid email address')).CFutils::_seedata($this->_path,$asc,$orderby,$task,$pagelimit,$start);

}

else{

	

$headers = "MIME-Version: 1.0\n";

$headers .= "Content-type: text/html; charset=iso-8859-1\n";



$headers .= "From: ".Configuration::get('PS_SHOP_NAME')." <".$mailsender.">\n";



$headers .= "Cc: ".Configuration::get('PS_SHOP_EMAIL')."\n";





// On envoi l’email

	if ( @mail($mailadress, $mailsubject, $mailmessage, $headers) )

		return $this->displayConfirmation($this->l('Mail sent')).CFutils::_seedata($this->_path,$asc,$orderby,$task,$pagelimit,$start);

   else 

		return $this->displayError($this->l('Failed sending email')).CFutils::_seedata($this->_path,$asc,$orderby,$task,$pagelimit,$start);



}





}





if(Tools::isSubmit('subeditcss')){

		$newcss=Tools::getValue('newcss');

		$mytoken=Tools::getValue('token');

		

		

	if(Configuration::get('CONTACTFORM_FORM')== 0)

		$fp = fopen (dirname(__FILE__).'/library/basicform/css/css.css', 'r+');

	else{

	if(Configuration::get('CONTACTFORM_STYLE')== 1)

			$fp = fopen (dirname(__FILE__).'/library/form/css/niceform.css', 'r+');

		else

			$fp = fopen (dirname(__FILE__).'/library/form/css/template.css', 'r+');

	}

		

			

		fputs ($fp, $newcss);

		fclose ($fp);

		header('Location:index.php?tab=AdminModules&configure=contactform&token='.$mytoken.'&task=editcss');

	}





		//-----------------------------TASK-----------------------------

		switch($task){

			

			case 'activateForm':

				return CFutils::_activateForm($this->_path);

			break;

			case 'disableForm':

				return CFutils::_disableForm($this->_path);

			break;

			case 'showformList':

				return CFTools::showformList($this->_path);

			break;

			case 'showformList2':

				if(intval(Tools::getValue('statut'))==0)

					return $this->displayConfirmation($this->l('Form was deleted successfully')).CFTools::showformList($this->_path);

				if(intval(Tools::getValue('statut'))==1)

					return $this->displayError($this->l('Error was occured where deleting the form')).CFTools::showformList($this->_path);

			break;

			case 'showfieldList':

				return CFTools::showfieldList($this->_path,$fid);

			break;

			case 'showfieldList2':

				if(intval(Tools::getValue('statut'))==1)

					return $this->displayConfirmation($this->l('Field was deleted successfully')).CFTools::showfieldList($this->_path,$fid);

				if(intval(Tools::getValue('statut'))==0)

					return $this->displayError($this->l('Error was occured where deleting the field')).CFTools::showfieldList($this->_path,$fid);

			break;

			case 'editform':

				return CFTools::editform($this->_path);

			break;

			

			case 'addfield':

				return CFTools::addfield($this->_path,intval(Tools::getValue('fid')));

			break;

			

            

			case 'infostatus':

				if(Configuration::get('CONTACTFORM_AUTOINFO')=='false')

					Configuration::updateValue('CONTACTFORM_AUTOINFO','true');

				else

					Configuration::updateValue('CONTACTFORM_AUTOINFO','false');

				return CFTools::frontpage($this->_path,$this->name,$this->version);

			break;

			

			case 'delform':

				return CFTools::_delForm($fid);

			break;

			case 'delfield':

				$fid=intval(Tools::getValue('fid'));

				$fdid=intval(Tools::getValue('fdid'));

				return CFTools::_delField($fid,$fdid);

			break;

			

			case 'changestatus':

				$status=intval(Tools::getValue('status'));

				$fdid=intval(Tools::getValue('fdid'));

				if($status==1)

					$updatestatus=0;

				else

					$updatestatus=1;

					CFTools::_changestatus($fdid,$updatestatus);

			break;

			

			case 'exportForm':

				return CFutils::_exportForm($this->_path);

			break;

			case 'restoreForm':

				return CFutils::_importForm($this->_path);

			break;

			case 'saveSql':

				$fileName=dirname(__FILE__)."/library/sql/contactform.sql.txt";

				CFutils::_saveAs($fileName,'contactform.sql.txt');

			break;

			case 'settings':

				return CFutils::_settings($this->_path);

			break;

			case 'btnActivecf':

				$mode=Tools::getValue('mode');

				CFutils::_btnActivecf($mode);

				return $this->displayConfirmation($this->l('Changes have been made on the activation button')).CFutils::_settings($this->_path);

			break;

			

			case 'btnDeactivecf':

				$mode=Tools::getValue('mode');

				CFutils::_btnDeactivecf($mode);

				return $this->displayConfirmation($this->l('Changes have been made on the activation button')).CFutils::_settings($this->_path);

			break;

			case 'saveHelp':

				$fileName=dirname(__FILE__).'/help/help_'.CFtools::getIsocode($cookie->id_lang).'.pdf';

					if(!file_exists($fileName))

						$fileName=dirname(__FILE__).'/help/help_es.pdf';

				CFutils::_saveAs($fileName,'contacform_'.CFtools::getIsocode($cookie->id_lang).'.pdf');

			break;

			case 'seedata':

				$asc=Tools::getValue('asc','ASC');

				$orderby=Tools::getValue('orderby','data_id');

				$start=intval(Tools::getValue('start',0));

				$pagelimit=intval(Tools::getValue('pagelimit',10));

				return CFutils::_seedata($this->_path,$asc,$orderby,$task,$pagelimit,$start);

			break;

			case 'deldata':

				$data_id=intval(Tools::getValue('data_id'));

				$asc=Tools::getValue('asc','ASC');

				$orderby=Tools::getValue('orderby','data_id');

				$start=intval(Tools::getValue('start',0));

				$pagelimit=intval(Tools::getValue('pagelimit',10));

				Db::getInstance()->ExecuteS('DELETE FROM '._DB_PREFIX_.'contactform_data WHERE data_id='.$data_id);

				return $this->displayConfirmation($this->l('Data deleted successfully')).CFutils::_seedata($this->_path,$asc,$orderby,$task,$pagelimit,$start);

			break;

			case 'seedatadetails':

				$data_id=intval(Tools::getValue('data_id'));

				return CFutils::_seedatadetails($this->_path,$data_id,$task);

			break;

			case 'seo':

				return CFutils::seo($this->_path);

			break;

			case 'addsample':

				return CFutils::_addsample($this->_path);

			break;

			case 'editcss':

				return CFutils::_editcss($this->_path);

			break;

			case 'importsample':

				$model=intval(Tools::getValue('model'));

				return CFutils::_importsample($model,$this->_path);

			break;

			

			default:

				return CFTools::frontpage($this->_path,$this->name,$this->version);

			break;

			

			

		}//Switch(task)



}//End of getContent

  

 



  

}//end class

  

  ?>