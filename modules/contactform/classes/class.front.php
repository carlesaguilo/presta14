<?php



include _PS_MODULE_DIR_.'contactform/library/mail/libmail.php';



class CFfront{

	

protected static $file_exists_cache = array();





public static function l($string, $specific = false)

	{

		global $_MODULES, $_MODULE, $cookie;

		$defaultLanguage = intval(Configuration::get('PS_LANG_DEFAULT'));

		$languages = Language::getLanguages();

		

		$id_lang = (!isset($cookie) OR !is_object($cookie)) ? intval(Configuration::get('PS_LANG_DEFAULT')) : intval($cookie->id_lang);



		$file = _PS_MODULE_DIR_.'contactform/'.Language::getIsoById($id_lang).'.php';

		if (self::file_exists_cache($file) AND include_once($file))

			$_MODULES = !empty($_MODULES) ? array_merge($_MODULES, $_MODULE) : $_MODULE;



		if (!is_array($_MODULES))

			return (str_replace('"', '&quot;', $string));



		$source = 'contactform';

		$string2 = str_replace('\'', '\\\'', $string);

		$currentKey = '<{contactform}'._THEME_NAME_.'>'.$source.'_'.md5($string2);

		$defaultKey = '<{contactform}prestashop>'.$source.'_'.md5($string2);



		if (key_exists($currentKey, $_MODULES))

			$ret = stripslashes($_MODULES[$currentKey]);

		elseif (key_exists($defaultKey, $_MODULES))

			$ret = stripslashes($_MODULES[$defaultKey]);

		else

			$ret = $string;

		return str_replace('"', '&quot;', $ret);

	}



public static function file_exists_cache($filename)

	{

		if (!isset(self::$file_exists_cache[$filename]))

			self::$file_exists_cache[$filename] = file_exists($filename);

		return self::$file_exists_cache[$filename];

	}







//



//=============================== BASIC FORM ==============================

public static function viewbasicForm($tabFields,$fid,$imgpath,$libpath){



global $cookie;

$output ='';

$output .= self::navigationPipe($fid);



$forms = Db::getInstance()->ExecuteS('SELECT cf.*, cfl.*

											 FROM `'._DB_PREFIX_.'contactform` cf 

											 LEFT JOIN `'._DB_PREFIX_.'contactform_lang` cfl  ON cf.`fid` = cfl.`fid` 

											 WHERE cfl.`id_lang`='.$cookie->id_lang.' AND  cf.`fid`='.$fid.' 

											 ');

											

$output .= '<div class="rte">'.$forms[0]['msgbeforeForm'].'</div>';









$Listforms = Db::getInstance()->ExecuteS('SELECT cf.*, cfl.*

											 FROM `'._DB_PREFIX_.'contactform_item` cf 

											 LEFT JOIN `'._DB_PREFIX_.'contactform_item_lang` cfl  ON cf.`fdid` = cfl.`fdid` 

											 WHERE cfl.`id_lang`='.$cookie->id_lang.' AND  cf.`fid`='.$fid.' AND cf.`published`=1

											 ORDER BY cf.`order` ASC

											 ');



$Listformsrequired = Db::getInstance()->ExecuteS('SELECT cf.*, cfl.*

											 FROM `'._DB_PREFIX_.'contactform_item` cf 

											 LEFT JOIN `'._DB_PREFIX_.'contactform_item_lang` cfl  ON cf.`fdid` = cfl.`fdid` 

											 WHERE cfl.`id_lang`='.$cookie->id_lang.' AND  cf.`fid`='.$fid.' AND cf.`published`=1 AND cf.`fields_require`=1

											 ORDER BY cf.`order` ASC

											 ');

$lastrequired =intval($Listformsrequired[count($Listformsrequired)-1]['fdid']);



$output .='<link rel="stylesheet" type="text/css" media="screen" href="'.$libpath .'basicform/css/css.css" />';

$output .='<script src="'. $libpath .'basicform/jquery.validate.js" type="text/javascript"></script>';







$output .='<script type="text/javascript">





	$().ready(function() {

		

		// validate signup form on keyup and submit

		$("#signupForm").validate({

			rules: {

';

			foreach($Listformsrequired as $required){

				$output .=$required['fields_name'].': "required",';

			}

			

			foreach($Listforms as $form){

				if($form['fields_valid']=='email' || $form['fields_type']=='email'){

					$output .= $form['fields_name'].': {

						'.($form['fields_require']==1?'required:true,':'').'

						email: true

					},';

					

				}

			}

			

			foreach($Listforms as $form){

				if($form['confirmation']==1 

					 && $form['fields_type']!='calendar' 

					 && $form['fields_type']!='radio' 

					 && $form['fields_type']!='checkbox'

					 && $form['fields_type']!='select'

					 && $form['fields_type']!='button'

					&& $form['fields_type']!='imagebtn'

					&& $form['fields_type']!='submitbtn'

					&& $form['fields_type']!='resetbtn'

					&& $form['fields_type']!='fileup'

					&& $form['fields_type']!='separator') 

					{

						$output .='re_'.$form['fields_name'].': {

								'.($form['fields_require']==1?'required:true,':'').'

								equalTo: "#'.$form['fields_id'].'"

							},';

					}

			}

			

							$output .='re_mycaptcha: {

								required:true

							}';

$output .='},';



$output .='messages: {

			re_mycaptcha :"<br>'.self::l('Fields not properly completed').'",

	

	';

			

foreach($Listforms as $form){

				if( $form['fields_type']!='radio'&& $form['fields_type']!='checkbox' && $form['fields_type']!='separator') 

					{

						$output .=$form['fields_name'].': "<br>'.(!empty($form['error_txt'])?$form['error_txt']:self::l('Fields not properly completed')).'",';

					}

			}

			

foreach($Listforms as $form){

				if($form['confirmation']==1 

					 && $form['fields_type']!='radio'

					 && $form['fields_type']!='checkbox'

					 && $form['fields_type']!='select'

					 && $form['fields_type']!='button'

					&& $form['fields_type']!='imagebtn'

					&& $form['fields_type']!='submitbtn'

					&& $form['fields_type']!='resetbtn'

					&& $form['fields_type']!='fileup'

					&& $form['fields_type']!='separator') 

					{

						$output .='re_'.$form['fields_name'].': "<br>'.(!empty($form['error_txt'])?$form['error_txt']:self::l('Fields not properly completed')).'",';

					}

					

			

			}

			



$output .='}';



//End ready function

$output .='});';

		



//End $.validator.setDefaults

$output .='});';



$output .='</script>';



$output .='

<script>

$(document).ready(function() {

	$("#signupForm").validate();

});

</script>



<style type="text/css">

form.cfsh label.cferror { display: none; }	

</style>';





$output .='<form enctype="multipart/form-data" class="cfsh" id="signupForm" method="POST" style="width:'.Configuration::get('CONTACTFORM_WIDTH').'px" action="">

<fieldset style=" border:none">

<table class="cfsh">

';

foreach ($Listforms as $form){

	

				if($form['fields_type']=='checkbox'){

					$key=array_search($form['fields_name'],$tabFields['name']);

					if(!empty($tabFields['value'][$key]))

						$defvalue=$tabFields['value'][$key];

					else

						$defvalue=array();

				}

				else{

										$key=array_search($form['fields_name'],$tabFields['name']);

					if(!empty($tabFields['value'][$key]))

						$defvalue=$tabFields['value'][$key];

					else

						$defvalue='';

				}

	

	if($form['fields_type']=='submitbtn'||$form['fields_type']=='imagebtn'||$form['fields_type']=='resetbtn'||$form['fields_type']=='separator')

		$point='';

	else

		$point=' : ';

	

	$output .='<tr>';

	if($form['fields_type']!='separator'){

	$output .='<td valign="top" class="right"><label for="'.$form['fields_name'].'">'.$form['fields_title'].$point.(!empty($form['fields_desc'])?self::info($libpath,$form['fields_desc']):'').' '.($form['fields_require']==1?'<sup style=" color:red">'.Configuration::get('CONTACTFORM_REQUIRED').'</sup>':'').'</label></td>';

	}

	

	

	

	switch($form['fields_type']){

		case 'separator':

				$output .='<td colspan=2 scop="rows"><div class="separator">'.$form['fields_default'].'</div></td>';

		break;

		case 'text':

			$output .='<td><input type="text" id="'.$form['fields_id'].'" name="'.$form['fields_name'].'" value="'.($defvalue!=''?$defvalue:$form['fields_default']).'" '.$form['fields_suppl'].' /></td>';

		break;

		case 'email':

			$output .='<td><input type="text" id="'.$form['fields_id'].'" name="'.$form['fields_name'].'" value="'.($defvalue!=''?$defvalue:$form['fields_default']).'"  '.$form['fields_suppl'].'/></td>';

		break;

		case 'textarea':

			$output .='<td><textarea id="'.$form['fields_id'].'" name="'.$form['fields_name'].'"   '.$form['fields_suppl'].'/>'.($defvalue!=''?$defvalue:$form['fields_default']).'</textarea></td>';

		break;

		case 'password':

			$output .='<td><input type="password" id="'.$form['fields_id'].'" name="'.$form['fields_name'].'" value="'.$form['fields_default'].'"  '.$form['fields_suppl'].'></td>';

		break;

		case 'select':

					$output .='<td><select name="'.$form['fields_name'].'" id="'.$form['fields_id'].'"  '.$form['fields_suppl'].'>';

						$options=explode(';',$form['fields_default']);

						for($i=0;$i<count($options);$i++){

								$output .='<option value="'.$options[$i].'" '.($defvalue==$options[$i]?'selected':'').' >'.$options[$i].'</option>';

						}

					

					$output .='</select></td>';

		break;

		case 'country':

					$states = Db::getInstance()->ExecuteS('SELECT `name` FROM `'._DB_PREFIX_.'country_lang` ORDER BY  `name` ASC');

					$output .='<td><select name="'.$form['fields_name'].'" id="'.$form['fields_id'].'"  '.$form['fields_suppl'].'>';

						$output .='<option value=""> ------------------ </option>';

						

						foreach($states as $state){

								$output .='<option value="'.$state['name'].'" '.($defvalue==$state['name']?'selected':'').'>'.$state['name'].'</option>';

						}

					

					$output .='</select></td>';

					

		break;

		case 'calendar':

					if(CFfront::getIsocode($cookie->id_lang)=='fr')

						$ifformat='%d/%m/%Y';

					else

						$ifformat='%Y/%m/%d';

					

						

					$output .='	<script type="text/javascript">

					jQuery(document).ready(function() {

						jQuery("#'.$form['fields_id'].'").dynDateTime({

							showsTime: false,

							ifFormat: "'.$ifformat.'",

							daFormat: "%l;%M %p, %e %m,  %Y",

							electric: false,

							singleClick: true,

							displayArea: ".siblings(\'.dtcDisplayArea\')",

							button: ".next()" //next sibling

						});

					});

				</script>

				<td><input type="text" name="'.$form['fields_name'].'" id="'.$form['fields_id'].'" value="'.($defvalue!=''?$defvalue:$form['fields_default']).'"/>

				<input  type="button" value="...">';

				$output .='<br><label for="'.$form['fields_name'].'" class="cferror">'.(!empty($form['error_txt'])?$form['error_txt']:self::l('Fields not properly completed')).'</label></td>';

				

			break;

			case 'radio':

						$output .='<td>';

						$options=explode(';',$form['fields_default']);

						for($i=0;$i<count($options);$i++){

							

								$output .='<input '.($defvalue==$options[$i]?'checked':'').' class="radio"  type="radio" id="'.$form['fields_id'].$i.'" value="'.$options[$i].'" name="'.$form['fields_name'].'" />'.$options[$i].(Configuration::get('CONTACTFORM_CFGRADIO')==1?'':'<br>');

						}

						

						$output .='<br><label  for="'.$form['fields_name'].'" class="cferror">'.(!empty($form['error_txt'])?$form['error_txt']:self::l('Fields not properly completed')).'</label>';

						$output .='</td>';

				break;

				

				case 'checkbox':

						$options=explode(';',$form['fields_default']);

						$output .='<td>';

						for($i=0;$i<count($options);$i++){

							

								$output .='<input '.(in_array($options[$i],$defvalue)?'checked="checked"':'').' class="checkbox" type="checkbox" id="'.$form['fields_id'].$i.'" value="'.$options[$i].'" name="'.$form['fields_name'].'[]" />'.$options[$i].(Configuration::get('CONTACTFORM_CFGRADIO')==1?'':'<br>');

						}

						

						$output .='<br><label  for="'.$form['fields_name'].'" class="cferror">'.(!empty($form['error_txt'])?$form['error_txt']:self::l('Fields not properly completed')).'</label>';

						$output .='</td>';

				break;

				case 'captcha':

						$noise=intval(Configuration::get('CONTACTFORM_CAPTCHANOISE'))/100;

						$useword=(intval(Configuration::get('CONTACTFORM_CAPTCHAWORD'))==1?'true':'false' );

						$output .='<td class="scaptcha"><img  id="siimage" align="left" style="padding-right: 5px; border: 0" src="'.$libpath.'recaptcha/securimage_show_example.php?line='.Configuration::get('CONTACTFORM_CAPTCHALINE').'&width='.Configuration::get('CONTACTFORM_CAPTCHAWIDTH').'&height='.Configuration::get('CONTACTFORM_CAPTCHAHEIGHT').'&angle='.Configuration::get('CONTACTFORM_CAPTCHAANGLE').'&opacity='.Configuration::get('CONTACTFORM_CAPTCHAOPACITY').'&copy='.Configuration::get('CONTACTFORM_CAPTCHACOPY').'&bg='.Configuration::get('CONTACTFORM_CAPTCHABG').'&font='.Configuration::get('CONTACTFORM_CAPTCHAFONT').'&noise='.$noise.'&useword='.$useword.'&sid='.md5(time()).'" />

						

						<a tabindex="-1" style="border-style: none" href="#" title="Refresh Image" onClick="document.getElementById(\'siimage\').src = \''.$libpath.'recaptcha/securimage_show_example.php?line='.Configuration::get('CONTACTFORM_CAPTCHALINE').'&width='.Configuration::get('CONTACTFORM_CAPTCHAWIDTH').'&height='.Configuration::get('CONTACTFORM_CAPTCHAHEIGHT').'&angle='.Configuration::get('CONTACTFORM_CAPTCHAANGLE').'&opacity='.Configuration::get('CONTACTFORM_CAPTCHAOPACITY').'&copy='.Configuration::get('CONTACTFORM_CAPTCHACOPY').'&bg='.Configuration::get('CONTACTFORM_CAPTCHABG').'&font='.Configuration::get('CONTACTFORM_CAPTCHAFONT').'&noise='.$noise.'&useword='.$useword.'&sid=\' + Math.random(); return false"><img src="'.$libpath.'recaptcha/images/refresh.png" alt="Reload Image" border="0" onClick="this.blur()" align="bottom" /></a>';

						

						

					$output .='</td>';

				break;

				

		case 'fileup':

					$output .='<td><input  class="file" type="file" id="'.$form['fields_id'].'" name="'.$form['fields_name'].'" value="'.$form['fields_default'].'"   '.$form['fields_suppl'].'></td>';

				break;

				case 'submitbtn';

					$output .='<td><input name="submitform" value="'.$form['fields_default'].'" class="submit" type="submit"  id="Send"/></td>';

				break;

				case 'imagebtn';

					$output .='<td><input name="submitform" value="'.$form['fields_default'].'" class="submit" type="image"  id="Send"/></td>';

				break;

				case 'resetbtn';

					$output .='<td><input name="'.$form['fields_name'].'" value="'.$form['fields_default'].'" class="submit" type="reset"  /></td>';

				break;

		

		

	}//End switch

	$output .='</tr>';

	

				if($form['fields_type']=='captcha') {

				$output .='<tr>';

				$output .='<td><label for="'.$form['fields_name'].'">'.$form['confirmation_txt'].' :</label></td>';

				$output .='<td><input '.$form['fields_suppl'].' size=10 type="text" name="re_mycaptcha" id="re_mycaptcha" /></td>';

				$output .='</tr>';

			

			}

				

				

				

				if($form['confirmation']==1 

					 && $form['fields_type']!='captcha' 

					 && $form['fields_type']!='calendar' 

					 && $form['fields_type']!='radio' 

					 && $form['fields_type']!='checkbox'

					 && $form['fields_type']!='select'

					 && $form['fields_type']!='button'

					&& $form['fields_type']!='imagebtn'

					&& $form['fields_type']!='submitbtn'

					&& $form['fields_type']!='resetbtn'

					&& $form['fields_type']!='fileup'

					&& $form['fields_type']!='separator') 

					{

						$output .='<tr>';

						$output .='<td><label for="'.$form['fields_id'].'">'.$form['confirmation_txt'].' :</label></td>';

						$output .='<td><input value="" type="'.($form['fields_type']=='password'?'password':'text').'" name="re_'.$form['fields_name'].'" id="re_'.$form['fields_id'].'" /></td>';

						$output .='</tr>';

					}

	

	

	

	

}//End foreach



$output .='</table>';

//$output .='<p><input class="submit" type="submit" value="Submit"/></p>';

$output .='<input class="submit" type="hidden" value="'.$fid.'" name="fid"/>';

$output .='	</fieldset>

			</form>';





$output .= '<div class="rte">'.$forms[0]['msgafterForm'].'</div>';



//Preloading

/*

$output .='</div>';

$output .='<script type="text/javascript">

	QueryLoader.selectorPreload = "#cfform";

	QueryLoader.init();

</script>';*/



return $output;



}



// ============================== HIGH LEVEL FORM =================================

public static function viewForm($tabFields,$fid,$imgpath,$libpath){



global $cookie;

$output = self::navigationPipe($fid);



$forms = Db::getInstance()->ExecuteS('SELECT cf.*, cfl.*

											 FROM `'._DB_PREFIX_.'contactform` cf 

											 LEFT JOIN `'._DB_PREFIX_.'contactform_lang` cfl  ON cf.`fid` = cfl.`fid` 

											 WHERE cfl.`id_lang`='.$cookie->id_lang.' AND  cf.`fid`='.$fid.' 

											 ');

											

											$output .= '<div class="rte">'.$forms[0]['msgbeforeForm'].'</div>';





$output .= '<form enctype="multipart/form-data" id="formID" class="formular" method="post" action="" style="width:'.Configuration::get('CONTACTFORM_WIDTH').'px">';

	

	$Listforms = Db::getInstance()->ExecuteS('SELECT cf.*, cfl.*

											 FROM `'._DB_PREFIX_.'contactform_item` cf 

											 LEFT JOIN `'._DB_PREFIX_.'contactform_item_lang` cfl  ON cf.`fdid` = cfl.`fdid` 

											 WHERE cfl.`id_lang`='.$cookie->id_lang.' AND  cf.`fid`='.$fid.' AND cf.`published`=1

											 ORDER BY cf.`order` ASC

											 ');

	

		foreach ($Listforms as $form){

			

			

			if($form['fields_type']=='checkbox'){

					$key=array_search($form['fields_name'],$tabFields['name']);

					if(!empty($tabFields['value'][$key]))

						$defvalue=$tabFields['value'][$key];

					else

						$defvalue=array();

				}

				else{

					$key=array_search($form['fields_name'],$tabFields['name']);

					if(!empty($tabFields['value'][$key]))

						$defvalue=$tabFields['value'][$key];

					else

						$defvalue='';

				}

				

				

				if($form['fields_type']=='submitbtn'||$form['fields_type']=='imagebtn'||$form['fields_type']=='resetbtn')

					$point='';

				else

					$point=' : ';

					

				if($form['fields_type']!='radio'&&$form['fields_type']!='checkbox') 

				 	$output .='<label>';

				

				if($form['fields_type']!='separator'){

				$output .='<span>'.$form['fields_title'].$point.(!empty($form['fields_desc'])?self::info($libpath,$form['fields_desc']):'').'</span>'.($form['fields_require']==1?'<sup style=" color:red">'.Configuration::get('CONTACTFORM_REQUIRED').'</sup>':'');

				}

				

				if($form['fields_type']=='radio'||$form['fields_type']=='checkbox') 

				 	$output .='<br>';

				

				$class='validate[';

			

			//Prepare class

			if($form['fields_require']==1)

				$class .='required';

			else

				$class .='optional';

			

			if($form['fields_valid']=='email' || $form['fields_type']=='email')

				$class .=',custom[email]';

			elseif($form['fields_valid']=='url')

				$class .=',custom[url]';

			elseif($form['fields_valid']=='numeric')

				$class .=',custom[integer]';

			elseif($form['fields_valid']=='alpha')

				$class .=',custom[onlyLetterSp]';

				

			//End preparing classes	

			$class .=']';

				

			switch($form['fields_type']){

				case 'separator':

				$output .='<div class="separator">'.$form['fields_default'].'</div>';

				break;

				

				case 'text':

					$class .=' text-input';

					$output .='<input type="text" id="'.$form['fields_id'].'" name="'.$form['fields_name'].'" value="'.($defvalue!=''?$defvalue:$form['fields_default']).'"  class="'.$class.'"  '.$form['fields_suppl'].' >';

				break;

				

				case 'password':

					$class .=' text-input';

					$output .='<input type="password" id="'.$form['fields_id'].'" name="'.$form['fields_name'].'" value="'.$form['fields_default'].'"  class="'.$class.'"  '.$form['fields_suppl'].'>';

				break;

				

				case 'select':

					$class .=' text-select';

					$output .='<select name="'.$form['fields_name'].'" id="'.$form['fields_id'].'" class="'.$class.'" '.$form['fields_suppl'].'>';

						$options=explode(';',$form['fields_default']);

						for($i=0;$i<count($options);$i++){

								$output .='<option value="'.$options[$i].'" '.($defvalue==$options[$i]?'selected':'').'>'.$options[$i].'</option>';

						}

					

					$output .='</select>';

				break;

				

				case 'country':

					$class .=' text-select';

					$states = Db::getInstance()->ExecuteS('SELECT `name` FROM `'._DB_PREFIX_.'country_lang` ORDER BY  `name` ASC');

					$output .='<select name="'.$form['fields_name'].'" id="'.$form['fields_id'].'" class="'.$class.'" '.$form['fields_suppl'].'>';

						$output .='<option value=""> ------------------ </option>';

						

						foreach($states as $state){

								$output .='<option value="'.$state['name'].'"  '.($defvalue==$state['name']?'selected':'').'>'.$state['name'].'</option>';

						}

					

					$output .='</select>';

					

				break;

				

				case 'email':

					$class .=' text-input';

					$output .='<input type="text" id="'.$form['fields_id'].'" name="'.$form['fields_name'].'"  class="'.$class.'"  '.$form['fields_suppl'].' value="'.($defvalue!=''?$defvalue:$form['fields_default']).'">';

				break;

				

				case 'textarea':

					$output .='<textarea '.$form['fields_suppl'].'  class="'.$class.'"  id="'.$form['fields_id'].'" name="'.$form['fields_name'].'" >'.($defvalue!=''?$defvalue:$form['fields_default']).'</textarea>';

				break;

				

				case 'calendar':

					$class .=' text-input';	

					if(CFfront::getIsocode($cookie->id_lang)=='fr')

						$ifformat='%d/%m/%Y';

					else

						$ifformat='%Y/%m/%d';

					

						

					$output .='	<script type="text/javascript">

					jQuery(document).ready(function() {

						jQuery("#'.$form['fields_id'].'").dynDateTime({

							showsTime: false,

							ifFormat: "'.$ifformat.'",

							daFormat: "%l;%M %p, %e %m,  %Y",

							electric: false,

							singleClick: true,

							displayArea: ".siblings(\'.dtcDisplayArea\')",

							button: ".next()" //next sibling

						});

					});

				</script>

				<br><input style=" width:150px; display:inline" class="'.$class.'" type="text" name="'.$form['fields_name'].'" id="'.$form['fields_id'].'" value="'.($defvalue!=''?$defvalue:$form['fields_default']).'"/>

				<input style="display:inline; background:url('.$imgpath.'ccalendar.png) no-repeat bottom; border:none; width:26px; height:26px"  type="button" value=""><br>';

				

				break;

				case 'captcha':

				$class .=' text-input';

				

				$noise=intval(Configuration::get('CONTACTFORM_CAPTCHANOISE'))/100;

						$useword=(intval(Configuration::get('CONTACTFORM_CAPTCHAWORD'))==1?'true':'false' );

						$output .='<br><img  id="siimage" align="left" style="padding-right: 5px; border: 0" src="'.$libpath.'recaptcha/securimage_show_example.php?line='.Configuration::get('CONTACTFORM_CAPTCHALINE').'&width='.Configuration::get('CONTACTFORM_CAPTCHAWIDTH').'&height='.Configuration::get('CONTACTFORM_CAPTCHAHEIGHT').'&angle='.Configuration::get('CONTACTFORM_CAPTCHAANGLE').'&opacity='.Configuration::get('CONTACTFORM_CAPTCHAOPACITY').'&copy='.Configuration::get('CONTACTFORM_CAPTCHACOPY').'&bg='.Configuration::get('CONTACTFORM_CAPTCHABG').'&font='.Configuration::get('CONTACTFORM_CAPTCHAFONT').'&noise='.$noise.'&useword='.$useword.'&sid='.md5(time()).'" />

						

						<a tabindex="-1" style="border-style: none" href="#" title="Refresh Image" onClick="document.getElementById(\'siimage\').src = \''.$libpath.'recaptcha/securimage_show_example.php?line='.Configuration::get('CONTACTFORM_CAPTCHALINE').'&width='.Configuration::get('CONTACTFORM_CAPTCHAWIDTH').'&height='.Configuration::get('CONTACTFORM_CAPTCHAHEIGHT').'&angle='.Configuration::get('CONTACTFORM_CAPTCHAANGLE').'&opacity='.Configuration::get('CONTACTFORM_CAPTCHAOPACITY').'&copy='.Configuration::get('CONTACTFORM_CAPTCHACOPY').'&bg='.Configuration::get('CONTACTFORM_CAPTCHABG').'&font='.Configuration::get('CONTACTFORM_CAPTCHAFONT').'&noise='.$noise.'&useword='.$useword.'&sid=\' + Math.random(); return false"><img src="'.$libpath.'recaptcha/images/refresh.png" alt="Reload Image" border="0" onClick="this.blur()" align="bottom" /></a>

<br clear="all" /><br clear="all" />					

						';

				break;

				case 'radio':

				

				$class .=' radio';

				

						$options=explode(';',$form['fields_default']);

						for($i=0;$i<count($options);$i++){

								$output .='<input '.($defvalue==$options[$i]?'checked':'').' '.$form['fields_suppl'].' style=" display:'.(Configuration::get('CONTACTFORM_CFGRADIO')==1?'inline':'block').'" id="'.$form['fields_id'].$i.'" class="'.$class.'" type="radio" value="'.$options[$i].'" name="'.$form['fields_name'].'">'.$options[$i];

						}

						

						$output .='<br>';

				break;

				

				case 'checkbox':

				$class .=' checkbox';

						$options=explode(';',$form['fields_default']);

						for($i=0;$i<count($options);$i++){

								$output .='<input '.(in_array($options[$i],$defvalue)?'checked="checked"':'').' '.$form['fields_suppl'].' style="display:'.(Configuration::get('CONTACTFORM_CFGCKBOX')==1?'inline':'block').'" id="'.$form['fields_id'].$i.'" class="'.$class.'" type="checkbox" value="'.$options[$i].'" name="'.$form['fields_name'].'[]">'.$options[$i];

						}

						$output .='<br>';

				break;

				

				case 'fileup':

					$class .=' text-input';

					$output .='<input type="file" id="'.$form['fields_id'].'" name="'.$form['fields_name'].'" value="'.$form['fields_default'].'"  class="'.$class.'"  '.$form['fields_suppl'].'>';

				break;

				case 'submitbtn';

					$output .='<input name="submitform" value="'.$form['fields_default'].'" class="submit" type="submit"  id="Send"/>';

				break;

				case 'imagebtn';

					$output .='<input name="submitform" value="'.$form['fields_default'].'" class="submit" type="image"  id="Send"/>';

				break;

				case 'resetbtn';

					$output .='<input name="'.$form['fields_name'].'" value="'.$form['fields_default'].'" class="submit" type="reset"  />';

				break;

				

			}

			

			if($form['fields_type']!='radio'&&$form['fields_type']!='checkbox')

				$output .='</label>';

			

			if($form['fields_type']=='captcha') {

				$output .='<label>';

				$output .='<span>'.$form['confirmation_txt'].' :</span>';

				$output .='<input '.$form['fields_suppl'].' size=10 value="" class="validate[required] text-input" type="text" name="re_mycaptcha" id="'.$form['fields_name'].'" />';

				$output .='</label>';

			

			}

			

			

			if($form['confirmation']==1 

					 && $form['fields_type']!='captcha' 

					 && $form['fields_type']!='password' 

					 && $form['fields_type']!='calendar' 

					 && $form['fields_type']!='radio' 

					 && $form['fields_type']!='checkbox'

					 && $form['fields_type']!='select'

					 && $form['fields_type']!='button'

					&& $form['fields_type']!='imagebtn'

					&& $form['fields_type']!='submitbtn'

					&& $form['fields_type']!='resetbtn'

					&& $form['fields_type']!='fileup'

					&& $form['fields_type']!='separator') 

				{

				$output .='<label>';

				$output .='<span>'.$form['confirmation_txt'].' :</span>';

				$output .='<input value="" class="validate[required,equals['.$form['fields_name'].']] text-input" type="text" name="re_'.$form['fields_name'].'" id="re_'.$form['fields_name'].'" />';

				$output .='</label>';

			

			}

			

			if($form['confirmation']==1 && $form['fields_type']=='password') {

				$output .='<label>';

				$output .='<span>'.$form['confirmation_txt'].' :</span>';

				$output .='<input value="" class="validate[required,equals['.$form['fields_id'].']] text-input" type="password" name="re_'.$form['fields_name'].'" id="re_'.$form['fields_name'].'" />';

				$output .='</label>';

			

			}

		

		}//End foreach

		

	$output .= '<input class="submit" type="hidden" value="'.$fid.'" name="fid"/><hr/>';

$output .= '<hr/></form>';



		$output .= '<div class="rte">'.$forms[0]['msgafterForm'].'</div>';



return $output;

	

	

	

}







	

 public static function getIsocode($id_lang){

  	$defaultLanguage = intval(Configuration::get('PS_LANG_DEFAULT'));

	$languages = Language::getLanguages();

	for($i=0; $i< count($languages); $i++){

		if($languages[$i]['id_lang']==$id_lang)

			$iso_code=$languages[$i]['iso_code'];

	}

  	return $iso_code;

  

  }



public static function info($libpath,$info){

	$output = '<link rel="stylesheet" type="text/css" href="'.$libpath.'info/vtip.css" />';

	$output.='<script type="text/javascript" src="'.$libpath.'info/vtip.js"></script>';

	

	$output.='<img src="'.$libpath.'info/info.png" title="'.$info.'" class="vtip" />';	

	return $output;



}







public function displayError($error)

	{

	 	$output = '<div  style="border:2px solid red; padding:10px; margin:10px; background:#FAE2E3; color:red">';

		foreach($error as $err){

			$output .= '<img src="'.__PS_BASE_URI__.'modules/contactform/img/unchecked.gif" alt="X" title="" /> '.$err.'<br>';

		}

		$output .= '</div>';

		return $output;

	}



public static function navigationPipe($fid){

	global $cookie;

	if (@eregi('http://', $_SERVER['HTTP_HOST'])) 

    	$host=$_SERVER['HTTP_HOST'].__PS_BASE_URI__;

	else

		$host='http://'.$_SERVER['HTTP_HOST'].__PS_BASE_URI__;

	$forms = Db::getInstance()->ExecuteS('SELECT cf.*, cfl.*

											 FROM `'._DB_PREFIX_.'contactform` cf 

											 LEFT JOIN `'._DB_PREFIX_.'contactform_lang` cfl  ON cf.`fid` = cfl.`fid` 

											 WHERE cfl.`id_lang`='.$cookie->id_lang.' AND  cf.`fid`='.$fid.' 

											 ');

	

	

	$output = '<div class="breadcrumb" style=" margin:10px">';

	$output .='<a href="'.$host.'">'.self::l('Home').'</a>';

	$output .= '<span class="navigation-pipe">&gt;</span>';

	$output .= $forms[0]['formtitle'];

	$output .= '</div>';

		return $output;

	

	}





  public static function _SendMail($tabFields,$fid){

  	

	global $cookie;

	$shopName=Configuration::get('PS_SHOP_NAME');

	$layout = Db::getInstance()->ExecuteS(' SELECT * FROM `'._DB_PREFIX_.'contactform` WHERE `fid`='.$fid);

	

	$output='';

	$Customermail=array();

	$msgform='';

 	$attachement='';

	

	$defaultlayoutseller ='<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/1999/REC-html401-19991224/strict.dtd">

<html>

<head>

	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

	<title>'.self::l('Message from your shop').' {shop_name}</title>

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

			<td align="left" style="background-color:#DB3484; color:#FFF; font-size: 12px; font-weight:bold; padding: 0.5em 1em;">{contactform_in}  {form_name}</td>

		</tr>

		<tr><td>&nbsp;</td></tr>

		<tr>

			<td>

			{here_msg} :</br>

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





$defaultlayoutcustomer ='<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/1999/REC-html401-19991224/strict.dtd">

<html>

<head>

	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

	<title>{notification} {shop_name}</title>

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

			<td align="left" style="background-color:#DB3484; color:#FFF; font-size: 12px; font-weight:bold; padding: 0.5em 1em;">'.self::l('Notification message from.').' {shop_name}</td>

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

	

	//Retrieve all parameters to send by mail 

	for($i=0; $i <count($tabFields['name']); $i++){

				if($tabFields['fields_valid'][$i]=='email'||$tabFields['fields_type'][$i]=='email'){

					array_push($Customermail,$tabFields['value'][$i]);

				}

	}

	if(count($Customermail)>0)

		$mailCustom = $Customermail[0];

	else

		$mailCustom = $shopName;

	 

	 

	 /* =================================== MSG TO SELLER =====================================*/

	 //Mail to the Seller

	$mailSeller=$layout[0]['email'];

	$subjectSeller=self::l('CONTACT FORM ON').' '.$shopName;

	$msgSeller='';

	

	for($i=0; $i<count($tabFields['label']); $i++){

			if(strpos($tabFields['name'][$i],'re-')===false&&$tabFields['fields_type'][$i]!='separator'&&$tabFields['fields_type'][$i]!='submitbtn'){

						if($tabFields['fields_type'][$i]=='checkbox'){

							$nbofck=count($tabFields['value'][$i]);

							$msgform .= '<b>'.$tabFields['label'][$i].'</b> : ';

							$msgform .= '<ul>';

							for($k=0;$k<$nbofck;$k++)

								{$msgform .= '<li>'.$tabFields['value'][$i][$k].'</li>';}

							$msgSeller .= '</ul>';

						}

						else

							{$msgform .= '<b>'.$tabFields['label'][$i].'</b> : '.$tabFields['value'][$i].'<br>';}

			}

		}

	

	//Purge $msgSeller;

	if (@eregi('http://', $_SERVER['HTTP_HOST'])) 

    	$host=$_SERVER['HTTP_HOST'];

	else

		$host='http://'.$_SERVER['HTTP_HOST'];

		

		

	$msgSeller .= "\n\n";

	if($layout[0]['layout']=='' || empty($layout[0]['layout']))

		$layoutseller = $defaultlayoutseller;

	else

		$layoutseller = $layout[0]['layout'];

		

		

		$layoutseller = str_replace('{shop_name}',$shopName,$layoutseller);

	    $layoutseller = str_replace('{message}',$msgform,$layoutseller);

		$layoutseller = str_replace('{form_name}',$layout[0]['formname'],$layoutseller);

		$layoutseller = str_replace('{message_from}',self::l('Message from'),$layoutseller);

		$layoutseller = str_replace('{contactform_in}',self::l('CONTACT ON YOUR FORM'),$layoutseller);

		$layoutseller = str_replace('{intro}',self::l('A visitor has sent a message from your form'),$layoutseller);

		$layoutseller = str_replace('{shop_logo}',$host.__PS_BASE_URI__.'img/logo.jpg',$layoutseller);

		$layoutseller = str_replace('{copyright}',self::l('Mail generated by'),$layoutseller); 

		$layoutseller = str_replace('{shop_url}',$host,$layoutseller);

		$layoutseller = str_replace('{here_msg}',self::l('Here is the message sent'),$layoutseller);

		

	$msgSeller .=$layoutseller;

	$msgSeller .= "\n\n";

	$msgSeller=utf8_decode($msgSeller) ;

	



	$m = new Mail; // create the mail

	$m->From( $shopName,$mailCustom );

	//	$m->To( "develop@aretmic.com" );

	$AllmailSeller=explode(';',$mailSeller);

	 	for($i=0;$i<count($AllmailSeller);$i++){

			$m->To($AllmailSeller[$i]);	

		}

	$m->Subject( $subjectSeller );

	$m->Body($msgSeller); 

	$m->Priority(1) ; 

	//Attachement

	for($i=0; $i <count($tabFields['name']); $i++){

		if($tabFields['fields_type'][$i]=='fileup'&&$tabFields['value'][$i]!=''){

			$filetoattach =$tabFields['value'][$i];

			$filesParams = explode('+',$filetoattach);

			$type= $filesParams[1];

				if($type=='application/x-download')

					$type='application/pdf';

				$filetocopy=_PS_MODULE_DIR_.'contactform/upload/'.$filesParams[2];

					if(copy($filesParams[0],$filetocopy))

						$m->Attach($filetocopy, $type, "attachment" );

					else

						$m->Attach($filesParams[0], $type, "attachment" ) ; 

			}

	}

	

	$mail1 = $m->Send();

	if($mail1){

			echo self::_upDataInfo($tabFields,$fid,'mail');}

		else{

			echo self::_upDataInfo($tabFields,$fid,'notmail');}

		

	self::cleanTmp();

	

			

	/* = ================================ NOTIFICATION MAIL TO THE CUSTOME ============================================ = */

	

	$lastparams = Db::getInstance()->ExecuteS('SELECT cf.`email`,cf.`formname`, cfl.*

											 FROM `'._DB_PREFIX_.'contactform` cf 

											 LEFT JOIN `'._DB_PREFIX_.'contactform_lang` cfl  ON cf.`fid` = cfl.`fid` 

											 WHERE cfl.`id_lang`='.$cookie->id_lang.' AND  cf.`fid`='.$fid.'

											 ');



		

		$headers ='From: "Contact '.$shopName.'" <'.$AllmailSeller[0].'>'."\n";

		$headers.= "MIME-Version: 1.0\n";

     	$headers .="Content-Type: text/html; charset=\"iso-8859-1\"\n";

     	$headers .="Content-Transfer-Encoding: quoted-printable\n "; 

		$headers .= "Content-Disposition: inline \n\n ";

		

		

		

		if($layout[0]['clayout']=='' || empty($layout[0]['clayout']))

			$layoutcustomer = $defaultlayoutcustomer;

		else

			$layoutcustomer = $layout[0]['clayout'];

			

			$layoutcustomer = str_replace('{shop_name}',$shopName,$layoutcustomer);

			$layoutcustomer = str_replace('{message}',stripslashes(utf8_decode($lastparams[0]['automailresponse'])),$layoutcustomer);

			$layoutcustomer = str_replace('{shop_logo}',$host.__PS_BASE_URI__.'img/logo.jpg',$layoutcustomer);

			$layoutcustomer = str_replace('{copyright}',self::l('Mail generated by'),$layoutcustomer); 

			$layoutcustomer = str_replace('{notification}',self::l('Notification message from'),$layoutcustomer); 

			$layoutcustomer = str_replace('{shop_url}',$host,$layoutcustomer);

			$layoutcustomer = str_replace('{message_from}','',$layoutcustomer);

			$layoutcustomer = str_replace('{contactform_in}','',$layoutcustomer);

			$layoutcustomer = str_replace('{here_msg}','',$layoutcustomer);

			$layoutcustomer = str_replace('{form_name}','',$layoutcustomer);

			

			for($i=0; $i<count($tabFields['name']); $i++){

				if($tabFields['fields_valid'][$i]=='email'||$tabFields['fields_type'][$i]=='email'){

					if(!@ereg('re_',$tabFields['name'][$i])){

							$mail2=@mail($tabFields['value'][$i],$lastparams[0]['subject'],$layoutcustomer,$headers);

					

					}

				}

			}

			

			

	if($mail1){

				if(file_exists(_PS_ROOT_DIR_.'/thankyou.php'))

					header('location:'.__PS_BASE_URI__.'thankyou.php?fid='.$fid);

				else{

					

					echo '<div class="rte">'.$lastparams[0]['thankyou'].'</div>';

					if(!empty($lastparams[0]['returnurl']))

					echo '<br><br><center><a href="'.$lastparams[0]['returnurl'].'" class="returnurl">'.self::l('Back').'</a></center><br>';

					

				}

			}

			else{

				$error=array();

				$error[]=self::l('Errors occurred when sending mail. Please contact the').' '.'<a class="link" href="mailto:'.Configuration::get('PS_SHOP_EMAIL').'">'.self::l('the administrator').'</a>' .' '.self::l('of the site or try later');

				echo self::displayError($error);

				

			}

  

 

  

  }









  public static function _upDataInfo($tabFields,$fid,$statut){

  	

	global $cookie;

	$ip=self::get_ip();

	$params= Db::getInstance()->ExecuteS(' SELECT * FROM `'._DB_PREFIX_.'contactform` WHERE `fid`='.$fid);

	$mailSeller=$params[0]['email'];

	

	for($i=0; $i <count($tabFields['name']); $i++){

				if($tabFields['fields_valid'][$i]=='email'||$tabFields['fields_type'][$i]=='email'){

					$mailCustomer=$tabFields['value'][$i];

				}

	}

	

	

	$msgSeller='';

	for($i=0; $i<count($tabFields['label']); $i++){

			if(strpos($tabFields['name'][$i],'re-')===false){

				if($tabFields['fields_type'][$i]=='checkbox'){

					$nbofck=count($tabFields['value'][$i]);

					$msgSeller .= '<b>'.$tabFields['label'][$i].'</b> : ';

					$msgSeller .= '<ul>';

					for($k=0;$k<$nbofck;$k++)

						{$msgSeller .= '<li>*'.addslashes($tabFields['value'][$i][$k]).'</li>';}

					$msgSeller .= '</ul>';

				}

				else

					{if($tabFields['fields_type'][$i]!='separator'&&$tabFields['fields_type'][$i]!='fileup'&&$tabFields['fields_type'][$i]!='submitbtn'&&$tabFields['fields_type'][$i]!='resetbtn'&&$tabFields['fields_type'][$i]!='imagebtn'&&$tabFields['fields_type'][$i]!='button')

						$msgSeller .= '<b>'.$tabFields['label'][$i].'</b> : '.addslashes($tabFields['value'][$i]).'<br>';}

			}

		}

  	

	Db::getInstance()->autoExecute( _DB_PREFIX_.'contactform_data',array(

		'ip'=>$ip,

		'date'=>date('m/d/Y').'-'. date('H:i'),

		'toemail'=>$mailSeller,

		'foremail'=>(empty($mailCustomer)?'':$mailCustomer),

		'info'=>$msgSeller,

		'statut_mail'=>$statut,

		'comment'=>''

		),'INSERT' );

	

	//return $msgSeller.$mailCustomer;

  

  }



public static function get_ip(){ 



if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])){ 

	$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];} 

elseif(isset($_SERVER['HTTP_CLIENT_IP'])){ 

	$ip = $_SERVER['HTTP_CLIENT_IP'];} 

else{ $ip = $_SERVER['REMOTE_ADDR'];} 

return $ip;}





 public  static function _purgeBadcharInfo($chaine){

 	$chaine = preg_replace("/[^a-zA-Z0-9\s,'.@_-������������������]/i", "", $chaine);

	return addslashes($chaine);

 }



public static function cleanTmp(){

	$dir = opendir(_PS_MODULE_DIR_.'contactform/upload/');

	

	while($file = readdir($dir)) {

	 		$ttf=explode('.',$file);

	 		if($file!="."&&$file!=".."&&$ttf[1]!="db"){

				unlink(_PS_MODULE_DIR_.'contactform/upload/'.$file);

			}

		}



}





}//End classes



?>