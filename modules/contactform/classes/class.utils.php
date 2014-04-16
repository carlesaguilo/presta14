<?php



class CFutils {

	





	  //EXPORT MYSQL ---------------------------------------------------------------

  public function _exportForm($mypath){

  	global $cookie;

	global $drop; 

	$tables=array();

	$output2='';

  	$mytoken=Tools::getValue('token');

	$task=Tools::getValue('task');

	$defaultLanguage = intval(Configuration::get('PS_LANG_DEFAULT'));

	$languages = Language::getLanguages();

	//$eofl='\n';

	$eofl="\n";

	if(CFtools::getIsocode($cookie->id_lang)=="fr")

		$dateformat='d/m/Y';

	else

		$dateformat='m/d/Y';

	

	//Taables used by COntactform

	//array_push($tables,_DB_PREFIX_.'contactform_cfg');

		array_push($tables,_DB_PREFIX_.'contactform');

		array_push($tables,_DB_PREFIX_.'contactform_item');

		array_push($tables,_DB_PREFIX_.'contactform_lang');

		array_push($tables,_DB_PREFIX_.'contactform_item_lang');

	

	//Navigation bar

	$output ='<form method="POST" action="'.$_SERVER['REQUEST_URI'].'">';

	

	$output .=CFtoolbar::toolbar('exportform',$mypath);

	

	

	//Affichage des résultats

	$output .='<legend style="margin-bottom:10px"><sup>'.CFtools::l('Please don not remove the ContaCtfotm tag : -- [CF_tag]').'</sup></legend><br><br>';

	$output .='<p>'.CFtools::l('Note').':<font color="red">'.CFtools::l('To restore a database from a backup contactform, it is advisable to use the specific contactform restore interface  via "Restore your form" menu. If you want to use <b>phpMyAdmin</b>, you should firstly clear the bases of existing contactform except "contactform_cfg" table, then after you can proceed with the restoration.').'</font></p><br>';

	$output .='<textarea  style="width: 100%; height: 456px;" name="sqldump" cols="50" rows="30" wrap="OFF">';

	$output2 .=$eofl.$eofl;

	$output2 .=$eofl.$eofl;

	$output2 .='SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";';

	$output2 .=$eofl.$eofl;

	$output2 .=$eofl.$eofl;

	$output2 .='-- [CF_tag]';

	$output2 .=$eofl.$eofl;

	

	

	for($i=0; $i<count($tables);$i++){

	

	

	$output2 .=$eofl.$eofl;

	

	//Key

	$tableskey=Db::getInstance()->ExecuteS('SHOW KEYS FROM '.$tables[$i]);

	//Fields definition

	$fieldsDef=Db::getInstance()->ExecuteS('SHOW FIELDS FROM '.$tables[$i]);

	

	$output2 .='CREATE TABLE IF NOT EXISTS `'.$tables[$i].'` ('.$eofl;

	$compteur=0;

		foreach($fieldsDef as $field){

				

			if($field['Null'] != 'YES') 

            	$null = ' NOT NULL '; 

			else{

				if($field["Default"]="NULL") 

            		$null = ' default NULL'; 

				else

					$null=' default '. $field['Default']; 

			}

			

			 if($field['Extra'] !='') 

            	$extra = $field['Extra'];

			 else 

			 	$extra = '';	

				

			if($compteur<count($fieldsDef)-1)

				$output2 .='`'.$field['Field'].'`  '.$field['Type'].'  '.$null.'  '.$extra.','.$eofl;

			else{

				if(!empty($tableskey[0]['Column_name']))

					$output2 .='`'.$field['Field'].'`  '.$field['Type'].'  '.$null.'  '.$extra.','.$eofl;

				else

					$output2 .='`'.$field['Field'].'`  '.$field['Type'].'  '.$null.'  '.$extra.$eofl;

				}

			

			$compteur++;

		}

	if(!empty($tableskey[0]['Column_name']))	

  		$output2 .='PRIMARY KEY  (`'.$tableskey[0]['Column_name'].'`)'.$eofl;

	

	$output2 .=') ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;' ;

	$output2 .=$eofl;

	$output2 .='-- [CF_tag]';

	$output2 .=$eofl.$eofl;

	

	$output2 .=$eofl.$eofl;

	

	//INSERTION 

	$tablecontents=Db::getInstance()->ExecuteS('SELECT *  FROM '.$tables[$i]);

	if(count($tablecontents)>0){

	$compteur=0;

	$comma=',';

		$output2 .='INSERT INTO `'.$tables[$i].'` (';

		

		foreach($fieldsDef as $field){

			if($compteur==count($fieldsDef)-1)

				$comma=' ';

			$output2 .=' `'.$field['Field'].'` ' .$comma.' ';

			$compteur++;

		}

		$output2 .=') VALUES'.$eofl;

		//Lists ou values

		$pcomma=',';

		$pcompteur=0;

		$comma=',';

		$compteur=0;

			foreach ($tablecontents as $tablecontent){

				if($pcompteur==count($tablecontents)-1)

					$pcomma=';';

				

				$output2 .='(';

				foreach($fieldsDef as $field){

					if($compteur==count($fieldsDef)-1)

						$comma='';

						$apost = str_replace("'",'´',$tablecontent[$field['Field']]);

						$output2 .='\''.$apost.'\''.$comma;

						$compteur++;

				}

			$output2 .=')';

			$output2 .=$pcomma;

			$output2 .=$eofl;

		$comma=',';

		$compteur=0;

		$pcompteur++;

		}

	}//end of if

	//END OF INSERTION	

	$output2 .=$eofl;

	$output2 .='-- [CF_tag]';

	$output2 .=$eofl.$eofl;



	}

	

	$output .=$output2.'</textarea>';

	$output .='</form>';

	

	//Write backup file: Alternative

			//Change file permission

			chmod(dirname(__FILE__).'/../library/sql/contactform.sql.txt',0666);

			

			$fp2 = fopen (dirname(__FILE__).'/../library/sql/contactform.sql.txt', "w");

			fputs ($fp2, $output2);

			fclose ($fp2);

	

	

	return $output;

  }

  



 //IMPORT MYSQL 

 public static function _importForm($mypath){

  	

	global $cookie;

	$tables=array();

  	$mytoken=Tools::getValue('token');

	$task=Tools::getValue('task');

	$defaultLanguage = intval(Configuration::get('PS_LANG_DEFAULT'));

	$languages = Language::getLanguages();

	

	$output =	'<form method="POST" action="'.$_SERVER['REQUEST_URI'].'" enctype="multipart/form-data">';

	

	$output .=CFtoolbar::toolbar('restoreform',$mypath);

	

	$output .=	'<fieldset><legend><img src="'.$mypath.'img/listform.png" alt="" title="" />'.CFtools::l('Import data').'</legend>';

	$output .= '<input  onclick="return(confirm(\''.CFtools::l('Caution, this will clear your forms and replace them with those in your backup').'\'));"  type="file" name="txtimportsql" size="30">&nbsp;&nbsp;&nbsp;&nbsp;<input class="button" type="submit" name="subimportsql" value="'.CFtools::l('Import').'">';

	$output .= '</fieldset>';

	$output .= '</form>';



  	return $output;

 }  

  

public static function _saveAs($fileName,$outputname){

  

  switch(strrchr(basename($fileName), ".")) {

	case ".gz": $type = "application/x-gzip"; break;

	case ".tgz": $type = "application/x-gzip"; break;

	case ".zip": $type = "application/zip"; break;

	case ".pdf": $type = "application/pdf"; break;

	case ".png": $type = "image/png"; break;

	case ".gif": $type = "image/gif"; break;

	case ".jpg": $type = "image/jpeg"; break;

	case ".txt": $type = "text/plain"; break;

	case ".htm": $type = "text/html"; break;

	case ".html": $type = "text/html"; break;

	default: $type = "application/octet-stream"; break;

	}



	header('Content-disposition: attachment; filename='.$outputname); 

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

 

  

public static function _truncateAllTable(){



			@Db::getInstance()->ExecuteS('TRUNCATE TABLE `'._DB_PREFIX_.'contactform`');

			@Db::getInstance()->ExecuteS('TRUNCATE TABLE `'._DB_PREFIX_.'contactform_item`');

			@Db::getInstance()->ExecuteS('TRUNCATE TABLE `'._DB_PREFIX_.'contactform_lang`');

			@Db::getInstance()->ExecuteS('TRUNCATE TABLE `'._DB_PREFIX_.'contactform_item_lang`');

}







 public static function _settings($mypath){

 

 	global $cookie;

	$tables=array();

  	$mytoken=Tools::getValue('token');

	$task=Tools::getValue('task');

	$defaultLanguage = intval(Configuration::get('PS_LANG_DEFAULT'));

	$languages = Language::getLanguages();

	$url='index.php?tab=AdminModules&configure=contactform&token='.$mytoken;

	$output =CFtoolbar::toolbar('settings',$mypath);

	

	$output .=self::_useSlider($mypath);

	$output .= '<link rel="stylesheet" type="text/css" href="'.$mypath.'library/tabs/tabs.css" />';

	$output .='

<script>

$(document).ready(function() {



	//When page loads...

	$(".tab_content").hide(); //Hide all content

	$("ul.tabs li:first").addClass("active").show(); //Activate first tab

	$(".tab_content:first").show(); //Show first tab content



	//On Click Event

	$("ul.tabs li").click(function() {



		$("ul.tabs li").removeClass("active"); //Remove any "active" class

		$(this).addClass("active"); //Add "active" class to selected tab

		$(".tab_content").hide(); //Hide all tab content



		var activeTab = $(this).find("a").attr("href"); //Find the href attribute value to identify the active tab + content

		$(activeTab).fadeIn(); //Fade in the active ID content

		return false;

	});



});

</script>';



$output .= '<fieldset class="pspage"> <legend class="pspage"><img src="'.$mypath.'img/listform.png" alt="" title="" />'.CFtools::l('Settings').'</legend>';

$output .='

	<ul class="tabs">

    <li><a href="#tab1">'.CFtools::l('General settings').'</a></li>

    <li><a href="#tab2">'.CFtools::l('Form settings').'</a></li>

	<li><a href="#tab3">'.CFtools::l('Captcha settings').'</a></li>

</ul>';



$output .='<div class="tab_container">

<form name="objForm" method="post" action="'.$_SERVER['REQUEST_URI'].'" enctype="multipart/form-data">';

/* ========================================= TAB 1 ====================================== */

$output .='<div id="tab1" class="tab_content">';

$output .='<table>';



$output .='<tr>

			<td>'.CFtools::l('Character displayed after required fields').' :</td>

			<td><input type="text" size=6 name="cfgrequired" value="'.Configuration::get('CONTACTFORM_REQUIRED').'"></td>';

$output .='</tr>';

$output .='<tr>

			<td>'.CFtools::l('Accepted file format for upload').'  '.CFtools::info($mypath,'Separate format with ","').' :</td>

			<td><input type="text" size=60 name="cfgupload" value="'.Configuration::get('CONTACTFORM_UPFORMAT').'"></td>';

$output .='</tr>';



$output .='<tr>

			<td>'.CFtools::l('Activation bouton').' :</td>

			<td>';

			

$output .= '<table class="homepage1" border="1">';

	$output .= '<tr align="center">';

	if(Configuration::get('CONTACTFORM_ACTIVE')==0){

		$output .= '<td ><a href="'.$url.'&task=btnActivecf&mode=1"><img src="'.$mypath.'img/activate-grey.png">

		<br>'.CFtools::l('Enable ContactForm activation button').'</a></td>';

	}

	else{

		$output .= '<td ><a href="'.$url.'&task=btnActivecf&mode=0"><img src="'.$mypath.'img/activate.png">

		<br>'.CFtools::l('Disable Contactform activation button').'</a></td>';

	}

	

	if(Configuration::get('CONTACTFORM_DEACTIVE')==0){

		$output .= '<td><a href="'.$url.'&task=btnDeactivecf&mode=1"><img src="'.$mypath.'img/diasable-grey.png">

		<br>'.CFtools::l('Enable Restoring Prestashop form').'</a></td>';

	}

	else{

		$output .= '<td><a href="'.$url.'&task=btnDeactivecf&mode=0"><img src="'.$mypath.'img/diasable.png">

		<br>'.CFtools::l('Disable Restoring Prestashop form button').'</a></td>';

	}

	$output .= '</tr>';

	$output .= '</table>';			

			

$output .='</td>';

$output .='</tr>';





$output .='</table>';

$output .='</div>';

/* ========================================= END TAB 1 ====================================== */



/* ========================================= TAB 2 ====================================== */

$output .='<div id="tab2" class="tab_content">';

$output .='<table cellpadding="10" cellspacing="15">';



$output .='<tr>

			<td>'.CFtools::l('Form style').CFtools::info($mypath,'Place your mouse over the text to see the preview').' :</td>

			<td>

			<ul>

				<li><input '.(Configuration::get('CONTACTFORM_FORM')==0?'checked':'' ).' name="cfstyle" type="radio" value="0">'.self::_imgpreview($mypath,'Basic','Style Basic',$mypath.'img/sample/basic.jpg',0).'</li>

				<li><input '.(Configuration::get('CONTACTFORM_FORM')==1?'checked':'' ).' name="cfstyle" type="radio" value="1">'.self::_imgpreview($mypath,'Advanced','Style Basic',$mypath.'img/sample/highform.jpg',1).' ('.CFtools::l('Only for prestashop prestashop 1.4 ou higher').')</li>

				

			</ul>';	

				

/*$output .='	<input name="cfgstyle" type="radio" value="0" '.(Configuration::get('CONTACTFORM_STYLE')==0?'checked':'' ).' ">'.CFtools::l('Basic').'

				<input name="cfgstyle"  type="radio" value="1" '.(Configuration::get('CONTACTFORM_STYLE')==1?'checked':'' ).'">'.CFtools::l('Niceform')';

*/				

				

$output .='</td>';

$output .='</tr>';





$output .='<tr>

			<td>'.CFtools::l('Form width').' (px) :</td>

			<td><input type="text" size=6 name="cfgfwidth" value="'.Configuration::get('CONTACTFORM_WIDTH').'"></td>';

$output .='</tr>';



$output .='<tr>

			<td>'.CFtools::l('Displaying radio button').' :</td>

			<td><select name="cfgradio" >

				<option value="1" '.(Configuration::get('CONTACTFORM_CFGRADIO')==1?'selected':'' ).' >'.CFtools::l('Horizontal').'</option>

				<option value="0" '.(Configuration::get('CONTACTFORM_CFGRADIO')==0?'selected':'' ).'>'.CFtools::l('Vertical').'</option>

			</select>

			</td>';

$output .='</tr>';



$output .='<tr>

			<td>'.CFtools::l('Displaying checkbox').' :</td>

			<td><select  name="cfgckbox" >

				<option value="1" '.(Configuration::get('CONTACTFORM_CFGCKBOX')==1?'selected':'' ).' >'.CFtools::l('Horizontal').'</option>

				<option value="0" '.(Configuration::get('CONTACTFORM_CFGCKBOX')==0?'selected':'' ).'>'.CFtools::l('Vertical').'</option>

			</select>

			</td>';

$output .='</tr>';

$output .='</table>';

$output .='</div>';

/* ========================================= END TAB 2 ====================================== */

/* ========================================= TAB 3 ====================================== */

$output .='<div id="tab3" class="tab_content">';

$output .='<table cellpadding="10" cellspacing="15">';







$output .='<tr>

				<td>'.CFtools::l('Width').'</td><td><input size=6 type="text" name="captchawidth" value="'.Configuration::get('CONTACTFORM_CAPTCHAWIDTH').'"></td>

		  </tr>';

$output .='<tr>

		  		<td>'.CFtools::l('Height').'</td><td><input size=6 type="text" name="captchaheight" value="'.Configuration::get('CONTACTFORM_CAPTCHAHEIGHT').'"></td>

		  </tr>';

		$output .= self::_newsliderline2('Perturbation','captchanoise',Configuration::get('CONTACTFORM_CAPTCHANOISE'),'a1',100,0);  

		  

		  

$output .='<tr>

		  		<td>'.CFtools::l('Number of line').'</td><td><input size=6 type="text" name="captchaline" value="'.Configuration::get('CONTACTFORM_CAPTCHALINE').'"></td>

		  </tr>';

$output .='<tr>

		  		<td>'.CFtools::l('Angle').'</td><td><input size=6 type="text" name="captchaangle" value="'.Configuration::get('CONTACTFORM_CAPTCHAANGLE').'"></td>

		  </tr>';



		  $output .= self::_newsliderline2('Opacity','captchaopacity',Configuration::get('CONTACTFORM_CAPTCHAOPACITY'),'a2',100,0);  

		  

		  

$output .='<tr>

		  		<td>'.CFtools::l('Text copyright').'</td><td><input type="text" name="captchacopy" value="'.Configuration::get('CONTACTFORM_CAPTCHACOPY').'">'.CFtools::info($mypath,'Leave blank if you do not want to display').'</td>

		  </tr>';

		  

$output .='<tr>

		  		<td>'.CFtools::l('Use word list').'</td>

				<td><input '.(Configuration::get('CONTACTFORM_CAPTCHAWORD')==1?'checked':'').' type="radio" name="captchaword" value="1">'.CFtools::l('Yes').'

					<input '.(Configuration::get('CONTACTFORM_CAPTCHAWORD')==0?'checked':'').' type="radio" name="captchaword" value="0">'.CFtools::l('No').'

				</td>

		  </tr>';

		$output .='<tr>';

		$ds =self::DS();

	$output .='<td colspan=2 scope="rows">'.self::iswriteimgDir(dirname(__FILE__).$ds.'..'.$ds.'library'.$ds.'recaptcha'.$ds.'backgrounds',$mypath).'</td>';

$output .='</tr>';  

		  

		  

	$output .= self::_showPreview('Background',dirname(__FILE__).'/../library/recaptcha/backgrounds',$mypath,'library/recaptcha/backgrounds/','captchabg','upcaptchabg',Configuration::get('CONTACTFORM_CAPTCHABG'),$width='100',$height='',$upload=1,'a1');

	

	$output .= self::_showfont('Font',dirname(__FILE__).'/../library/recaptcha/fonts',$mypath,'library/recaptcha/fonts/','captchfont','upcaptchafont',Configuration::get('CONTACTFORM_CAPTCHAFONT'),$upload=1,'a2');	

		  





$output .='</table>';

$output .='</div>';







$output .='<div align="center" style="margin:10px">

	<input class="button" type="submit" name="submitsettings" value="'.CFtools::l('    Save    ').'" >

	</div>';



$output .='</form>';

$output .='</div>';

$output .='</fieldset>';	

	

	return $output;

}





public static function _imgpreview($mypath,$linktxt,$titletxt,$img,$theme){

	$mytoken=Tools::getValue('token');

	$output = '<link rel="stylesheet" type="text/css" href="'.$mypath.'library/imgpreview/imgpreview.css" />';

	$output.='<script type="text/javascript" src="'.$mypath.'library/imgpreview/imgpreview.js"></script>';

	$output.='<a '.(Configuration::get('CONTACTFORM_FORM')==intval($theme)?'style="text-decoration:underline padding:5px; border: 1px solid green; background: lightgreen"':'').' href="#" rel="'.$img.'" class="screenshot" title="'.$titletxt.'">'.CFtools::l($linktxt).'</a>';

	$output.=(Configuration::get('CONTACTFORM_FORM')==intval($theme)?'<img style="padding-left:10px;" width=16px src="'.$mypath.'img/ok2.png">':'');

	return $output;

	

	}





public static function _activateForm($mypath){

	global $cookie;

	$tables=array();

  	$mytoken=Tools::getValue('token');

	$task=Tools::getValue('task');

	$defaultLanguage = intval(Configuration::get('PS_LANG_DEFAULT'));

	$languages = Language::getLanguages();

	

			$url='index.php?tab=AdminModules&configure=contactform&token='.$mytoken;

		$output=CFtoolbar::toolbar('classic',$mypath);

		

		//Copy thankyou file

		$t1=_PS_MODULE_DIR_.'contactform/bkp/contactform/thankyou.php';

		$t2 = _PS_ROOT_DIR_.'/thankyou.php';

		if(file_exists($t1))

			copy($t1, $t2);

		//Make backup

		$original=_PS_ROOT_DIR_.'/contact-form.php';

		$backup = _PS_MODULE_DIR_.'contactform/bkp/original/contact-form.php';

		if(file_exists($original))

			copy($original, $backup);

		//Rename  first

		$old= _PS_ROOT_DIR_.'/contact-form.php';

		$oldto=_PS_ROOT_DIR_.'/contact-form.old.php';

		if(file_exists($old)&&!file_exists($oldto)){

			rename($old,$oldto);

		}

		

		$file= _PS_MODULE_DIR_.'contactform/bkp/contactform/contact-form.php';

		$destination=_PS_ROOT_DIR_.'/contact-form.php';

		

		

		if (!copy($file, $destination)) {

 			$output .= CFtools::_errFormat(CFtools::l('The copy of the file failed. Please try again. If the problem persists, please replace manually (or via FTP) the file  contact-form.php in the root directory by the  file in the directory <b>modules/contactform/bkp/site/contact-form.php</b>'),0,false);

		}

		else{

			Configuration::updateValue('CONTACTFORM_ACTIVE', 0);

			$output .= self::_validRes(CFtools::l('The copy of the file <b>contact-form.php</b> was finished. If you still encounter problems after this process, please manually copy the file at <b>modules /contactform/bkp/site/contact-form.php</b> to the directory root of the site to replace the other.'));

			}

		return $output;



	



}



public static function _disableForm($mytoken,$link){

		global $cookie;

		$mytoken=Tools::getValue('token');

		$defaultLanguage = intval(Configuration::get('PS_LANG_DEFAULT'));

		$languages = Language::getLanguages();

		$url='index.php?tab=AdminModules&configure=contactform&token='.$mytoken;

		$output='';

		$file= _PS_MODULE_DIR_.'contactform/bkp/original/contact-form.php';

		$destination=_PS_ROOT_DIR_.'/contact-form.php';

		

		if (!copy($file, $destination)) {

 			$output .= CFtools::_errFormat(CFtools::l('The copy of the file failed. Please try again. If the problem persists, rename the file <b>modules/contactform/bkp/site/contact-form-orig.php</b> to <b>contact-form.php</b> and  put it in the directory root of prestashop site.'));

		}

		else{

			Configuration::updateValue('CONTACTFORM_DEACTIVE', 0);

			$output .= self::_validRes(CFtools::l('The copy of the file <b>contact-form.php</b> original was finished. If you still encounter problems after this process, please rename to contact-form.php and manually copy the file at <b>modules /contactform/bkp/site/contact-form-orig.php</b> to the directory root of the site to replace the other.'));

			}

		return $output;

	} 





public static function _btnActivecf($mode){

		switch($mode){

			case 1:

			Configuration::updateValue('CONTACTFORM_ACTIVE', 1);

			break;

			case 0:

			Configuration::updateValue('CONTACTFORM_ACTIVE', 0);

			break;

		}

	}

	public static function _btnDeactivecf($mode){

		switch($mode){

			case 1:

			Configuration::updateValue('CONTACTFORM_DEACTIVE', 1);

			break;

			case 0:

			Configuration::updateValue('CONTACTFORM_DEACTIVE', 0);

			break;

		}

	}







public static function _seedata($link,$asc,$orderby,$task,$pagelimit=10,$start=0){



global $cookie;

		$lastpage =0;

		$mytoken=Tools::getValue('token');

		$dataLists = Db::getInstance()->ExecuteS('SELECT * FROM '._DB_PREFIX_.'contactform_data ORDER BY `'.$orderby.'` '.$asc.' LIMIT '.$start.','.$pagelimit.'  ');

		$imgy='<img width="12" height="12" alt="" src="'.$link.'img/ok.png">';

		$imgn='<img width="12" height="12" alt="" src="'.$link.'img/del.png">';

		$output='';

		$url='index.php?tab=AdminModules&configure=contactform&task=seedata&token='.$mytoken;

		$url2='index.php?tab=AdminModules&configure=contactform&task=seedatadetails&token='.$mytoken;

		$url3='index.php?tab=AdminModules&configure=contactform&task=deldata&token='.$mytoken;

		

		

		if($asc=='ASC')

			$asc='DESC';

		elseif($asc=='DESC')

			$asc='ASC';

		$output=CFtoolbar::toolbar('classic',$link);

		

		?>

	<link rel="stylesheet" href="<?php echo $link ?>library/popup/general.css" type="text/css" media="screen">

	<script src="<?php echo $link ?>library/popup/jquery-1.js" type="text/javascript"></script>

	<script>

	var popupStatus = 0;



//loading popup with jQuery magic!

function loadPopup(){

	//loads popup only if it is disabled

	if(popupStatus==0){

		$("#backgroundPopup").css({

			"opacity": "0.7"

		});

		$("#backgroundPopup").fadeIn("slow");

		$("#popupContact").fadeIn("slow");

		popupStatus = 1;

	}

}



//disabling popup with jQuery magic!

function disablePopup(){

	//disables popup only if it is enabled

	if(popupStatus==1){

		$("#backgroundPopup").fadeOut("slow");

		$("#popupContact").fadeOut("slow");

		popupStatus = 0;

	}

}



//centering popup

function centerPopup(){

	//request data for centering

	var windowWidth = document.documentElement.clientWidth;

	var windowHeight = document.documentElement.clientHeight;

	var popupHeight = $("#popupContact").height();

	var popupWidth = $("#popupContact").width();

	//centering

	$("#popupContact").css({

		"position": "absolute",

		"top": windowHeight/2-popupHeight/2,

		"left": windowWidth/2-popupWidth/2

	});

	//only need force for IE6

	

	$("#backgroundPopup").css({

		"height": windowHeight

	});

	

}





//CONTROLLING EVENTS IN jQuery

$(document).ready(function(){

	$("#popupContactClose").click(function(){

		disablePopup();

	});

	//Click out event!

	$("#backgroundPopup").click(function(){

		disablePopup();

	});

	//Press Escape event!

	$(document).keypress(function(e){

		if(e.keyCode==27 && popupStatus==1){

			disablePopup();

		}

	});



});

	</script>

    <script>

	function addscript(id,foremail,toemail){

		var mailadress = document.getElementById('mailadress');	

		var mailsender = document.getElementById('mailsender');	

		mailadress.value = foremail;

		mailsender.value = toemail;

		

		$(document).ready(function(){

				$("#button"+id).click(function(){

				

				

				//centering with css

				centerPopup();

				//load popup

				loadPopup();

			});

		});

		



	

	}

	</script>

	<div style="position: absolute; top: 71.5px; left: 476px; display: none; background:#EEF2F7" id="popupContact">

		<a id="popupContactClose"><img src="<?php echo $link ?>library/popup/close.png" alt="X" /></a>

		<h1><?php echo CFtools::l('INSTANT MAIL RESPONSE'); ?></h1>

		<p id="contactArea">

			<form  method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">

            <table cellpadding="5" cellspacing="5">

                        <tr>

            	<td><?php echo CFtools::l('Recipient'); ?></td><td><input size=40 id="mailadress" type="text" name="mailadress" value="" /></td>

            </tr>

             <tr>

            	<td><?php echo CFtools::l('Subject'); ?>:</td><td><input size=40 type="text" name="mailsubject" value="" /></td>

            </tr>

            <tr valign="top">

            	<td valign="top">Message:</td><td><textarea cols="45" rows="10" name="mailmessage"/></textarea></td>

            </tr>

            <tr>

            	<td><?php echo CFtools::l('Sender'); ?></td><td><input size=40 id="mailsender" type="text" name="mailsender" value="" /></td>

            </tr>

            <tr>

            	<td></td><td><input type="submit" name="mailsubmit" value="<?php echo CFtools::l('Envoyer'); ?>" /></td>

            </tr>

            </table>

            </form>

		</p>

	</div>

	<div style="height: 527px; opacity: 0.7; display: none;" id="backgroundPopup"></div>



<?php

$output .='<fieldset><legend><img src="'.$link.'img/listform.png" alt="" title="" />'.CFtools::l('Data list').'</legend>';

		$output.='<div id="itemList" class="itemList">';

		

		//All data

		$alldata = Db::getInstance()->ExecuteS('SELECT * FROM '._DB_PREFIX_.'contactform_data ORDER BY `'.$orderby.'` '.$asc);

		$maxstart=count($alldata)-$pagelimit;

		$output .=CFtools::l('Go to page').' : '.'<a href="'.$url.'&pagelimit='.$pagelimit.'&start=0"><< - </a>';

		for($i=0;$i<count($alldata)/$pagelimit;$i++){

			

			$output .='<a href="'.$url.'&pagelimit='.$pagelimit.'&start='.$pagelimit*$i.'">'.($i+1).' - '.'</a>';

			$lastpage=$pagelimit*$i;

		}

		

		$output .='<script type="text/javascript">

		checked=false;

		function checkedAll (frm1) {

		var aa= document.getElementById("frm1");

	 	if (checked == false)

          	{

           	checked = true

          	}

        else

          {

          checked = false

          }

		for (var i =0; i < aa.elements.length; i++) 

		{

	 		aa.elements[i].checked = checked;

		}

      }

	</script>';

		

		$output .='<a href="'.$url.'&pagelimit='.$pagelimit.'&start='.$lastpage.'">>></a><br><br>';

		

		$output.='<form id ="frm1" name="frm1" method="post" action="'.$_SERVER['REQUEST_URI'].'" >';

		$output.='<table width="100%" class="table" cellspacing="0" cellpadding="0">';

		$output.='<thead><tr class="nodrag nodrop" >

						<th><input type="checkbox" name="checkall" onclick="checkedAll(frm1);"></th>

						<th><a href="'.$url.'&asc='.$asc.'&orderby=data_id">ID</a></th>

						<th><a href="'.$url.'&asc='.$asc.'&orderby=ip">'.CFtools::l('Ip address').'</a></th>

						<th><a href="'.$url.'&asc='.$asc.'&orderby=date">'.CFtools::l('Date').'</a></th>

						<th><a href="'.$url.'&asc='.$asc.'&orderby=toemail">'.CFtools::l('Mail to').'</a></th>

						<th><a href="'.$url.'&asc='.$asc.'&orderby=foremail">'.CFtools::l('Mail From').'</a></th>

						<th><a href="'.$url.'&asc='.$asc.'&orderby=info">'.CFtools::l('Message sent').'</a></th>

						<th><a href="'.$url.'&asc='.$asc.'&orderby=statut_mail">'.CFtools::l('Mail Statut').'</a></th>

						<th width="15%">'.CFtools::l('Actions').'</th></tr></thead>';

		

		

		foreach($dataLists as $dataList){

			$output.='<tr valign="top">';

						$output.='<td align="left"><input type="checkbox" name="actlink['.$dataList['data_id'].']" value="1"></td>';

						$output.='<td><a href="'.$url2.'&data_id='.$dataList['data_id'].'">'.$dataList['data_id'].'</a></td>';

						$output.='<td><a href="'.$url2.'&data_id='.$dataList['data_id'].'">'.$dataList['ip'].'</a></td>';

						$tabdate=explode('/',$dataList['date']);

						if(CFtools::getIsocode($cookie->id_lang)=='fr')

							$mdate=$tabdate[1].'/'.$tabdate[0].'/'.$tabdate[2];

						else

							$mdate=$dataList['date'];

						$output.='<td><a href="'.$url2.'&data_id='.$dataList['data_id'].'">'.$mdate.'</a></td>';

						$output.='<td><a href="'.$url2.'&data_id='.$dataList['data_id'].'">'.$dataList['toemail'].'</a></td>';

						$output.='<td><a href="'.$url2.'&data_id='.$dataList['data_id'].'">'.$dataList['foremail'].'</a></td>';

						$output.='<td><a href="'.$url2.'&data_id='.$dataList['data_id'].'">'.CFtools::_substrStr(50,$dataList['info']).'</a></td>';

						if($dataList['statut_mail']=='mail'){

							$output.='<td><a href="'.$url2.'&data_id='.$dataList['data_id'].'">'.$imgy.CFtools::l('Mail sent').'</a></td>';

						}

						else{

							$output.='<td><a href="'.$url2.'&data_id='.$dataList['data_id'].'">'.$imgn.CFtools::l('Mail not sent').'</a></td>';

						}

						$output.='<td>

						<div style=" float:left; border:1px solid #ccc; padding:2px; margin-right:2px; background:lightgreen" id="button'.$dataList['data_id'].'"><a onclick="addscript('.$dataList['data_id'].',\''.$dataList['foremail'].'\',\''.$dataList['toemail'].'\')" >Repondre</a></div>

							<a  title="'.CFtools::l('Preview').'" href="'.$url2.'&data_id='.$dataList['data_id'].'">

							<img width=16 alt="'.CFtools::l('Preview').'" src="'.$link.'img/preview.png">

							</a>

						<a title="'.CFtools::l('Delete').'" href="'.$url3.'&data_id='.$dataList['data_id'].'" onclick="return(confirm(\''.CFtools::l('Do you really want to delete this message?').'\'));">

						<img width=16 alt="'.CFtools::l('Delete').'" src="'.$link.'img/delete.png">

						</a>

						

						

						

						</td>';

			$output.='</tr>';

		}

		$output.='</table>';

		$output.='<input style="margin:10px;" class="button" type="submit" name="deleteselectdata" value="'.CFtools::l('Delete selected').'" onclick="return(confirm(\''.CFtools::l('Do you really want  to deleted data?').'\'));">';

		$output.='</form>';

		$output.='</div>';

		$output.='</fieldset>';



	return $output;



}





public static function _addsample($mypath){



	global $cookie;

	$mytoken=Tools::getValue('token');

	$url='index.php?tab=AdminModules&configure=contactform&token='.$mytoken;

	

	$output=CFtoolbar::toolbar('classic',$mypath);

	

	$output .='<fieldset><legend><img src="'.$mypath.'logo.gif" alt="" title="" />'.CFtools::l('Add sample').'</legend>';

	$output .='<center><table class="homepage" border="1">';

	$output .='<tr>';

	$output .='<td>';

	$output .='<table class="homepage1" border="1" >';

		$output .='<tr>';

			$output .='<td><a href="'.$url.'&task=importsample&model=1">

							<img src="'.$mypath.'img/sample/modele1.jpg"><br>'.CFtools::l('BASIC FORM').'</a></td>';

			$output .='<td><a href="'.$url.'&task=importsample&model=2"><img src="'.$mypath.'img/sample/modele2.jpg"><br>'.CFtools::l('INSCRIPTION FORM').'</a></td>';

		$output .='</tr>';

	$output .='</table>';

	$output .='</td>';

	$output .='</tr>';	

	$output .='</table></center>';	

	

	$output .='</fieldset>';

	return $output;



}



public static function _importSample($model,$mypath){



global $cookie;

$mytoken=Tools::getValue('token');	



	$defaultlayout ='<html>

<head>

	<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">

	<title>{message_from} {shop_name}</title>

</head>

<body>

	<table style=\"font-family:Verdana,sans-serif; font-size:11px; color:#374953; width: 550px;\">

		<tr>

			<td align=\"left\">

				<a href=\"{shop_url}\" title=\"{shop_name}\"><img alt=\"{shop_name}\" src=\"{shop_logo}\" style=\"border:none;\" ></a>

			</td>

		</tr>

		<tr><td>&nbsp;</td></tr>

		<tr>

			<td align=\"left\" style=\"background-color:#DB3484; color:#FFF; font-size: 12px; font-weight:bold; padding: 0.5em 1em;\">{contactform_in}  {form_name}</td>

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

			<td align=\"center\" style=\"font-size:10px; border-top: 1px solid #D9DADE;\">

				<a href=\"{shop_url}\" style=\"color:#DB3484; font-weight:bold; text-decoration:none;\">{shop_name}</a> powered with <a href=\"http://www.aretmic.com/\" style=\"text-decoration:none; color:#374953;\">Contactform</a>

			</td>

		</tr>

	</table>

</body>

</html>';





$customerlayout ='

<html>

<head>

	<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">

	<title>{notification} {shop_name}</title>

</head>

<body>

	<table style=\"font-family:Verdana,sans-serif; font-size:11px; color:#374953; width: 550px;\">

		<tr>

			<td align=\"left\">

				<a href=\"{shop_url}\" title=\"{shop_name}\"><img alt=\"{shop_name}\" src=\"{shop_logo}\" style=\"border:none;\" ></a>

			</td>

		</tr>

		<tr><td>&nbsp;</td></tr>

		<tr>

			<td align=\"left\" style=\"background-color:#DB3484; color:#FFF; font-size: 12px; font-weight:bold; padding: 0.5em 1em;\">{notification} {shop_name}</td>

		</tr>

		<tr><td>&nbsp;</td></tr>

		<tr>

			<td>

				{message}

			</td>

		</tr>

		<tr><td>&nbsp;</td></tr>

		<tr>

			<td align=\"center\" style=\"font-size:10px; border-top: 1px solid #D9DADE;\">

				<a href=\"{shop_url}\" style=\"color:#DB3484; font-weight:bold; text-decoration:none;\">{shop_name}</a> powered with <a href=\"http://www.aretmic.com/\" style=\"text-decoration:none; color:#374953;\">Contactform</a>

			</td>

		</tr>

	</table>

</body>

</html>';

	

switch($model){



case 1 : 

@@Db::getInstance()->ExecuteS("INSERT INTO `"._DB_PREFIX_."contactform` (`fid`, `formname`, `email`, `mailtype`, `layout`, `clayout`) VALUES

('', 'BasicForm', 'admin@admin.com', '0',\"".$defaultlayout."\",\"".$customerlayout."\");");



$Cte_id=mysql_insert_id();



@@Db::getInstance()->ExecuteS("INSERT INTO `"._DB_PREFIX_."contactform_lang` (`id_lang`, `fid`, `alias`, `formtitle`, `thankyou`, `msgbeforeForm`, `msgafterForm`, `toname`, `subject`, `automailresponse`, `returnurl`) VALUES

(".CFtools::getIdlangFromiso("en").", '".$Cte_id."', 'Contact-Form', 'Contact Form', '<p>Thank you for your request. We will respond shortly to the email you just send us. Sincerely.<br /><br />Team.</p>', '', '', 'Administrator', 'Contact Prestashop', '<p>Thank you for your request. We will respond shortly to the email you just send us. Sincerely.<br /><br />Team.</p>', 'http://www.aretmic.com'),

(".CFtools::getIdlangFromiso("fr").", '".$Cte_id."', 'Formulaire-de-contact', 'Formulaire de contact', '<p>Merci pour votre demande. Nous répondrons très prochainement au mail que vous venez de nous faire parvenir. Bien cordialement.<br /><br />L\'équipe.</p>', '', '', 'Administrateur', 'Contact Prestashop', '<p>Merci pour votre demande. Nous répondrons très prochainement au mail que vous venez de nous faire parvenir. Bien cordialement.<br /><br />L\'équipe.</p>', 'http://www.aretmic.com'),

(".CFtools::getIdlangFromiso("es").", '".$Cte_id."', 'Formulario-de-contacto', 'Formulario de contacto', '<p>Gracias por su solicitud. Nosotros responderemos a la brevedad al correo electrónico que nos acaba de enviar. Atentamente.<br /><br />Equipo.</p>', '', '', 'Administrador', 'Póngase en contacto con PrestaShop', '<p>Gracias por su solicitud. Nosotros responderemos a la brevedad al correo electrónico que nos acaba de enviar. Atentamente.<br /><br />Equipo.</p>', 'http://www.aretmic.com'),

(".CFtools::getIdlangFromiso("de").", '".$Cte_id."', 'Kontaktformular', 'Kontaktformular', '<p>Vielen Dank für Ihre Anfrage. Wir werden in Kürze Antwort auf die E-Mail senden Sie uns einfach. Mit freundlichen Grüßen.<br /><br />Team.</p>', '', '', 'Administrator', 'Kontakt PrestaShop', '<p>Vielen Dank für Ihre Anfrage. Wir werden in Kürze Antwort auf die E-Mail senden Sie uns einfach. Mit freundlichen Grüßen.<br /><br />Team.</p>', 'http://www.aretmic.com'),

(".CFtools::getIdlangFromiso("it").", '".$Cte_id."', 'Modulo-di-contatto', 'Modulo di contatto', '<p>Grazie per la vostra richiesta. Ci sarà presto una risposta alle e-mail è sufficiente inviare. Cordiali saluti.<br /><br />Team.</p>', '', '', 'Administrator', 'Contatta PrestaShop', '<p>Grazie per la vostra richiesta. Ci sarà presto una risposta alle e-mail è sufficiente inviare. Cordiali saluti.<br /><br />Team.</p>', 'http://www.aretmic.com');");



//========================= ITEM ==========================



@@Db::getInstance()->ExecuteS("INSERT INTO `"._DB_PREFIX_."contactform_item` (`fdid`, `fid`, `fields_id`, `fields_name`, `confirmation`, `fields_valid`, `fields_type`, `fields_style`, `err_style`, `fields_suppl`, `fields_require`, `order`, `published`) VALUES('', ".$Cte_id.", 'title', 'title', 0, 'none', 'select', '', '', '', 1, 1, 1);");

$newcte_id=mysql_insert_id();



@@Db::getInstance()->ExecuteS("INSERT INTO `"._DB_PREFIX_."contactform_item_lang` (`fdid`, `id_lang`, `fields_title`, `fields_desc`, `confirmation_txt`, `fields_default`, `error_txt`, `error_txt2`) VALUES

(".$newcte_id.", ".CFtools::getIdlangFromiso("en").", 'Title', '', '', ';Mrs;Ms;Mr', '', ''),

(".$newcte_id.", ".CFtools::getIdlangFromiso("fr").", 'Titre', '', '', ';Mme;Mlle;Mr', '', ''),

(".$newcte_id.", ".CFtools::getIdlangFromiso("es").", 'Título', '', '', ';Sra.; El Sr.', '', ''),

(".$newcte_id.", ".CFtools::getIdlangFromiso("de").", 'Titel', '', '', 'Frau;Fräulein;Herr', '', ''),

(".$newcte_id.", ".CFtools::getIdlangFromiso("it").", 'Titolo', '', '', 'Ms.; Miss; Sig.', '', '');");







@@Db::getInstance()->ExecuteS("INSERT INTO `"._DB_PREFIX_."contactform_item` (`fdid`, `fid`, `fields_id`, `fields_name`, `confirmation`, `fields_valid`, `fields_type`, `fields_style`, `err_style`, `fields_suppl`, `fields_require`, `order`, `published`) VALUES 																																								('' , ".$Cte_id.", 'name', 'name', 0, 'none', 'text', '', '', '', 1, 2, 1);");

$newcte_id=mysql_insert_id();	

@@Db::getInstance()->ExecuteS("INSERT INTO `"._DB_PREFIX_."contactform_item_lang` (`fdid`, `id_lang`, `fields_title`, `fields_desc`, `confirmation_txt`, `fields_default`, `error_txt`, `error_txt2`) VALUES

(".$newcte_id.", ".CFtools::getIdlangFromiso("en").", 'Your full name', '', '', 'Your full name ...', '', ''),

(".$newcte_id.", ".CFtools::getIdlangFromiso("fr").", 'Votre nom et prenom', '', '', 'Votre nom ...', '', ''),

(".$newcte_id.", ".CFtools::getIdlangFromiso("es").", 'Su nombre', '', '', 'Su nombre ..', '', ''),

(".$newcte_id.", ".CFtools::getIdlangFromiso("de").", 'Ihr vollständiger Name', '', '', 'Ihr Name ...', '', ''),

(".$newcte_id.", ".CFtools::getIdlangFromiso("it").", 'Il tuo nome completo', '', '', 'Il tuo nome completo', '', '');");																	   

																	   

																	   

																	   

																																																																																																						@@Db::getInstance()->ExecuteS("INSERT INTO `"._DB_PREFIX_."contactform_item` (`fdid`, `fid`, `fields_id`, `fields_name`, `confirmation`, `fields_valid`, `fields_type`, `fields_style`, `err_style`, `fields_suppl`, `fields_require`, `order`, `published`) VALUES 

('', ".$Cte_id.", 'myemail', 'myemail', 1, 'email', 'email', '', '', '', 1, 3, 1);");

$newcte_id=mysql_insert_id();	

@@Db::getInstance()->ExecuteS("INSERT INTO `"._DB_PREFIX_."contactform_item_lang` (`fdid`, `id_lang`, `fields_title`, `fields_desc`, `confirmation_txt`, `fields_default`, `error_txt`, `error_txt2`) VALUES

(".$newcte_id.", ".CFtools::getIdlangFromiso("en").", 'Your e-mail', '', 'Confirm your email', '', '', ''),

(".$newcte_id.", ".CFtools::getIdlangFromiso("fr").", 'Votre e-mail', '', 'Confirmer votre email', '', '', ''),

(".$newcte_id.", ".CFtools::getIdlangFromiso("es").", 'Tu e-mail', '', 'Confirme su correo electrónico', '', '', ''),

(".$newcte_id.", ".CFtools::getIdlangFromiso("de").", 'Ihre E-Mail', '', 'Bestätigen Sie Ihre E-Mail', '', '', ''),

(".$newcte_id.", ".CFtools::getIdlangFromiso("it").", 'Il tuo indirizzo e-mail', '', 'Conferma la tua email', '', '', '');");																	   





@@Db::getInstance()->ExecuteS("INSERT INTO `"._DB_PREFIX_."contactform_item` (`fdid`, `fid`, `fields_id`, `fields_name`, `confirmation`, `fields_valid`, `fields_type`, `fields_style`, `err_style`, `fields_suppl`, `fields_require`, `order`, `published`) VALUES 

('', ".$Cte_id.", 'subject', 'subject', 0, 'none', 'text', '', '', '', 1, 4, 1);");

$newcte_id=mysql_insert_id();

@@Db::getInstance()->ExecuteS("INSERT INTO `"._DB_PREFIX_."contactform_item_lang` (`fdid`, `id_lang`, `fields_title`, `fields_desc`, `confirmation_txt`, `fields_default`, `error_txt`, `error_txt2`) VALUES

(".$newcte_id.", ".CFtools::getIdlangFromiso("en").",  'Subject', '', '', '', '', ''),

(".$newcte_id.", ".CFtools::getIdlangFromiso("fr").", 'Sujet', '', '', '', '', ''),

(".$newcte_id.", ".CFtools::getIdlangFromiso("es").", 'Tema', '', '', '', '', ''),

(".$newcte_id.", ".CFtools::getIdlangFromiso("de").",  'Über', '', '', '', '', ''),

(".$newcte_id.", ".CFtools::getIdlangFromiso("it").", 'Soggetto', '', '', '', '', '');");	







@@Db::getInstance()->ExecuteS("INSERT INTO `"._DB_PREFIX_."contactform_item` (`fdid`, `fid`, `fields_id`, `fields_name`, `confirmation`, `fields_valid`, `fields_type`, `fields_style`, `err_style`, `fields_suppl`, `fields_require`, `order`, `published`) VALUES 

('', ".$Cte_id.", 'message', 'message', 0, 'none', 'textarea', '', '', '', 1, 5, 1);");

$newcte_id=mysql_insert_id();

@@Db::getInstance()->ExecuteS("INSERT INTO `"._DB_PREFIX_."contactform_item_lang` (`fdid`, `id_lang`, `fields_title`, `fields_desc`, `confirmation_txt`, `fields_default`, `error_txt`, `error_txt2`) VALUES

(".$newcte_id.", ".CFtools::getIdlangFromiso("en").",  'Message', '', '', '', '', ''),

(".$newcte_id.", ".CFtools::getIdlangFromiso("fr").", 'Message', '', '', '', '', ''),

(".$newcte_id.", ".CFtools::getIdlangFromiso("es").", 'Mensaje', '', '', '', '', ''),

(".$newcte_id.", ".CFtools::getIdlangFromiso("de").",  'Nachricht', '', '', '', '', ''),

(".$newcte_id.", ".CFtools::getIdlangFromiso("it").", 'Messaggio', '', '', '', '', '');");	







@@Db::getInstance()->ExecuteS("INSERT INTO `"._DB_PREFIX_."contactform_item` (`fdid`, `fid`, `fields_id`, `fields_name`, `confirmation`, `fields_valid`, `fields_type`, `fields_style`, `err_style`, `fields_suppl`, `fields_require`, `order`, `published`) VALUES 

('', ".$Cte_id.", 'captcha', 'captcha', 1, 'none', 'captcha', '', '', '', 0, 6, 1);");

$newcte_id=mysql_insert_id();

@@Db::getInstance()->ExecuteS("INSERT INTO `"._DB_PREFIX_."contactform_item_lang` (`fdid`, `id_lang`, `fields_title`, `fields_desc`, `confirmation_txt`, `fields_default`, `error_txt`, `error_txt2`) VALUES

(".$newcte_id.", ".CFtools::getIdlangFromiso("en").",  'Verification code', '', 'Retape code here', '', '', ''),

(".$newcte_id.", ".CFtools::getIdlangFromiso("fr").", 'Code de sécurity', '', 'Recopier le code ici', '', '', ''),

(".$newcte_id.", ".CFtools::getIdlangFromiso("es").", 'Código de verificación', '', 'Copia el código aquí', '', '', ''),

(".$newcte_id.", ".CFtools::getIdlangFromiso("de").",  'Bestätigungs-Code', '', 'Kopieren Sie den Code hier', '', '', ''),

(".$newcte_id.", ".CFtools::getIdlangFromiso("it").", 'Codice di verifica', '', 'Riscrivi il codice qui', '', '', '');");	







@@Db::getInstance()->ExecuteS("INSERT INTO `"._DB_PREFIX_."contactform_item` (`fdid`, `fid`, `fields_id`, `fields_name`, `confirmation`, `fields_valid`, `fields_type`, `fields_style`, `err_style`, `fields_suppl`, `fields_require`, `order`, `published`) VALUES 

('', ".$Cte_id.", 'submit', 'submit', 0, 'none', 'submitbtn', '', '', '', 0, 7, 1);");

$newcte_id=mysql_insert_id();

@@Db::getInstance()->ExecuteS("INSERT INTO `"._DB_PREFIX_."contactform_item_lang` (`fdid`, `id_lang`, `fields_title`, `fields_desc`, `confirmation_txt`, `fields_default`, `error_txt`, `error_txt2`) VALUES

(".$newcte_id.", ".CFtools::getIdlangFromiso("en").", '', '', '', 'Send', '', ''),

(".$newcte_id.", ".CFtools::getIdlangFromiso("fr").", '', '', '', 'Envoyer', '', ''),

(".$newcte_id.", ".CFtools::getIdlangFromiso("es").", '', '', '', 'Enviar', '', ''),

(".$newcte_id.", ".CFtools::getIdlangFromiso("de").", '', '', '', 'Senden', '', ''),

(".$newcte_id.", ".CFtools::getIdlangFromiso("it").", '', '', '', 'Invia', '', '');");	



	break;

//============================ MODEL 2 ===============================================	

case 2 :



@@Db::getInstance()->ExecuteS("INSERT INTO `"._DB_PREFIX_."contactform` (`fid`, `formname`, `email`, `mailtype`, `layout`, `clayout`) VALUES

('', 'InscriptionForm', 'admin@admin.com', '0',\"".$defaultlayout."\",\"".$customerlayout."\");");



/*@@Db::getInstance()->ExecuteS("INSERT INTO `"._DB_PREFIX_."contactform` (`fid`, `formname`, `email`, `mailtype`, `layout`, `clayout`) VALUES

('', 'BasicForm', 'admin@admin.com', '0', '<p>{message_from} {shop_name}</p>\r\n<table>\r\n<tbody>\r\n<tr>\r\n<td align=\"&quot;left&quot;\"><a title=\"&quot;{shop_name}&quot;\" href=\"&quot;{shop_url}&quot;\"><img src=\"&quot;{shop_logo}&quot;\" alt=\"&quot;{shop_name}&quot;\" /></a></td>\r\n</tr>\r\n<tr>\r\n<td></td>\r\n</tr>\r\n<tr>\r\n<td align=\"&quot;left&quot;\">{contactform_in}  {form_name}</td>\r\n</tr>\r\n<tr>\r\n<td></td>\r\n</tr>\r\n<tr>\r\n<td>{here_msg} :<br /> {message}</td>\r\n</tr>\r\n<tr>\r\n<td></td>\r\n</tr>\r\n<tr>\r\n<td align=\"&quot;center&quot;\"><a href=\"&quot;{shop_url}&quot;\">{shop_name}</a> powered with <a href=\"&quot;http://www.aretmic.com/&quot;\">Contactform</a></td>\r\n</tr>\r\n</tbody>\r\n</table>', '<p>{notification} {shop_name}</p>\r\n<table>\r\n<tbody>\r\n<tr>\r\n<td align=\"&quot;left&quot;\"><a title=\"&quot;{shop_name}&quot;\" href=\"&quot;{shop_url}&quot;\"><img src=\"&quot;{shop_logo}&quot;\" alt=\"&quot;{shop_name}&quot;\" /></a></td>\r\n</tr>\r\n<tr>\r\n<td></td>\r\n</tr>\r\n<tr>\r\n<td align=\"&quot;left&quot;\">Notification message from. {shop_name}</td>\r\n</tr>\r\n<tr>\r\n<td></td>\r\n</tr>\r\n<tr>\r\n<td>{message}</td>\r\n</tr>\r\n<tr>\r\n<td></td>\r\n</tr>\r\n<tr>\r\n<td align=\"&quot;center&quot;\"><a href=\"&quot;{shop_url}&quot;\">{shop_name}</a> powered with <a href=\"&quot;http://www.aretmic.com/&quot;\">Contactform</a></td>\r\n</tr>\r\n</tbody>\r\n</table>');");*/



$Cte_id=mysql_insert_id();



@@Db::getInstance()->ExecuteS("INSERT INTO `"._DB_PREFIX_."contactform_lang` (`id_lang`, `fid`, `alias`, `formtitle`, `thankyou`, `msgbeforeForm`, `msgafterForm`, `toname`, `subject`, `automailresponse`, `returnurl`) VALUES

(".CFtools::getIdlangFromiso("en").", '".$Cte_id."', 'Contact-Form', 'Contact Form', '<p>Thank you for your request. We will respond shortly to the email you just send us. Sincerely.<br /><br />Team.</p>', '', '', 'Administrator', 'Contact Prestashop', '<p>Thank you for your request. We will respond shortly to the email you just send us. Sincerely.<br /><br />Team.</p>', 'http://www.aretmic.com'),

(".CFtools::getIdlangFromiso("fr").", '".$Cte_id."', 'Formulaire-de-contact', 'Formulaire de contact', '<p>Merci pour votre demande. Nous répondrons très prochainement au mail que vous venez de nous faire parvenir. Bien cordialement.<br /><br />L\'équipe.</p>', '', '', 'Administrateur', 'Contact Prestashop', '<p>Merci pour votre demande. Nous répondrons très prochainement au mail que vous venez de nous faire parvenir. Bien cordialement.<br /><br />L\'équipe.</p>', 'http://www.aretmic.com'),

(".CFtools::getIdlangFromiso("es").", '".$Cte_id."', 'Formulario-de-contacto', 'Formulario de contacto', '<p>Gracias por su solicitud. Nosotros responderemos a la brevedad al correo electrónico que nos acaba de enviar. Atentamente.<br /><br />Equipo.</p>', '', '', 'Administrador', 'Póngase en contacto con PrestaShop', '<p>Gracias por su solicitud. Nosotros responderemos a la brevedad al correo electrónico que nos acaba de enviar. Atentamente.<br /><br />Equipo.</p>', 'http://www.aretmic.com'),

(".CFtools::getIdlangFromiso("de").", '".$Cte_id."', 'Kontaktformular', 'Kontaktformular', '<p>Vielen Dank für Ihre Anfrage. Wir werden in Kürze Antwort auf die E-Mail senden Sie uns einfach. Mit freundlichen Grüßen.<br /><br />Team.</p>', '', '', 'Administrator', 'Kontakt PrestaShop', '<p>Vielen Dank für Ihre Anfrage. Wir werden in Kürze Antwort auf die E-Mail senden Sie uns einfach. Mit freundlichen Grüßen.<br /><br />Team.</p>', 'http://www.aretmic.com'),

(".CFtools::getIdlangFromiso("it").", '".$Cte_id."', 'Modulo-di-contatto', 'Modulo di contatto', '<p>Grazie per la vostra richiesta. Ci sarà presto una risposta alle e-mail è sufficiente inviare. Cordiali saluti.<br /><br />Team.</p>', '', '', 'Administrator', 'Contatta PrestaShop', '<p>Grazie per la vostra richiesta. Ci sarà presto una risposta alle e-mail è sufficiente inviare. Cordiali saluti.<br /><br />Team.</p>', 'http://www.aretmic.com');");





																  



@@Db::getInstance()->ExecuteS("INSERT INTO `"._DB_PREFIX_."contactform_item` (`fdid`, `fid`, `fields_id`, `fields_name`, `confirmation`, `fields_valid`, `fields_type`, `fields_style`, `err_style`, `fields_suppl`, `fields_require`, `order`, `published`) VALUES

('', '".$Cte_id."', 'sep1', 'sep1', 0, 'none', 'separator', '', '', '', 0, 1, 1);");

$newcte_id=mysql_insert_id();



@@Db::getInstance()->ExecuteS("INSERT INTO `"._DB_PREFIX_."contactform_item_lang` (`fdid`, `id_lang`, `fields_title`, `fields_desc`, `confirmation_txt`, `fields_default`, `error_txt`, `error_txt2`) VALUES

(".$newcte_id.", ".CFtools::getIdlangFromiso("en").", 'Identification', '', '', 'Identification', '', ''),

(".$newcte_id.", ".CFtools::getIdlangFromiso("fr").", 'Identification', '', '', 'Identification', '', ''),

(".$newcte_id.", ".CFtools::getIdlangFromiso("es").", 'Identification', '', '', 'Identificación', '', ''),

(".$newcte_id.", ".CFtools::getIdlangFromiso("de").", 'Identification', '', '', 'Identifizierung', '', ''),

(".$newcte_id.", ".CFtools::getIdlangFromiso("it").", 'Identification', '', '', 'Identificazione', '', '');");





@@Db::getInstance()->ExecuteS("INSERT INTO `"._DB_PREFIX_."contactform_item` (`fdid`, `fid`, `fields_id`, `fields_name`, `confirmation`, `fields_valid`, `fields_type`, `fields_style`, `err_style`, `fields_suppl`, `fields_require`, `order`, `published`) VALUES

('', '".$Cte_id."', 'myemail', 'myemail', 0, 'email', 'email', '', '', '', 1, 2, 1);");

$newcte_id=mysql_insert_id();

@@Db::getInstance()->ExecuteS("INSERT INTO `"._DB_PREFIX_."contactform_item_lang` (`fdid`, `id_lang`, `fields_title`, `fields_desc`, `confirmation_txt`, `fields_default`, `error_txt`, `error_txt2`) VALUES

(".$newcte_id.", ".CFtools::getIdlangFromiso("en").", 'Your e-mail', '', '', 'Confirm your email', '', ''),

(".$newcte_id.", ".CFtools::getIdlangFromiso("fr").", 'Votre e-mail', '', '', 'Confirmer votre email', '', ''),

(".$newcte_id.", ".CFtools::getIdlangFromiso("es").", 'Tu e-mail', '', '', 'Confirme su correo electrónico', '', ''),

(".$newcte_id.", ".CFtools::getIdlangFromiso("de").", 'Ihre E-Mail', '', '', 'Bestätigen Sie Ihre E-Mail', '', ''),

(".$newcte_id.", ".CFtools::getIdlangFromiso("it").", 'Il tuo indirizzo e-mail', '', '', 'Conferma la tua email', '', '');");





@@Db::getInstance()->ExecuteS("INSERT INTO `"._DB_PREFIX_."contactform_item` (`fdid`, `fid`, `fields_id`, `fields_name`, `confirmation`, `fields_valid`, `fields_type`, `fields_style`, `err_style`, `fields_suppl`, `fields_require`, `order`, `published`) VALUES

('', '".$Cte_id."', 'Pseudo', 'Pseudo', 0, 'none', 'text', '', '', '', 1, 3, 1);");

$newcte_id=mysql_insert_id();

@@Db::getInstance()->ExecuteS("INSERT INTO `"._DB_PREFIX_."contactform_item_lang` (`fdid`, `id_lang`, `fields_title`, `fields_desc`, `confirmation_txt`, `fields_default`, `error_txt`, `error_txt2`) VALUES

(".$newcte_id.", ".CFtools::getIdlangFromiso("en").", 'Username', '', '', '', '', ''),

(".$newcte_id.", ".CFtools::getIdlangFromiso("fr").", 'Pseudo', '', '', '', '', ''),

(".$newcte_id.", ".CFtools::getIdlangFromiso("es").", 'Username', '', '', '', '', ''),

(".$newcte_id.", ".CFtools::getIdlangFromiso("de").", 'Benutzername', '', '', '', '', ''),

(".$newcte_id.", ".CFtools::getIdlangFromiso("it").", 'Username', '', '', '', '', '');");







@@Db::getInstance()->ExecuteS("INSERT INTO `"._DB_PREFIX_."contactform_item` (`fdid`, `fid`, `fields_id`, `fields_name`, `confirmation`, `fields_valid`, `fields_type`, `fields_style`, `err_style`, `fields_suppl`, `fields_require`, `order`, `published`) VALUES

('', '".$Cte_id."', 'psw', 'password', 1, 'none', 'password', '', '', '', 1, 4, 1);");

$newcte_id=mysql_insert_id();

@@Db::getInstance()->ExecuteS("INSERT INTO `"._DB_PREFIX_."contactform_item_lang` (`fdid`, `id_lang`, `fields_title`, `fields_desc`, `confirmation_txt`, `fields_default`, `error_txt`, `error_txt2`) VALUES

(".$newcte_id.", ".CFtools::getIdlangFromiso("en").", 'Password', '', 'Repeat password', '', '', ''),

(".$newcte_id.", ".CFtools::getIdlangFromiso("fr").", 'Mot de passe', '', 'Répéter le mot de passe', '', '', ''),

(".$newcte_id.", ".CFtools::getIdlangFromiso("es").", 'Contraseña', '', 'Repita la contraseña', '', '', ''),

(".$newcte_id.", ".CFtools::getIdlangFromiso("de").", 'Passwort', '', 'Kennwort wiederholen', '', '', ''),

(".$newcte_id.", ".CFtools::getIdlangFromiso("it").", 'Password', '', 'Ripetere la password', '', '', '');");









@@Db::getInstance()->ExecuteS("INSERT INTO `"._DB_PREFIX_."contactform_item` (`fdid`, `fid`, `fields_id`, `fields_name`, `confirmation`, `fields_valid`, `fields_type`, `fields_style`, `err_style`, `fields_suppl`, `fields_require`, `order`, `published`) VALUES

('', '".$Cte_id."', 'sep2', 'sep2', 0, 'none', 'separator', '', '', '', 0, 5, 1);");

$newcte_id=mysql_insert_id();



@@Db::getInstance()->ExecuteS("INSERT INTO `"._DB_PREFIX_."contactform_item_lang` (`fdid`, `id_lang`, `fields_title`, `fields_desc`, `confirmation_txt`, `fields_default`, `error_txt`, `error_txt2`) VALUES

(".$newcte_id.", ".CFtools::getIdlangFromiso("en").", '', '', '', 'Personal Information', '', ''),

(".$newcte_id.", ".CFtools::getIdlangFromiso("fr").", '', '', '', 'Information personnelle', '', ''),

(".$newcte_id.", ".CFtools::getIdlangFromiso("es").", '', '', '', 'Información Personal', '', ''),

(".$newcte_id.", ".CFtools::getIdlangFromiso("de").", '', '', '', 'Persönliche Informationen', '', ''),

(".$newcte_id.", ".CFtools::getIdlangFromiso("it").", '', '', '', 'Dati Personali', '', '');");







@@Db::getInstance()->ExecuteS("INSERT INTO `"._DB_PREFIX_."contactform_item` (`fdid`, `fid`, `fields_id`, `fields_name`, `confirmation`, `fields_valid`, `fields_type`, `fields_style`, `err_style`, `fields_suppl`, `fields_require`, `order`, `published`) VALUES

('', '".$Cte_id."', 'sexe', 'sexe', 0, 'none', 'radio', '', '', '', 1, 6, 1);");

$newcte_id=mysql_insert_id();

@@Db::getInstance()->ExecuteS("INSERT INTO `"._DB_PREFIX_."contactform_item_lang` (`fdid`, `id_lang`, `fields_title`, `fields_desc`, `confirmation_txt`, `fields_default`, `error_txt`, `error_txt2`) VALUES

(".$newcte_id.", ".CFtools::getIdlangFromiso("en").", 'Sex', '', '', 'Male;Female', '', ''),

(".$newcte_id.", ".CFtools::getIdlangFromiso("fr").", 'Sexe', '', '', 'Homme;Femme', '', ''),

(".$newcte_id.", ".CFtools::getIdlangFromiso("es").", 'Sexo', '', '', 'Hombre;Mujer', '', ''),

(".$newcte_id.", ".CFtools::getIdlangFromiso("de").", 'Sex', '', '', 'Männlich;Weiblich', '', ''),

(".$newcte_id.", ".CFtools::getIdlangFromiso("it").", 'Sesso', '', '', 'Maschio;Femmina', '', '');");







@@Db::getInstance()->ExecuteS("INSERT INTO `"._DB_PREFIX_."contactform_item` (`fdid`, `fid`, `fields_id`, `fields_name`, `confirmation`, `fields_valid`, `fields_type`, `fields_style`, `err_style`, `fields_suppl`, `fields_require`, `order`, `published`) VALUES

('', '".$Cte_id."', 'name', 'name', 0, 'none', 'text', '', '', 'size=\"25\"', 1, 7, 1);");

$newcte_id=mysql_insert_id();

@@Db::getInstance()->ExecuteS("INSERT INTO `"._DB_PREFIX_."contactform_item_lang` (`fdid`, `id_lang`, `fields_title`, `fields_desc`, `confirmation_txt`, `fields_default`, `error_txt`, `error_txt2`) VALUES

(".$newcte_id.", ".CFtools::getIdlangFromiso("en").", 'Your full name', '', '', 'Your full name ...', '', ''),

(".$newcte_id.", ".CFtools::getIdlangFromiso("fr").", 'Votre nom et prenom', '', '', 'Votre nom ...', '', ''),

(".$newcte_id.", ".CFtools::getIdlangFromiso("es").", 'Su nombre', '', '', 'Su nombre ..', '', ''),

(".$newcte_id.", ".CFtools::getIdlangFromiso("de").", 'Ihr vollständiger Name', '', '', 'Ihr Name ...', '', ''),

(".$newcte_id.", ".CFtools::getIdlangFromiso("it").", 'Il tuo nome completo', '', '', 'Il tuo nome completo', '', '');");







@@Db::getInstance()->ExecuteS("INSERT INTO `"._DB_PREFIX_."contactform_item` (`fdid`, `fid`, `fields_id`, `fields_name`, `confirmation`, `fields_valid`, `fields_type`, `fields_style`, `err_style`, `fields_suppl`, `fields_require`, `order`, `published`) VALUES

('', '".$Cte_id."', 'state', 'state', 0, 'none', 'country', '', '', '', 1, 8, 1);");

$newcte_id=mysql_insert_id();

@@Db::getInstance()->ExecuteS("INSERT INTO `"._DB_PREFIX_."contactform_item_lang` (`fdid`, `id_lang`, `fields_title`, `fields_desc`, `confirmation_txt`, `fields_default`, `error_txt`, `error_txt2`) VALUES

(".$newcte_id.", ".CFtools::getIdlangFromiso("en").", 'State', '', '', '', '', ''),

(".$newcte_id.", ".CFtools::getIdlangFromiso("fr").", 'Pays', '', '', '', '', ''),

(".$newcte_id.", ".CFtools::getIdlangFromiso("es").", 'País', '', '', '', '', ''),

(".$newcte_id.", ".CFtools::getIdlangFromiso("de").", 'Land', '', '', '', '', ''),

(".$newcte_id.", ".CFtools::getIdlangFromiso("it").", 'Paese', '', '', '', '', '');");



@@Db::getInstance()->ExecuteS("INSERT INTO `"._DB_PREFIX_."contactform_item` (`fdid`, `fid`, `fields_id`, `fields_name`, `confirmation`, `fields_valid`, `fields_type`, `fields_style`, `err_style`, `fields_suppl`, `fields_require`, `order`, `published`) VALUES

('', '".$Cte_id."', 'datebirth', 'datebirth', 0, 'none', 'calendar', '', '', '', 0, 9, 1);");

$newcte_id=mysql_insert_id();



@@Db::getInstance()->ExecuteS("INSERT INTO `"._DB_PREFIX_."contactform_item_lang` (`fdid`, `id_lang`, `fields_title`, `fields_desc`, `confirmation_txt`, `fields_default`, `error_txt`, `error_txt2`) VALUES

(".$newcte_id.", ".CFtools::getIdlangFromiso("en").", 'Date of birth', '', '', '', '', ''),

(".$newcte_id.", ".CFtools::getIdlangFromiso("fr").", 'Date de naissance', '', '', '', '', ''),

(".$newcte_id.", ".CFtools::getIdlangFromiso("es").", 'Fecha de Nacimiento', '', '', '', '', ''),

(".$newcte_id.", ".CFtools::getIdlangFromiso("de").", 'Datum der Geburt', '', '', '', '', ''),

(".$newcte_id.", ".CFtools::getIdlangFromiso("it").", 'Data di nascita', '', '', '', '', '');");









@@Db::getInstance()->ExecuteS("INSERT INTO `"._DB_PREFIX_."contactform_item` (`fdid`, `fid`, `fields_id`, `fields_name`, `confirmation`, `fields_valid`, `fields_type`, `fields_style`, `err_style`, `fields_suppl`, `fields_require`, `order`, `published`) VALUES

('', '".$Cte_id."', 'captcha', 'captcha', 1, 'none', 'captcha', '', '', '', 0, 10, 1);");

$newcte_id=mysql_insert_id();

@@Db::getInstance()->ExecuteS("INSERT INTO `"._DB_PREFIX_."contactform_item_lang` (`fdid`, `id_lang`, `fields_title`, `fields_desc`, `confirmation_txt`, `fields_default`, `error_txt`, `error_txt2`) VALUES

(".$newcte_id.", ".CFtools::getIdlangFromiso("en").",  'Verification code', '', 'Retape code here', '', '', ''),

(".$newcte_id.", ".CFtools::getIdlangFromiso("fr").", 'Code de sécurity', '', 'Recopier le code ici', '', '', ''),

(".$newcte_id.", ".CFtools::getIdlangFromiso("es").", 'Código de verificación', '', 'Copia el código aquí', '', '', ''),

(".$newcte_id.", ".CFtools::getIdlangFromiso("de").",  'Bestätigungs-Code', '', 'Kopieren Sie den Code hier', '', '', ''),

(".$newcte_id.", ".CFtools::getIdlangFromiso("it").", 'Codice di verifica', '', 'Riscrivi il codice qui', '', '', '');");	







@@Db::getInstance()->ExecuteS("INSERT INTO `"._DB_PREFIX_."contactform_item` (`fdid`, `fid`, `fields_id`, `fields_name`, `confirmation`, `fields_valid`, `fields_type`, `fields_style`, `err_style`, `fields_suppl`, `fields_require`, `order`, `published`) VALUES

('', '".$Cte_id."', 'activity', 'activity', 0, 'none', 'checkbox', '', '', 'style=\"margin-top:10px;display:inline-table\"', 0, 11, 1);");

$newcte_id=mysql_insert_id();

@@Db::getInstance()->ExecuteS("INSERT INTO `"._DB_PREFIX_."contactform_item_lang` (`fdid`, `id_lang`, `fields_title`, `fields_desc`, `confirmation_txt`, `fields_default`, `error_txt`, `error_txt2`) VALUES

(".$newcte_id.", ".CFtools::getIdlangFromiso("en").", 'Business Area', '', '', 'Trade;Technology;Agriculture;Communication;Computers; Transportation', '', ''),

(".$newcte_id.", ".CFtools::getIdlangFromiso("fr").", 'Secteur d''activité', '', '', 'Commerce;Technologie;Agriculture;Communication;Informatique;Transport', '', ''),

(".$newcte_id.", ".CFtools::getIdlangFromiso("es").", 'Área de Negocios', '', '', 'Comercio;Tecnología;Agricultura;Comunicaciones;Informática;Transporte', '', ''),

(".$newcte_id.", ".CFtools::getIdlangFromiso("de").", 'Business Area', '', '', 'Handel; Technologie; Landwirtschaft, Kommunikation;Computer; Transporter', '', ''),

(".$newcte_id.", ".CFtools::getIdlangFromiso("it").", 'Area di Business', '', '', 'Commercio; tecnologia;agricoltura;comunicazione; computer;Trasporto', '', '');");





@@Db::getInstance()->ExecuteS("INSERT INTO `"._DB_PREFIX_."contactform_item` (`fdid`, `fid`, `fields_id`, `fields_name`, `confirmation`, `fields_valid`, `fields_type`, `fields_style`, `err_style`, `fields_suppl`, `fields_require`, `order`, `published`) VALUES

('', '".$Cte_id."', 'sep3', 'sep3', 0, 'none', 'separator', '', '', '', 0, 12, 1);");

$newcte_id=mysql_insert_id();

@@Db::getInstance()->ExecuteS("INSERT INTO `"._DB_PREFIX_."contactform_item_lang` (`fdid`, `id_lang`, `fields_title`, `fields_desc`, `confirmation_txt`, `fields_default`, `error_txt`, `error_txt2`) VALUES

(".$newcte_id.", ".CFtools::getIdlangFromiso("en").", '', '', '', '', '', ''),

(".$newcte_id.", ".CFtools::getIdlangFromiso("fr").", '', '', '', '', '', ''),

(".$newcte_id.", ".CFtools::getIdlangFromiso("es").", '', '', '', '', '', ''),

(".$newcte_id.", ".CFtools::getIdlangFromiso("de").", '', '', '', '', '', ''),

(".$newcte_id.", ".CFtools::getIdlangFromiso("it").", '', '', '', '', '', '');");







@@Db::getInstance()->ExecuteS("INSERT INTO `"._DB_PREFIX_."contactform_item` (`fdid`, `fid`, `fields_id`, `fields_name`, `confirmation`, `fields_valid`, `fields_type`, `fields_style`, `err_style`, `fields_suppl`, `fields_require`, `order`, `published`) VALUES																  

('', '".$Cte_id."', 'submit', 'submit', 0, 'none', 'submitbtn', '', '', '', 0, 13, 1);");

$newcte_id=mysql_insert_id();

@@Db::getInstance()->ExecuteS("INSERT INTO `"._DB_PREFIX_."contactform_item_lang` (`fdid`, `id_lang`, `fields_title`, `fields_desc`, `confirmation_txt`, `fields_default`, `error_txt`, `error_txt2`) VALUES

(".$newcte_id.", ".CFtools::getIdlangFromiso("en").", '', '', '', 'Send', '', ''),

(".$newcte_id.", ".CFtools::getIdlangFromiso("fr").", '', '', '', 'Envoyer', '', ''),

(".$newcte_id.", ".CFtools::getIdlangFromiso("es").", '', '', '', 'Enviar', '', ''),

(".$newcte_id.", ".CFtools::getIdlangFromiso("de").", '', '', '', 'Senden', '', ''),

(".$newcte_id.", ".CFtools::getIdlangFromiso("it").", '', '', '', 'Invia', '', '');");





break;	



}//End switch





$url=__PS_BASE_URI__.'contact-form.php?fid='.$Cte_id;

	$txtSample=CFtools::l('Your sample has been saved successfully').'&nbsp;&nbsp;<a target="_blank" class="link" href='.$url.'>'.CFtools::l('Preview').'</a>>>';

	$output  = self::_validRes($txtSample);

	$output .= self::_addsample($mypath);

	return $output;



	

	

}







public static function _seedata2($link,$asc,$orderby,$task,$pagelimit=10,$start=0){

		global $cookie;

		$lastpage =0;

		$mytoken=Tools::getValue('token');

		$dataLists = Db::getInstance()->ExecuteS('SELECT * FROM '._DB_PREFIX_.'contactform_data ORDER BY `'.$orderby.'` '.$asc.' LIMIT '.$start.','.$pagelimit.'  ');

		$imgy='<img width="12" height="12" alt="" src="'.$link.'img/ok.png">';

		$imgn='<img width="12" height="12" alt="" src="'.$link.'img/del.png">';

		$output='';

		

		

		

		$url='index.php?tab=AdminModules&configure=contactform&task=seedata&token='.$mytoken;

		$url2='index.php?tab=AdminModules&configure=contactform&task=seedatadetails&token='.$mytoken;

		$url3='index.php?tab=AdminModules&configure=contactform&task=deldata&token='.$mytoken;

		if($asc=='ASC')

			$asc='DESC';

		elseif($asc=='DESC')

			$asc='ASC';

		$output=CFtoolbar::toolbar('classic',$link);

		

		$output .='<fieldset><legend><img src="'.$link.'img/listform.png" alt="" title="" />'.CFtools::l('Data list').'</legend>';

		$output.='<div id="itemList" class="itemList">';

		

		//All data

		$alldata = Db::getInstance()->ExecuteS('SELECT * FROM '._DB_PREFIX_.'contactform_data ORDER BY `'.$orderby.'` '.$asc);

		$maxstart=count($alldata)-$pagelimit;

		$output .='<a href="'.$url.'&pagelimit='.$pagelimit.'&start=0"><< - </a>';

		for($i=0;$i<count($alldata)/$pagelimit;$i++){

			

			$output .='<a href="'.$url.'&pagelimit='.$pagelimit.'&start='.$pagelimit*$i.'">'.($i+1).' - '.'</a>';

			$lastpage=$pagelimit*$i;

		}

		

		$output .='<script type="text/javascript">

		checked=false;

		function checkedAll (frm1) {

		var aa= document.getElementById("frm1");

	 	if (checked == false)

          	{

           	checked = true

          	}

        else

          {

          checked = false

          }

		for (var i =0; i < aa.elements.length; i++) 

		{

	 		aa.elements[i].checked = checked;

		}

      }

	</script>';

		

		$output .='<a href="'.$url.'&pagelimit='.$pagelimit.'&start='.$lastpage.'">>></a>';

		

		$output.='<form id ="frm1" name="frm1" method="post" action="'.$_SERVER['REQUEST_URI'].'" >';

		$output.='<table width="100%" class="table" cellspacing="0" cellpadding="0">';

		$output.='<thead><tr class="nodrag nodrop" >

						<th><input type="checkbox" name="checkall" onclick="checkedAll(frm1);"></th>

						<th><a href="'.$url.'&asc='.$asc.'&orderby=data_id">ID</a></th>

						<th><a href="'.$url.'&asc='.$asc.'&orderby=ip">'.CFtools::l('Ip address').'</a></th>

						<th><a href="'.$url.'&asc='.$asc.'&orderby=date">'.CFtools::l('Date').'</a></th>

						<th><a href="'.$url.'&asc='.$asc.'&orderby=toemail">'.CFtools::l('Mail to').'</a></th>

						<th><a href="'.$url.'&asc='.$asc.'&orderby=foremail">'.CFtools::l('Mail From').'</a></th>

						<th><a href="'.$url.'&asc='.$asc.'&orderby=info">'.CFtools::l('Message sent').'</a></th>

						<th><a href="'.$url.'&asc='.$asc.'&orderby=statut_mail">'.CFtools::l('Mail Statut').'</a></th>

						<th>'.CFtools::l('Actions').'</th></tr></thead>';

		

		

		foreach($dataLists as $dataList){

			$output.='<tr valign="top">';

						$output.='<td align="left"><input type="checkbox" name="actlink['.$dataList['data_id'].']" value="1"></td>';

						$output.='<td><a href="'.$url2.'&data_id='.$dataList['data_id'].'">'.$dataList['data_id'].'</a></td>';

						$output.='<td><a href="'.$url2.'&data_id='.$dataList['data_id'].'">'.$dataList['ip'].'</a></td>';

						$tabdate=explode('/',$dataList['date']);

						if(CFtools::getIsocode($cookie->id_lang)=='fr')

							$mdate=$tabdate[1].'/'.$tabdate[0].'/'.$tabdate[2];

						else

							$mdate=$dataList['date'];

						$output.='<td><a href="'.$url2.'&data_id='.$dataList['data_id'].'">'.$mdate.'</a></td>';

						$output.='<td><a href="'.$url2.'&data_id='.$dataList['data_id'].'">'.$dataList['toemail'].'</a></td>';

						$output.='<td><a href="'.$url2.'&data_id='.$dataList['data_id'].'">'.$dataList['foremail'].'</a></td>';

						$output.='<td><a href="'.$url2.'&data_id='.$dataList['data_id'].'">'.CFtools::_substrStr(50,$dataList['info']).'</a></td>';

						if($dataList['statut_mail']=='mail'){

							$output.='<td><a href="'.$url2.'&data_id='.$dataList['data_id'].'">'.$imgy.CFtools::l('Mail sent').'</a></td>';

						}

						else{

							$output.='<td><a href="'.$url2.'&data_id='.$dataList['data_id'].'">'.$imgn.CFtools::l('Mail not sent').'</a></td>';

						}

						$output.='<td>

							<a  title="" href="'.$url2.'&data_id='.$dataList['data_id'].'">

							<img alt="" src="'.$link.'img/preview.png">

							</a>

						<a title="" href="'.$url3.'&data_id='.$dataList['data_id'].'" onclick="return(confirm(\''.CFtools::l('Do you really want to delete this message?').'\'));">

						<img alt="" src="'.$link.'img/delete.png">

						</a>

						

						

						

						</td>';

			$output.='</tr>';

		}

		$output.='</table>';

		$output.='<input style="margin:10px;" class="button" type="submit" name="deleteselectdata" value="'.CFtools::l('Delete selected').'" onclick="return(confirm(\''.CFtools::l('Do you really want  to deleted data?').'\'));">';

		$output.='</form>';

		$output.='</div>';

		$output.='</fieldset>';

		

		

		return $output;

	

}



















	public static function _validRes($txt){

		$output='<div style="border:1px solid #999999; background-color:#B3E1EF; width:99%; margin-bottom:20px; padding:5px">'.CFtools::l($txt).'</div>';

		return $output;

	} 





	

 public  static function _errFormat($errmsg,$nbErr,$shownbr){

	global $cookie;

  $output ='<div style="border:1px solid #999999; background-color:#FFDFDF; width:99%; margin-bottom:20px; padding:5px">';

  	if($shownbr)

		$output .='<font color=red>'.CFtools::l('There are').' '.$nbErr.' '.CFtools::l('error(s)').'</font>:<br><br>';

  $output .=$errmsg;

  $output .='</div>';

  return $output;

 }





public static function _deldata($link,$data_id){

	$mytoken=Tools::getValue('token');

	$delMsg = Db::getInstance()->ExecuteS('DELETE FROM '._DB_PREFIX_.'contactform_data WHERE data_id='.$data_id);

	header("location:index.php?tab=AdminModules&configure=contactform&task=seedata&token=".$mytoken);

}



public static function _seedatadetails($link,$data_id,$task){

	$output='';

	global $cookie;

	$mytoken=Tools::getValue('token');

	$imgy='<img width="12" height="12" alt="" src="'.$link.'img/ok.png">';

	$imgn='<img width="12" height="12" alt="" src="'.$link.'img/del.png">';

	

	$output .=CFtoolbar::toolbar('seedetails',$link);

	

	$ListMsg = Db::getInstance()->ExecuteS('SELECT * FROM '._DB_PREFIX_.'contactform_data WHERE data_id='.$data_id);

	$output .='<fieldset><legend><img src="'.$link.'logo.gif" alt="" title="" />'.CFtools::l('Data details').' - ID = '.$ListMsg[0]['data_id'].'</legend>';

	$output .='<b>'.CFtools::l('Ip address').'</b> = '.$ListMsg[0]['ip'].'<br>';

	

		$tabdate=explode('/',$ListMsg[0]['date']);

		if(CFtools::getIsocode($cookie->id_lang)=='fr')

			$ddate=$tabdate[1].'/'.$tabdate[0].'/'.$tabdate[2];

		else

			$ddate=$ListMsg[0]['date'];

							

	$output .='<b>'.CFtools::l('Date').'</b> = '.$ddate.'<br>';

	$output .='<b>'.CFtools::l('Mail to').'</b> = '.$ListMsg[0]['toemail'].'<br>';

	$output .='<b>'.CFtools::l('Mail From').'</b> = '.$ListMsg[0]['foremail'].'<br>';

	$output .='<b>'.CFtools::l('Message sent').'</b> = <br><blockquote style="margin-left:40px">'.$ListMsg[0]['info'].'</blockquote>';

	

	if($ListMsg[0]['statut_mail']=='mail'){

				$output.='<b>'.CFtools::l('Mail Statut').'</b> = '.$imgy.CFtools::l('Mail sent').'<br>';

			}

	else{

				$output.='<b>'.CFtools::l('Mail Statut').'</b> = '.$imgn.CFtools::l('Mail not sent').'<br>';

						}

	

	$output .='</fieldset>';

	return $output;

}





public static function seo($mypath){

	

	$mytoken=Tools::getValue('token');

	$output =CFtoolbar::toolbar('seedetails',$mypath);

	

	$output .='<fieldset><legend><img width=30 src="'.$mypath.'img/seo.png" alt="" title="" />'.CFtools::l('Url rewriting').'</legend>';

	

	$output .='</fieldset>';

	return $output;





}





 public static function _editcss($link){

	global $cookie;

	$mytoken=Tools::getValue('token');

	$output='';

	$output.=CFtoolbar::toolbar('classic',$link);

	

	if(Configuration::get('CONTACTFORM_FORM')== 0)

		$file=_PS_MODULE_DIR_.'contactform/library/basicform/css/css.css';

	else{

	if(Configuration::get('CONTACTFORM_STYLE')== 1)

			$file=_PS_MODULE_DIR_.'contactform/library/form/css/niceform.css';

		else

			$file=_PS_MODULE_DIR_.'contactform/library/form/css/template.css';

	}

	

	$output.='<fieldset><legend><img src="'.$link.'logo.gif" alt="" title="" />'.CFtools::l('Edit Css').'</legend>';

	if(file_exists($file)){

		chmod($file,0666);

		$fpcss = fopen ($file, 'r');

		$output.= '<form method="POST" action="'.$_SERVER['REQUEST_URI'].'">';

		$output.= '<center><textarea name="newcss" cols="140" rows="20">';

		

		while(!feof($fpcss)) {

			$output.= fgets ($fpcss, 1255);

		}

		

			$output.= "</textarea></center>";

			$output.= '<br><input class="button" type="submit" name="subeditcss" value="'.CFtools::l('   Save   ').'">';

			$output.= '</form>';

		

		fclose($fpcss);

	}

	else

		$output.=CFtools::l('File  doesn\'t exist').':'.$file;

	$output.='</fieldset>';



  return $output;

 }







public static function _showPreview($title,$location,$mypath,$imgpath,$btnname,$uploadname,$default,$width=32,$height=32,$upload=1,$msg_body){

$dir = opendir($location);

$output = '';



$output.='

<script type="text/javascript">

$(document).ready(function(){

	//hide the all of the element with class msg_body

	$(".'.$msg_body.'").hide();

	//toggle the componenet with class msg_body

	$(".msg_head").click(function(){

		$(this).next(".'.$msg_body.'").slideToggle(600);

	});

});

</script>

<style type="text/css">

.msg_head {

	padding: 2px 10px;

	cursor: pointer;

	position: relative;

	background-color:#777777;

	background: url('.$mypath.'img/down.png) no repeat right;

	margin:1px;

}

.'.$msg_body.' {

	padding: 5px 10px 15px;

	background-color:#F4F4F8;

	width:252px;

}

</style>

';

$output.='<tr>';

$output.='<td>'.CFtools::l($title).' : </td>';



$output.='<td>';

$output.='<div class="msg_list">';

$output.='<p class="msg_head">'.CFtools::l('Clic to show').'</p>

		<div class="'.$msg_body.'">';

if($upload==1)

	$output .= '<div><input type="file" name="'.$uploadname.'"></div><br>';

$compteur=0;



while($file = readdir($dir)) {

	 		$ttf=explode('.',$file);

	 		if($file!="."&&$file!=".."&&$ttf[1]!="db"){

				$output .='<span><img width="'.$width.'" style=" margin-bottom:3px" height="'.$height.'" src="'.$mypath.$imgpath.$file.'"><input '.($default==$file?'checked="checked"':'').'  type="radio" name="'.$btnname.'" value="'.$file.'"></span>';	

			}

		$compteur++;

		}

		

$output.='</div>';

$output.='</div>';



$output.='</td>';

$output.='</tr>';



return $output;



} 







public static function _showfont($title,$location,$mypath,$imgpath,$btnname,$uploadname,$default,$upload=1,$msg_body){

$dir = opendir($location);

$output = '';



$output.='

<script type="text/javascript">

$(document).ready(function(){

	//hide the all of the element with class msg_body

	$(".'.$msg_body.'").hide();

	//toggle the componenet with class msg_body

	$(".msg_head").click(function(){

		$(this).next(".'.$msg_body.'").slideToggle(600);

	});

});

</script>

<style type="text/css">

.msg_head {

	padding: 2px 10px;

	cursor: pointer;

	position: relative;

	background-color:#777777;

	background: url('.$mypath.'img/down.png) no repeat right;

	margin:1px;

}

.'.$msg_body.' {

	padding: 5px 10px 15px;

	background-color:#F4F4F8;

	width:252px;

}

</style>

';

$output.='<tr>';

$output.='<td>'.CFtools::l($title).' : </td>';



$output.='<td>';

$output.='<div class="msg_list">';

$output.='<p class="msg_head">'.CFtools::l('Clic to show').'</p>

		<div class="'.$msg_body.'">';

if($upload==1)

	$output .= '<div><input type="file" name="'.$uploadname.'"></div><br>';

$compteur=0;



while($file = readdir($dir)) {

	 		$ttf=explode('.',$file);

	 		if($file!="."&&$file!=".."&&$ttf[1]!="db"){

				$output .='<span>'.$file.'"<input '.($default==$file?'checked="checked"':'').'  type="radio" name="'.$btnname.'" value="'.$file.'"></span>&nbsp;&nbsp;';	

			}

		$compteur++;

		}

		

$output.='</div>';

$output.='</div>';



$output.='</td>';

$output.='</tr>';



return $output;



} 

















public static function _uploadimgFile($content_dir,$uploadname,$var_toup,$var_get,$format){

	

				$error=0;

			//UPDATE ARROW IMAGE

			if(!empty($_FILES[$uploadname]['tmp_name'])){

					//$content_dir = dirname(__FILE__).'/themes/vertical/images/arrows/'; // dossier où sera déplacé le fichier

					$tmp_file = $_FILES[$uploadname]['tmp_name'];

					$type_file = $_FILES[$uploadname]['type'];

					$name_file = $_FILES[$uploadname]['name'];

					if($format==1)

						$format = array('jpg','png','gif');

					elseif($format==2)

						$format = array('ttf');

					

					$ext=explode('.', $name_file);

					$maxindex=count($ext)-1;

				

					if( !is_uploaded_file($tmp_file) ){

						$error=1;

					}

					if($error==0){

						if(!in_array($ext[$maxindex],$format)){

							$error=2;

						}

					}

					

					if($error==0){

						if( !move_uploaded_file($tmp_file, $content_dir . $name_file) )

						{

							$error=3;

						}

					}

		

					if($error==0)

					Configuration::updateValue($var_toup, $name_file);	

					

					return $error;

			}

			else{

					Configuration::updateValue($var_toup, $var_get);		

			}	



}





public static function iswriteimgDir($directory,$mypath){



if (is_writable($directory)) {

   return  '<div class="conf"><img src="'.$mypath.'img/ok2.png">'.CFtools::l('Directory writable').': '.$directory.'</div>';

} else {

   return '<div class="error"><img src="'.$mypath.'img/error.png">'.CFtools::l('Unable to write to the directory').': '.$directory.'</div>';

}



}





public static function DS(){

	$ds = DIRECTORY_SEPARATOR;

	if(!empty($ds)&& $ds!='')

		return DIRECTORY_SEPARATOR;

	else

		return '/';

}



public static function _newsliderline2($title,$fieldname,$default,$Id,$max,$min){

	

$var = $Id.'x';

 $output = '<tr>';

	$output .= '<td>'.CFtools::l($title).':</td><td></td>';

	$output .= '</tr>';



$output .= '<tr>';

	$output .= '<td><div class="slider" id="slider-'.$Id.'" tabIndex="1"><input class="slider-input" id="slider-input-'.$Id.'"/></div></td><td><input id="h'.$Id.'-value" onchange="a.setValue(parseInt(this.value))" size="6" name="'.$fieldname.'"/>

</td>';

$output .= '</tr>';



$output .='<script type="text/javascript">



var '.$var.' = new Slider(document.getElementById("slider-'.$Id.'"), document.getElementById("slider-input-'.$Id.'"));

'.$var.'.onchange = function () {

	document.getElementById("h'.$Id.'-value").value = '.$var.'.getValue();

};

'.$var.'.setValue('.$default.');

'.$var.'.setMaximum('.$max.');

'.$var.'.setMinimum('.$min.');



window.onresize = function () {

	'.$var.'.recalculate();

};



</script>';



return $output;

	

}



public static function _useSlider($mypath){

	$output = '<link rel="stylesheet" type="text/css" href="'.$mypath.'library/slider/css/luna.css" />';

	$output.='<script type="text/javascript" src="'.$mypath.'library/slider/js/range.js"></script>';

	$output.='<script type="text/javascript" src="'.$mypath.'library/slider/js/slider.js"></script>';

	$output.='<script type="text/javascript" src="'.$mypath.'library/slider/js/timer.js"></script>';

	return $output;

}







}//End class





?>