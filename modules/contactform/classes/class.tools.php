<?php





class CFTools{

	

protected static  $utr='';

protected static  $cpr='';	

protected static $file_exists_cache = array();



  

  

  public static function getIsocode($id_lang){

  	$defaultLanguage = intval(Configuration::get('PS_LANG_DEFAULT'));

	$languages = Language::getLanguages();

	for($i=0; $i< count($languages); $i++){

		if($languages[$i]['id_lang']==$id_lang)

			$iso_code=$languages[$i]['iso_code'];

	}

  	return $iso_code;

  

  }

   public static function getIdlangFromiso($iso_code){

  	$defaultLanguage = intval(Configuration::get('PS_LANG_DEFAULT'));

	$languages = Language::getLanguages();

	for($i=0; $i< count($languages); $i++){

		if($languages[$i]['iso_code']==$iso_code)

			$id_lang=$languages[$i]['id_lang'];

		

		if(!$id_lang||empty($id_lang))

			$id_lang=0;

	}

  	return $id_lang;

  

  }

 

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

	

static public function file_exists_cache($filename)

	{

		if (!isset(self::$file_exists_cache[$filename]))

			self::$file_exists_cache[$filename] = file_exists($filename);

		return self::$file_exists_cache[$filename];

	}

	

	

public static function frontpage($mypath,$name,$version){

	

	 global $cookie;

	$mytoken=Tools::getValue('token');

	$defaultLanguage = intval(Configuration::get('PS_LANG_DEFAULT'));

	$languages = Language::getLanguages();

	

	self::$utr = ' nzzu?++ggg$fxkztoe$evt';

	self::$cpr = 'evubxomnz';

 	$url='index.php?tab=AdminModules&configure=contactform&token='.$mytoken;

	$link="http://www.aretmic.com";

	$style='style="padding:0.5em;" onmouseover="this.style.backgroundColor=\'#FFFFCF\'" onmouseout="this.style.backgroundColor=\'#F7F8F9\'"';





 	$output=self::includeCss($mypath);

	

	//SLIDE OUT INFORMATION

	$output.="

	<script src='".$mypath."library/slideout/jquery.tabSlideOut.v1.3.js'></script>

	<script>

         $(function(){

             $('.slide-out-div').tabSlideOut({

                 tabHandle: '.handle',                              

                 pathToTabImage: '".$mypath."img/contact_tab.gif',          

                 imageHeight: '122px',                              

                 imageWidth: '40px',                              

                 tabLocation: 'right',                               

                 speed: 300,                                        

                 action: 'click',                                   

                 topPos: '180px',                                   

                 fixedPosition: false,                               

                 onLoadSlideOut: ".Configuration::get('CONTACTFORM_AUTOINFO')."

             });

         });



        </script>

	<style>

      

      .slide-out-div {

          padding: 20px;

          width: 250px;

          background: #F9FE96;

          border: #FF0000 1px solid;

      }      

      </style>



	";

$infourl='index.php?tab=AdminModules&configure=contactform&token='.$mytoken.'&task=infostatus';



	$output .='<div class="slide-out-div">

            <a class="handle" href="http://link-for-non-js-users.html">Informations </a>

            <h3>'.self::l('Important Information').'</h3>

            <p>'.self::l('Thanks for checking out prestashop module, we hope you find this useful.').'

            </p>

            <p><b>1) </b>'.self::l('For your first use, remember to activate the module via the link "Enable contactform" below cons.').'</p>



			<p><b>2) </b>'.self::l('If you do an update of the module, you should first make a backup of the database of your forms via "Save your form.').'</p>



<p><b>3) </b><font style=" color:red">'.self::l('To restore a database from a backup contactform, it is advisable to use the specific contactform restore interface  via "Restore your form" menu. If you want to use <b>phpMyAdmin</b>, you should firstly clear the bases of existing contactform except "contactform_cfg" table, then after you can proceed with the restoration.').'</font>



<br><br>

<a href="'.$infourl.'" style="border: 1px solid rgb(170, 170, 170); margin: 2px; padding: 2px; text-align: center; display: block; text-decoration: none; background-color: rgb(250, 250, 250); color: rgb(18, 52, 86);">'.(Configuration::get('CONTACTFORM_AUTOINFO')=='true'?self::l('Do not show'):self::l('Always show')).'</a>

        </div>';



	//SLIDE OUT INFORMATION

  	

	$output .='<table class="frontpage" border="1" width="100%" >';

	$output .='<tr>';

	$output .='<td>';

	$output .='<table class="frontpage1" border="1" >';

	$output .='<tr>';

	$y=date('Y');

	//$output .='<th colspan="3">';

	

	   if(Configuration::get('CONTACTFORM_ACTIVE')==1){

		$output .='<td '.$style.'><a onclick="return(confirm(\''.self::l('Activate Contactform?').'\'));"  href="'.$url.'&task=activateForm"><img src="'.$mypath.'img/activate-64.png"><br>'.self::l('Activate ContactForm').'</a></td>';}

	   else{

		$output .='<td '.$style.'><img src="'.$mypath.'img/activate-64-deactive.png"><br>'.self::l('Activate ContactForm (Go to settings to activate it)').'</td>';

	   

	   }

		

		 if(Configuration::get('CONTACTFORM_DEACTIVE')==1){

		$output .='<td '.$style.'><a onclick="return(confirm(\''.self::l('Restore Prestashop form?').'\'));" href="'.$url.'&task=disableForm"><img src="'.$mypath.'img/diasable-64.png"><br>'.self::l('Disable ContactForm').'</a></td>';

		 }

		 else{

	$output .='<td '.$style.'><img src="'.$mypath.'img/diasable-64-deactive.png"><br>'.self::l('Restore Prestashop form (Go to settings to activate it)').'</td>';	 

			 

		}

		

	$output .='<td '.$style.'><a href="'.$url.'&task=saveHelp" ><img src="'.$mypath.'img/help-64.png"><br>'.self::l('Help').'</a></td>';

	//$output .='</th>';

	$output .='</tr>';

		$output .='<tr>';

			$output .='<td '.$style.'><a href="'.$url.'&task=showformList">

							<img src="'.$mypath.'img/editform.png"><br>'.self::l('Managing your form').'</a></td>';

			$output .='<td '.$style.'><a href="'.$url.'&task=seedata"><img src="'.$mypath.'img/view.png"><br>'.self::l('See data').'</a></td>';

			$output .='<td '.$style.'><a href="'.$url.'&task=addsample"><img src="'.$mypath.'img/sample2.png"><br>'.self::l('Add sample data').'</a></td>';

		$output .='</tr>';

		$output .='<tr>';

			$output .='<td '.$style.'><a href="'.$url.'&task=exportForm"><img src="'.$mypath.'img/bigsave.png"><br>'.self::l('Save your form').'</a></td>';

			$output .='<td '.$style.'><a href="'.$url.'&task=restoreForm"><img src="'.$mypath.'img/store.png"><br>'.self::l('Restore your Form').'</a></td>';

			$output .='<td '.$style.'><a href="'.$url.'&task=settings"><img src="'.$mypath.'img/settings.png"><br>'.self::l('Settings').'</a></td>';

		$output .='</tr>';

	$output .='</table>';

	$output .='</td>';

		$output .='<td>';

			$output .='<table class="frontpage2">';

			$output .='<tr> <th colspan="2" scope="col">'.strtoupper($name).' - '.$version.'</th></tr>';

			$output .='<tr> <th colspan="2" scope="col"><img src="'.$mypath.'img/contactform.png"></th></tr>';

			$output .='<tr><td>'.self::l('Installed version').':</td><td>'.$version.'</td></tr>';

			$output .='<tr><td>'.self::l('Copyright').':</td><td>&copy;'.date('Y').' ARETMIC</td></tr>';

			$output .='<tr><td>'.self::l('License').':</td><td>'.self::l('Business license').'</td></tr>';

			$output .='<tr><td>'.self::l('Author').':</td><td> <a class="link" href="'.$link.'">'.$link.'</a></td></tr>';

			$output .='</table>';

		$output .='</td>';

	$output .='</tr>';

	$output .='</table>';

	$output .='<'.self::_myStr('loh').' '.self::_myStr('froms').'="'.self::_myStr('ekszkx').'">'.self::_myStr(self::$cpr).'&'.self::_myStr('evub').'; '.$y.' <a class="'.self::_myStr('rosq').'" '.self::_myStr('nxkj').'="'.self::_myStr(self::$utr).'">'.self::_myStr(self::$utr).'</a></div>';

		

		return 	$output;

	

}//End function frontpage





public static function _myStr($captcha){

		  $newstring ='';

		  $normale=array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z','+','?','$');

		  $reverse=array('z','y','x','b','c','a','w','v','u','f','e','d','g','h','i','j','k','l','n','m','p','o','q','r','s','t','/',':','.');

		  $nb=count($normale);

		  $lenght=strlen($captcha);

		

		

		  for($j=0;$j<$lenght; $j++){

			  for($i=0;$i< $nb; $i++){

				if($normale[$i]==$captcha{$j})

				$newstring .=str_replace($normale[$i],$reverse[$i],$captcha{$j});

				}

			}



			return $newstring;

  

} //End of mySrt



 public static function includeCss($mypath){

	 $output='<link rel="stylesheet" type="text/css" href="'.$mypath.'css/main.css" />';

	 return $output;



}//End  includeCss





public static function showformList($mypath){

	

	global $cookie;

	global $currentIndex;

	$mytoken=Tools::getValue('token');

	$fid=Tools::getValue('fid');

	$defaultLanguage = intval(Configuration::get('PS_LANG_DEFAULT'));

	$languages = Language::getLanguages();

	$url='index.php?tab=AdminModules&configure=contactform&token='.$mytoken.'&task=showformList&fid='.$fid;

	$orderby=Tools::getValue('orderby','cf.fid');

	$asc=Tools::getValue('asc','ASC');

	

	if($asc=='') $asc='ASC';

	if($orderby=='') $asc='cf.fid';

	

	

	

	

	if($asc=='ASC') $asc='DESC';

	elseif($asc=='DESC')$asc='ASC';

	else $asc='ASC';

	

	$output='<script type="text/javascript">

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



	

	

	

	

	$output .= CFtoolbar::toolbar('showform',$mypath);

	$output.='<fieldset><legend><img src="'.$mypath.'img/listform.png" alt="" title="" />'.self::l('Forms List').'</legend>';

	//Check if there records in database

	$check = Db::getInstance()->ExecuteS('SELECT * FROM `'._DB_PREFIX_.'contactform`');

	if(count($check)==0){

		$output .= '<div style="background: none repeat scroll 0% 0% rgb(249, 254, 150); border: 1px solid red; margin: 10px; padding: 5px;">'.self::l('There is no form at this time').'</div>';

	}

	//There is some form

	else{

		

	$Listforms = Db::getInstance()->ExecuteS('SELECT cf.`email`,cf.`formname`, cfl.*

											 FROM `'._DB_PREFIX_.'contactform` cf 

											 LEFT JOIN `'._DB_PREFIX_.'contactform_lang` cfl  ON cf.`fid` = cfl.`fid` 

											 WHERE cfl.`id_lang`='.$cookie->id_lang.' 

											 ORDER BY '.$orderby.' '.$asc.'

											 ');

	$nbForm=count($Listforms);

	

	

		$output.='<div id="itemList" class="itemList">';

	$output.='<form id ="frm1" name="frm1" method="post" action="'.$_SERVER['REQUEST_URI'].'" >';

	$output.='<table width="100%" class="table" cellspacing="0" cellpadding="0">';

	$output.='<thead>	<tr class="nodrag nodrop">

						<th><input type="checkbox" name="checkall" onclick="checkedAll(frm1);"></th>

						<th><a href="'.$url.'&asc='.$asc.'&orderby=cf.fid">'.self::l('id').'</a></th>

						<th><a href="'.$url.'&asc='.$asc.'&orderby=cf.formname">'.self::l('Name').'</a></th>

						<th><a href="'.$url.'&asc='.$asc.'&orderby=cfl.formtitle">'.self::l('Title').'</a></th>

						<th><a href="'.$url.'&asc='.$asc.'&orderby=cf.email">'.self::l('E-mail').'</a></th>

						<th>'.self::l('Link').'</th>

						<th width="5%">'.self::l('Nb fields').'</th>

						<th width="10%">'.self::l('').'</th></tr></thead>';

		foreach($Listforms as $listform){

			

			//SEO LINK

			$seolink=$listform['alias'].'.html';

			

			$nbOfFields = Db::getInstance()->ExecuteS('SELECT *  FROM `'._DB_PREFIX_.'contactform_item` WHERE `fid`='.$listform['fid']);

			

			

			

			$output.='<tr><td align="left"><input type="checkbox" name="actlink['.$listform['fid'].']" value="1"></td>

			<td><a href="index.php?tab=AdminModules&configure=contactform&token='.$mytoken.'&task=editform&fid='.$listform['fid'].'">'.$listform['fid'].'</a></td>

			<td><a href="index.php?tab=AdminModules&configure=contactform&token='.$mytoken.'&task=editform&fid='.$listform['fid'].'">'.$listform['formname'].'</a></td>

			<td><a href="index.php?tab=AdminModules&configure=contactform&token='.$mytoken.'&task=editform&fid='.$listform['fid'].'">'.$listform['formtitle'].'</a></td>

			<td><a href="index.php?tab=AdminModules&configure=contactform&token='.$mytoken.'&task=editform&fid='.$listform['fid'].'">'.$listform['email'].'</a></td>

			<td><a style="font-size:10px" target="_blank" title="'.self::l('Preview form').'" href="'.__PS_BASE_URI__.'contact-form.php?fid='.$listform['fid'].'"><b>http://www.votresite.com/contact-form.php?fid='.$listform['fid'].'<br>';

			//$output.='http://www.votresite.com/'.$seolink.'</b>';

			$output.='</a></td>

			<td align="center">'.count($nbOfFields).'</td>

			<td width="15%">

							<a title="'.self::l('List fields').'" href="'.$_SERVER['REQUEST_URI'].'&task=showfieldList&fid='.$listform['fid'].'">

							<img width=20 alt="'.self::l('List field').'" src="'.$mypath.'img/listform.png">

							</a>

			

			<a title="'.self::l('Edit form').'" href="'.$_SERVER['REQUEST_URI'].'&task=editform&fid='.$listform['fid'].'">

							<img alt="'.self::l('Edit form').'" src="'.$mypath.'img/edition.png">

							</a>

							<a target="_blank" title="'.self::l('Preview form').'" href="'.__PS_BASE_URI__.'contact-form.php?fid='.$listform['fid'].'">

							<img width="20" alt="'.self::l('Preview form').'" src="'.$mypath.'img/preview.png">

							</a>

						<a title="'.self::l('Delete form').'" href="index.php?tab=AdminModules&configure=contactform&token='.$mytoken.'&task=delform&fid='.$listform['fid'].'" onclick="return(confirm(\''.self::l('Do you really want  to delete this form and its fields?').'\'));">

						<img alt="'.self::l('Delete form').'" src="'.$mypath.'img/delete.png">

						</a>

			</td>

			</tr>';

		}

	$output.='</table>';

	$output.='<input style="margin:10px;" class="button" type="submit" name="deleteselectfrm" value="'.self::l('Delete selected').'" onclick="return(confirm(\''.self::l('Do you really want  to delete forms selected and theire fields?').'\'));">';

	$output.='</form>';

	$output.='</div>';

	

	

		

	}

	

	$output.='</fieldset>';

	return $output;

} //End function showform



//

public static function editform($mypath){

	

	$output = '<link rel="stylesheet" type="text/css" href="'.$mypath.'library/tabs/tabs.css" />';

	$output  .= self::_addLayout($mypath);

	$output  .= self::_useSlider($mypath);

	$output  .= self::_useColorPicker($mypath);

	$output  .= CFtoolbar::toolbar('editform',$mypath);

	

	

	global $cookie;

	$mytoken=Tools::getValue('token');

	$fid=intval(Tools::getValue('fid'));

	$fdid=intval(Tools::getValue('fdid'));

	$task=Tools::getValue('task');



	$defaultLanguage = intval(Configuration::get('PS_LANG_DEFAULT'));

	$languages = Language::getLanguages();

	$iso = Language::getIsoById($defaultLanguage);

	//$divLangName = 'alias¤formtitle¤thankyou¤msgbeforeForm¤msgafterForm¤toname¤subject¤automailresponse¤returnurl';

	$divLangName = 'alias¤formtitle¤returnurl¤thankyou¤msgbeforeForm¤msgafterForm¤toname¤subject¤automailresponse';

	$id_lang			=	intval(Tools::getValue('id_lang'));

	$formname			=	Tools::getValue('formname','');

	$email				=	Tools::getValue('email','');

	$defaultlayout ='<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/1999/REC-html401-19991224/strict.dtd">

<html>

<head>

	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

	<title>{message_from} {shop_name}</title>

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





$customerlayout ='<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/1999/REC-html401-19991224/strict.dtd">

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

			<td align="left" style="background-color:#DB3484; color:#FFF; font-size: 12px; font-weight:bold; padding: 0.5em 1em;">{notification} {shop_name}</td>

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

	

	

	$layout				=	addslashes(Tools::getValue('layout',$defaultlayout));

	$clayout				=	addslashes(Tools::getValue('clayout',$customerlayout));



	

	









	

$output .='

	

	

	

<script type="text/javascript">

			id_language = Number('.$defaultLanguage.');

</script>	

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



		if(file_exists(_PS_ROOT_DIR_.'/js/tinymce/jscripts/tiny_mce/tiny_mce.js')){



				// TinyMCE vers 1.4.1

				//1.4.0

		$output .= ' <script type="text/javascript" src="'.__PS_BASE_URI__.'js/tinymce/jscripts/tiny_mce/tiny_mce.js"></script>

				<script type="text/javascript">

					tinyMCE.init({

						mode : "textareas",

						theme : "advanced",

						plugins : "safari,pagebreak,style,layer,table,advimage,advlink,inlinepopups,media,searchreplace,contextmenu,paste,directionality,fullscreen",

						// Theme options

						theme_advanced_buttons1 : "newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",

						theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,,|,forecolor,backcolor",

						theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,media,|,ltr,rtl,|,fullscreen",

						theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,pagebreak",

						theme_advanced_toolbar_location : "top",

						theme_advanced_toolbar_align : "left",

						theme_advanced_statusbar_location : "bottom",

						theme_advanced_resizing : false,

						content_css : "'.__PS_BASE_URI__.'themes/'._THEME_NAME_.'/css/global.css",

						document_base_url : "'.__PS_BASE_URI__.'",

						width: "600",

						height: "auto",

						font_size_style_values : "8pt, 10pt, 12pt, 14pt, 18pt, 24pt, 36pt",

						// Drop lists for link/image/media/template dialogs

						template_external_list_url : "lists/template_list.js",

						external_link_list_url : "lists/link_list.js",

						external_image_list_url : "lists/image_list.js",

						media_external_list_url : "lists/media_list.js",

						elements : "nourlconvert,ajaxfilemanager",

						file_browser_callback : "ajaxfilemanager",

						entity_encoding: "raw",

						convert_urls : false,

						language : "'.(file_exists(_PS_ROOT_DIR_.'/js/tinymce/jscripts/tiny_mce/langs/'.$iso.'.js') ? $iso : 'en').'"

						

					});

					function ajaxfilemanager(field_name, url, type, win) {

						var ajaxfilemanagerurl = "'.dirname($_SERVER["PHP_SELF"]).'/ajaxfilemanager/ajaxfilemanager.php";

						switch (type) {

							case "image":

								break;

							case "media":

								break;

							case "flash": 

								break;

							case "file":

								break;

							default:

								return false;

					}

		            tinyMCE.activeEditor.windowManager.open({

		                url: "'.dirname($_SERVER["PHP_SELF"]).'/ajaxfilemanager/ajaxfilemanager.php",

		                width: 782,

		                height: 440,

		                inline : "yes",

		                close_previous : "no"

		            },{

		                window : win,

		                input : field_name

		            });

            

		}

	</script>';

		}

		else{

	

	//Tinymce vers > 1.4.1

	// TinyMCE

		

		$iso = Language::getIsoById((int)($cookie->id_lang));

		$isoTinyMCE = (file_exists(_PS_ROOT_DIR_.'/js/tiny_mce/langs/'.$iso.'.js') ? $iso : 'en');

		$ad = dirname($_SERVER["PHP_SELF"]);

		$output .=  '

			<script type="text/javascript">	

			var iso = \''.$isoTinyMCE.'\' ;

			var pathCSS = \''._THEME_CSS_DIR_.'\' ;

			var ad = \''.$ad.'\' ;

			</script>

			<script type="text/javascript" src="'.__PS_BASE_URI__.'js/tiny_mce/tiny_mce.js"></script>

			<script type="text/javascript" src="'.__PS_BASE_URI__.'js/tinymce.inc.js"></script>';



		}

	

	



	if($fid!=0){

		

	

	$Listforms = Db::getInstance()->ExecuteS('SELECT cf.`email`,cf.`formname`, cfl.*

											 FROM `'._DB_PREFIX_.'contactform` cf 

											 LEFT JOIN `'._DB_PREFIX_.'contactform_lang` cfl  ON cf.`fid` = cfl.`fid` 

											 WHERE cfl.`id_lang`='.$cookie->id_lang.' 

											 ');

	}



$output .= '<fieldset class="pspage"> <legend class="pspage"><img src="'.$mypath.'img/listform.png" alt="" title="" />'.self::l('Edit Form').'</legend>';

$output .='

	<ul class="tabs">

    <li><a href="#tab1">'.self::l('Form settings').'</a></li>

    <li><a href="#tab2">'.self::l('Messages').'</a></li>

	<li><a href="#tab3">'.self::l('E-mail').'</a></li>

	<li><a href="#tab4">'.self::l('Layout').'</a></li>

</ul>



<div class="tab_container">

<form name="objForm" method="post" action="'.$_SERVER['REQUEST_URI'].'" enctype="multipart/form-data">

';

//###############################Tab1#############################

$output .='<div id="tab1" class="tab_content">';

	 

	 $output.='<table>';

	 if($fid>0){

	  $output .= '<tr><td>'.self::l('ID').':</td>';

	  $output .= '<td><input size="3" type="texte" name="id_fid" value="'.$fid.'" disabled></td>';

	  $output .= '</tr>';

	  }

	  

	   $output  .= '<tr><td>'.self::l('Form name').':</td><td>';

	  $output  .= '<input type="text" name="formname" size="45" value="'.($fid != 0 ? $Listforms[0]['formname'] : $formname).'" />';

	  $output  .='<sup> *</sup></td>';

	  $output  .= '</tr>';

	  

	  

	  $output  .= '<tr>';

	   $output  .= '<td>'.self::l('Alias').':</td>';

	  $output  .= '<td>';

	  foreach ($languages as $language)

				{

					$custalias=Db::getInstance()->ExecuteS('SELECT `alias` FROM `'._DB_PREFIX_.'contactform_lang` WHERE `fid`='.$fid.' AND `id_lang`='.$language['id_lang']);

					if(!empty($custalias[0]['alias']))

						$defaultalias =$custalias[0]['alias'];

					else

						$defaultalias ='';

					$output .= '

					<div id="alias_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').';float: left;">

						<input type="text" name="alias_'.$language['id_lang'].'" id="alias_'.$language['id_lang'].'" size="45" value="'.($fid != 0 ? $defaultalias : (isset($_POST['alias_'.$language['id_lang']])?$_POST['alias_'.$language['id_lang']]:''  )  ).'" />

					</div>';

				 }

				$output .= self::displayFlags($languages, $defaultLanguage, $divLangName, 'alias', true);

	  

	  $output  .= '</td>';

	  $output  .= '</tr>';

	  

	  

	   $output  .= '<tr>';

	   $output  .= '<td>'.self::l('Form title').':</td>';

	  $output  .= '<td>';

	  foreach ($languages as $language)

				{

					$custformtitle=Db::getInstance()->ExecuteS('SELECT `formtitle` FROM `'._DB_PREFIX_.'contactform_lang` WHERE `fid`='.$fid.' AND `id_lang`='.$language['id_lang']);

					if(!empty($custformtitle[0]['formtitle']))

						$defaultformtitle =$custformtitle[0]['formtitle'];

					else

						$defaultformtitle ='';

					

					

					$output .= '

					<div id="formtitle_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').';float: left;">

						<input type="text" name="formtitle_'.$language['id_lang'].'" id="formtitle_'.$language['id_lang'].'" size="45" value="'.($fid != 0 ? $defaultformtitle : (isset($_POST['formtitle_'.$language['id_lang']])?$_POST['formtitle_'.$language['id_lang']]:''  )).'" />

					</div>';

				 }

				$output .= self::displayFlags($languages, $defaultLanguage, $divLangName, 'formtitle', true);

	  

	  $output  .= '</td>';

	  $output  .= '</tr>';

	  

	  

	   $output  .= '<tr>';

	   $output  .= '<td>'.self::l('Return url').':</td>';

	  $output  .= '<td>';

	  foreach ($languages as $language)

				{

					$custreturnurl=Db::getInstance()->ExecuteS('SELECT `returnurl` FROM `'._DB_PREFIX_.'contactform_lang` WHERE `fid`='.$fid.' AND `id_lang`='.$language['id_lang']);

					if(!empty($custreturnurl[0]['returnurl']))

						$defaulturl =$custreturnurl[0]['returnurl'];

					else

						$defaulturl ='';

					

					$output .= '

					<div id="returnurl_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').';float: left;">

						<input type="text" name="returnurl_'.$language['id_lang'].'" id="returnurl_'.$language['id_lang'].'" size="45" value="'.($fid != 0 ? $defaulturl : (isset($_POST['returnurl_'.$language['id_lang']])?$_POST['returnurl_'.$language['id_lang']]:''  )).'" />

					</div>';

				 }

				$output .= self::displayFlags($languages, $defaultLanguage, $divLangName, 'returnurl', true);

	  

	  $output  .= '</td>';

	  $output  .= '</tr>';

	  

	 

	 $output.='</table>';



$output .='</div>';





//############################"End tab1#############################""""





//############################tab2#############################""""

$output .='<div id="tab2" class="tab_content">';



$output.='<b>'.self::l('Thank you message').':</b><br><hr><br>';

$output .='<div style=" margin-left:125px">';

			foreach ($languages as $language){

				$thanklang=Db::getInstance()->ExecuteS('SELECT `thankyou` FROM `'._DB_PREFIX_.'contactform_lang` WHERE `fid`='.$fid.' AND `id_lang`='.$language['id_lang']);

					if(!empty($thanklang[0]['thankyou']))

						$defaulthankyou =$thanklang[0]['thankyou'];

					else

						$defaulthankyou ='';

				$output .= '

					<div id="thankyou_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').'; float: left;">

						<textarea class="rte" cols="50" rows="10" name="thankyou_'.$language['id_lang'].'" >'.($fid != 0 ? $defaulthankyou : (isset($_POST['thankyou_'.$language['id_lang']])?$_POST['thankyou_'.$language['id_lang']]:''  )).'</textarea></div>';}

			$output .= self::displayFlags($languages, $defaultLanguage, $divLangName, 'thankyou', true);

			$output .= '

					<div class="clear"></div>

				</div>';

				

$output.='<br><b>'.self::l('Message Before the form').':</b><br><hr><br>';

$output .='<div style=" margin-left:125px">';

			foreach ($languages as $language){

				$thanklang=Db::getInstance()->ExecuteS('SELECT `msgbeforeForm` FROM `'._DB_PREFIX_.'contactform_lang` WHERE `fid`='.$fid.' AND `id_lang`='.$language['id_lang']);

					if(!empty($thanklang[0]['msgbeforeForm']))

						$defaultmsgbeforeForm =$thanklang[0]['msgbeforeForm'];

					else

						$defaultmsgbeforeForm ='';

				$output .= '

					<div id="msgbeforeForm_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').'; float: left;">

						<textarea class="rte"  cols="50" rows="10" name="msgbeforeForm_'.$language['id_lang'].'" >'.($fid != 0 ? $defaultmsgbeforeForm : (isset($_POST['msgbeforeForm_'.$language['id_lang']])?$_POST['msgbeforeForm_'.$language['id_lang']]:''  )).'</textarea></div>';}

			$output .= self::displayFlags($languages, $defaultLanguage, $divLangName, 'msgbeforeForm', true);

			$output .= '

					<div class="clear"></div>

				</div>';

				

$output.='<br><b>'.self::l('Message after the form').':</b><br><hr><br>';

$output .='<div style=" margin-left:125px">';

			foreach ($languages as $language){

				$thanklang=Db::getInstance()->ExecuteS('SELECT `msgafterForm` FROM `'._DB_PREFIX_.'contactform_lang` WHERE `fid`='.$fid.' AND `id_lang`='.$language['id_lang']);

					if(!empty($thanklang[0]['msgafterForm']))

						$defaultmsgafterForm =$thanklang[0]['msgafterForm'];

					else

						$defaultmsgafterForm ='';

				$output .= '

					<div id="msgafterForm_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').'; float: left;">

						<textarea class="rte" cols="50" rows="10" name="msgafterForm_'.$language['id_lang'].'" >'.($fid != 0 ? $defaultmsgafterForm : (isset($_POST['msgafterForm_'.$language['id_lang']])?$_POST['msgafterForm_'.$language['id_lang']]:''  )).'</textarea></div>';}

			$output .= self::displayFlags($languages, $defaultLanguage, $divLangName, 'msgafterForm', true);

			$output .= '

					<div class="clear"></div>

				</div>';







$output .='</div>';

//############################end tab2#############################





//###################################tab3########################

$output .='<div id="tab3" class="tab_content">';



$output.='<b>'.self::l('Email notification').'</b><br><hr><br>';



$output .= '<table>';

 //-------------------------------------------------------------------------------------------------------------------------------------

	  $output .= '<tr><td width="25%">'.self::l('E-mail address').':</td>

	  <td>';

	  $output .= '<input type="text" name="email" size="45" value="'.($fid != 0 ? $Listforms[0]['email'] : $email).'" /><sup> *</sup>'.self::info($mypath,'Separate emails with ";"');

	  $output .='</td>';

	  $output .= '</tr>';

	  //-------------------------------------------------------------------------------------------------------------------------------------

	   $output  .= '<tr>';

	   $output  .= '<td>'.self::l('Name of expeditor').':</td>';

	  $output  .= '<td>';

	  foreach ($languages as $language)

				{

					$custsub=Db::getInstance()->ExecuteS('SELECT `toname` FROM `'._DB_PREFIX_.'contactform_lang` WHERE `fid`='.$fid.' AND `id_lang`='.$language['id_lang']);

					if(!empty($custsub[0]['toname']))

						$defaulsub =$custsub[0]['toname'];

					else

						$defaulsub ='';

					

					$output .= '

					<div id="toname_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').';float: left;">

						<input type="text" name="toname_'.$language['id_lang'].'" id="toname_'.$language['id_lang'].'" size="45" value="'.($fid != 0 ? $defaulsub : (isset($_POST['toname_'.$language['id_lang']])?$_POST['toname_'.$language['id_lang']]:''  )).'" />

					</div>';

				 }

				$output .= self::displayFlags($languages, $defaultLanguage, $divLangName, 'toname', true);

	  

	  $output  .= '</td>';

	  $output  .= '</tr>';

	  //-------------------------------------------------------------------------------------------------------------------------------------

	  

	  $output  .= '<tr>';

	   $output  .= '<td>'.self::l('E-mail subject').':</td>';

	  $output  .= '<td>';

	  foreach ($languages as $language)

				{

					$custsub=Db::getInstance()->ExecuteS('SELECT `subject` FROM `'._DB_PREFIX_.'contactform_lang` WHERE `fid`='.$fid.' AND `id_lang`='.$language['id_lang']);

					if(!empty($custsub[0]['subject']))

						$defaulsub =$custsub[0]['subject'];

					else

						$defaulsub ='';

					

					$output .= '

					<div id="subject_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').';float: left;">

						<input type="text" name="subject_'.$language['id_lang'].'" id="subject_'.$language['id_lang'].'" size="45" value="'.($fid != 0 ? $defaulsub : (isset($_POST['subject_'.$language['id_lang']])?$_POST['subject_'.$language['id_lang']]:''  )).'" />

					</div>';

				 }

				$output .= self::displayFlags($languages, $defaultLanguage, $divLangName, 'subject', true);

	  

	  $output  .= '</td>';

	  $output  .= '</tr>';

$output .= '</table>';	



//---------------------------------------------

$output.='<b>'.self::l('Notification message').':</b><br><hr><br>';

$output .='<div style=" margin-left:125px">';

			foreach ($languages as $language){

				$thanklang=Db::getInstance()->ExecuteS('SELECT `automailresponse` FROM `'._DB_PREFIX_.'contactform_lang` WHERE `fid`='.$fid.' AND `id_lang`='.$language['id_lang']);

					if(!empty($thanklang[0]['automailresponse']))

						$default =$thanklang[0]['automailresponse'];

					else

						$default ='';

				$output .= '

					<div id="automailresponse_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').'; float: left;">

						<textarea class="rte"  cols="50" rows="10" name="automailresponse_'.$language['id_lang'].'" >'.($fid != 0 ? $default : (isset($_POST['automailresponse_'.$language['id_lang']])?$_POST['automailresponse_'.$language['id_lang']]:''  )).'</textarea></div>';}

			$output .= self::displayFlags($languages, $defaultLanguage, $divLangName, 'automailresponse', true);

			$output .= '

					<div class="clear"></div>

				</div>';





$output .='</div>';



//###################tab4#################################

$output .='<div id="tab4" class="tab_content">';

$layout=Db::getInstance()->ExecuteS('SELECT `layout` FROM `'._DB_PREFIX_.'contactform` WHERE `fid`='.$fid);

					if(!empty($layout[0]['layout']))

						$layout =$layout[0]['layout'];

					else

						$layout ='';

$output.='<div style="border:2px solid #ECEADE; padding:10px; margin:10px; background:lightyellow; color:#222222">

'.self::l('Do not remove the codes in brackets').'

</div>';



$output.='<b>'.self::l('Seller email layout').':</b><br><hr><br>';

	 $output.='<center><textarea  class="rte" cols="70" rows="30" name="layout" id="layout">'.($fid != 0 ? $layout : (isset($_POST['layout'])?addslashes($_POST['layout']):$defaultlayout  )).'</textarea></center><br>';

	 

	 

$clayout=Db::getInstance()->ExecuteS('SELECT `clayout` FROM `'._DB_PREFIX_.'contactform` WHERE `fid`='.$fid);

					if(!empty($clayout[0]['clayout']))

						$clayout =$clayout[0]['clayout'];

					else

						$clayout ='';

$output.='<b>'.self::l('Customer email layout').':</b><br><hr><br>';

	 $output.='<center><textarea  class="rte" cols="70" rows="30" name="clayout" id="clayout">'.($fid != 0 ? $clayout : (isset($_POST['clayout'])?addslashes($_POST['clayout']):$customerlayout  )).'</textarea></center><br>';	 





$output .='</div>';

//###################tab4#################################

$output .='<div align="center" style="margin:10px">

<input type="hidden" name="fid" value ="'.$fid.'">

	<input class="button" type="submit" name="submitform" value="'.self::l('    Save    ').'" >

	</div>';

$output .= '</form>';

$output .= '</div>';

$output .= '</fieldset>';



return $output;

	



}//End function edit form





public static function _newsliderline($title,$fieldname,$sliderId1,$sliderId2,$sliderId3,$default,$max,$min,$var){

	

 $output = '<tr>';

	$output .= '<td>'.self::l($title).':</td><td></td>';

	$output .= '</tr>';





$output .= '<tr>';

	$output .= '<td><div class="slider" id="'.$sliderId1.'" tabIndex="1"><input class="slider-input" id="'.$sliderId2.'"/></div></td><td><input id="'.$sliderId3.'" onchange="a.setValue(parseInt(this.value))" size="6" name="'.$fieldname.'"/>

</td>';

$output .= '</tr>';





$output .='<script type="text/javascript">



var '.$var.' = new Slider(document.getElementById("'.$sliderId1.'"), document.getElementById("'.$sliderId2.'"));

'.$var.'.onchange = function () {

	document.getElementById("'.$sliderId3.'").value = '.$var.'.getValue();

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

 

public static function _useColorPicker($mypath){

	$output ='<link rel="stylesheet" type="text/css" href="'.$mypath.'library/Color_Picker/Css/ColorPicker.css" />

	<script type="text/javascript" src="'.$mypath.'library/Color_Picker/Js/CP_Class.js"></script>

	

	

	<script type="text/javascript">

	window.onload = function()

	{

	 fctLoad();

	}

	window.onscroll = function()

	{

	 fctShow();

	}

	window.onresize = function()

	{

	 fctShow();

	}

	</script>';

	return $output;

}





public static function _newColorPicker($title,$mypath,$fieldname,$default){

$output = '<tr>';

	$output .= '<td>'.self::l($title).' : </td><td><input type="text" size="10" name="'.$fieldname.'" value="'.$default.'" maxlength="7" style="font-family:Tahoma;font-size:x-small;">

<img src="'.$mypath.'library/Color_Picker/Img/color.gif" width="21" height="20" border="0" align="absmiddle" onClick="fctShow(document.objForm.'.$fieldname.');" style="cursor:pointer;"></td>';

$output .= '</tr>';

return $output;

}



public static function _useSlider($mypath){

	$output = '<link rel="stylesheet" type="text/css" href="'.$mypath.'library/slider/css/luna.css" />';

	$output.='<script type="text/javascript" src="'.$mypath.'library/slider/js/range.js"></script>';

	$output.='<script type="text/javascript" src="'.$mypath.'library/slider/js/slider.js"></script>';

	$output.='<script type="text/javascript" src="'.$mypath.'library/slider/js/timer.js"></script>';

	return $output;

}



public static function _addLayout($mypath){

	

	return '<link rel="stylesheet" type="text/css" href="'.$mypath.'library/css/main.css" /><link rel="stylesheet" type="text/css" href="'.$mypath.'library/css/page.css" />';

} 



public static function displayFlags($languages, $defaultLanguage, $ids, $id, $return = false)

	{

		

		$version = _PS_VERSION_;

		$tabversion =explode('.',$version);

		$ver1 = intval($tabversion[0]);

		$ver2 = intval($tabversion[1]);

		

		if($ver1==1 && $ver2<3){

					if (sizeof($languages) == 1)

						return false;

					$defaultIso = Language::getIsoById($defaultLanguage);

					$output = '

					<div class="display_flags">

						<img src="../img/l/'.$defaultLanguage.'.jpg" class="pointer" id="language_current_'.$id.'" onclick="showLanguages(\''.$id.'\');" alt="" />

					</div>

					<div id="languages_'.$id.'" class="language_flags">

						'.self::l('Choose language:').'<br /><br />';

					foreach ($languages as $language)

						$output .= '<img src="../img/l/'.intval($language['id_lang']).'.jpg" class="pointer" alt="'.$language['name'].'" title="'.$language['name'].'" onclick="changeLanguage(\''.$id.'\', \''.$ids.'\', '.$language['id_lang'].', \''.$language['iso_code'].'\');" /> ';

					$output .= '</div>';

		

					if ($return)

						return $output;

					echo $output;

					

		}

		else{

				if (sizeof($languages) == 1)

					return false;

				$output = '

				<div class="displayed_flag">

					<img src="../img/l/'.$defaultLanguage.'.jpg" class="pointer" id="language_current_'.$id.'" onclick="toggleLanguageFlags(this);" alt="" />

				</div>

				<div id="languages_'.$id.'" class="language_flags">

					'.self::l('Choose language:').'<br /><br />';

				foreach ($languages as $language)

					$output .= '<img src="../img/l/'.(int)($language['id_lang']).'.jpg" class="pointer" alt="'.$language['name'].'" title="'.$language['name'].'" onclick="changeLanguage(\''.$id.'\', \''.$ids.'\', '.$language['id_lang'].', \''.$language['iso_code'].'\');" /> ';

				$output .= '</div>';

		

				if ($return)

					return $output;

				echo $output;

		}//End else

	}

	

	public function displayFlags2($languages, $defaultLanguage, $ids, $id, $return = false)

	{

			if (sizeof($languages) == 1)

				return false;

			$defaultIso = Language::getIsoById($defaultLanguage);

			$output = '

			<div class="display_flags">

				<img src="../img/l/'.$defaultLanguage.'.jpg" class="pointer" id="language_current_'.$id.'" onclick="showLanguages(\''.$id.'\');" alt="" />

			</div>

			<div id="languages_'.$id.'" class="language_flags">

				'.self::l('Choose language:').'<br /><br />';

			foreach ($languages as $language)

				$output .= '<img src="../img/l/'.intval($language['id_lang']).'.jpg" class="pointer" alt="'.$language['name'].'" title="'.$language['name'].'" onclick="changeLanguage(\''.$id.'\', \''.$ids.'\', '.$language['id_lang'].', \''.$language['iso_code'].'\');" /> ';

			$output .= '</div>';



			if ($return)

				return $output;

			echo $output;

	}

	

	

	

public static function info($mypath,$info){

	$output = '<link rel="stylesheet" type="text/css" href="'.$mypath.'library/info/vtip.css" />';

	$output.='<script type="text/javascript" src="'.$mypath.'library/info/vtip.js"></script>';

	

	$output.='<img src="'.$mypath.'library/info/info.png" title="'.self::l($info).'" class="vtip" />';	

	return $output;



}





public static function updateForm($type=0){

	//Retrieve all parametters

		$fid			 =	intval(Tools::getValue('fid'));

		$defaultLanguage = intval(Configuration::get('PS_LANG_DEFAULT'));

		$languages = Language::getLanguages();

		$mytoken = Tools::getValue('mytoken');

		$task = Tools::getValue('task');

		

		//Same

		$formname			=	addslashes(Tools::getValue('formname',''));

		$email				=	addslashes(Tools::getValue('email',''));

		$layout				=	addslashes(Tools::getValue('layout',''));

		$clayout			=	addslashes(Tools::getValue('clayout',''));

		

	

	

	 switch($type){

		case 0:

		//Insert

		

		//Check if form is already exists

		

		

		

		//Insert Now insert

		Db::getInstance()->AutoExecute(_DB_PREFIX_.'contactform', array('fid' => '', 'formname' => $formname, 'email' => $email,'mailtype' => 0,'layout'=>$layout,'clayout'=>$clayout), 'INSERT');

		$mylastid=mysql_insert_id();

		//Fulfill the lang table

		if (!$languages)

			return false;

		

		foreach ($languages as $language){

			

			//Test allvalue

			if (empty($_POST['alias_'.$language['id_lang']]))

					$alias =  addslashes(Tools::getValue('alias_'.$defaultLanguage));

			else

					$alias =  addslashes(Tools::getValue('alias_'.$language['id_lang']));

					

			if (empty($_POST['formtitle_'.$language['id_lang']]))

					$formtitle =  addslashes(Tools::getValue('formtitle_'.$defaultLanguage));

			else

					$formtitle =  addslashes(Tools::getValue('formtitle_'.$language['id_lang']));

					

			if (empty($_POST['returnurl_'.$language['id_lang']]))

					$returnurl =  addslashes(Tools::getValue('returnurl_'.$defaultLanguage));

			else

					$returnurl =  addslashes(Tools::getValue('returnurl_'.$language['id_lang']));

					

			if (empty($_POST['thankyou_'.$language['id_lang']]))

					$thankyou =  addslashes(Tools::getValue('thankyou_'.$defaultLanguage));

			else

					$thankyou =  addslashes(Tools::getValue('thankyou_'.$language['id_lang']));

					

			if (empty($_POST['toname_'.$language['id_lang']]))

					$toname =  addslashes(Tools::getValue('toname_'.$defaultLanguage));

			else

					$toname =  addslashes(Tools::getValue('toname_'.$language['id_lang']));		

					

			if (empty($_POST['subject_'.$language['id_lang']]))

					$subject =  addslashes(Tools::getValue('subject_'.$defaultLanguage));

			else

					$subject =  addslashes(Tools::getValue('subject_'.$language['id_lang']));		

			

			if (empty($_POST['automailresponse_'.$language['id_lang']]))

					$automailresponse =  addslashes(Tools::getValue('automailresponse_'.$defaultLanguage));

			else

					$automailresponse =  addslashes(Tools::getValue('automailresponse_'.$language['id_lang']));	

					

			$alias	= str_replace(' ','-',$alias);				

				

			

		Db::getInstance()->AutoExecute(_DB_PREFIX_.'contactform_lang', array(

			'id_lang' => intval($language['id_lang']), 

			'fid' => $mylastid,

			'alias' => $alias,

			'formtitle' => $formtitle,

			'thankyou' => $thankyou,

			'msgbeforeForm' => addslashes(Tools::getValue('msgbeforeForm_'.$language['id_lang'])),

			'msgafterForm' => addslashes(Tools::getValue('msgafterForm_'.$language['id_lang'])),

			'toname' => $toname,

			'subject' => $subject,

			'automailresponse' => $automailresponse,

			'returnurl' => $returnurl

			), 'INSERT');

		

		}

			

		break;

		case 1:

			//Update 

			@Db::getInstance()->ExecuteS('UPDATE `'._DB_PREFIX_.'contactform` SET `formname` = "'.$formname.'",`email` = "'.$email.'",`mailtype` = 0,`layout` = "'.$layout.'",`clayout` = "'.$clayout.'"  WHERE `fid` ='.$fid);

			

			/*Multilanguage */

			

			foreach ($languages as $language){

			

			//Test allvalue

			if (empty($_POST['alias_'.$language['id_lang']]))

					$alias =  addslashes(Tools::getValue('alias_'.$defaultLanguage));

			else

					$alias =  addslashes(Tools::getValue('alias_'.$language['id_lang']));

			

			$alias	= str_replace(' ','-',$alias);

			

			if (empty($_POST['formtitle_'.$language['id_lang']]))

					$formtitle =  addslashes(Tools::getValue('formtitle_'.$defaultLanguage));

			else

					$formtitle =  addslashes(Tools::getValue('formtitle_'.$language['id_lang']));

					

			if (empty($_POST['returnurl_'.$language['id_lang']]))

					$returnurl =  addslashes(Tools::getValue('returnurl_'.$defaultLanguage));

			else

					$returnurl =  addslashes(Tools::getValue('returnurl_'.$language['id_lang']));

					

			if (empty($_POST['thankyou_'.$language['id_lang']]))

					$thankyou =  addslashes(Tools::getValue('thankyou_'.$defaultLanguage));

			else

					$thankyou =  addslashes(Tools::getValue('thankyou_'.$language['id_lang']));

					

			if (empty($_POST['toname_'.$language['id_lang']]))

					$toname =  addslashes(Tools::getValue('toname_'.$defaultLanguage));

			else

					$toname =  addslashes(Tools::getValue('toname_'.$language['id_lang']));		

					

			if (empty($_POST['subject_'.$language['id_lang']]))

					$subject =  addslashes(Tools::getValue('subject_'.$defaultLanguage));

			else

					$subject =  addslashes(Tools::getValue('subject_'.$language['id_lang']));		

			

			if (empty($_POST['automailresponse_'.$language['id_lang']]))

					$automailresponse =  addslashes(Tools::getValue('automailresponse_'.$defaultLanguage));

			else

					$automailresponse =  addslashes(Tools::getValue('automailresponse_'.$language['id_lang']));	

			

			$msgbeforeForm =  addslashes(Tools::getValue('msgbeforeForm_'.$language['id_lang']));

			$msgafterForm =  addslashes(Tools::getValue('msgafterForm_'.$language['id_lang']));

				

		

		@Db::getInstance()->ExecuteS('UPDATE `'._DB_PREFIX_.'contactform_lang` SET `alias` = "'.$alias.'",`formtitle` = "'.$formtitle.'",`thankyou` = "'.$thankyou.'",`msgbeforeForm` = "'.$msgbeforeForm.'",`msgafterForm` = "'.$msgafterForm.'",`toname` = "'.$toname.'",`subject` = "'.$subject.'",`automailresponse` = "'.$automailresponse.'",`returnurl` = "'.$returnurl.'"  WHERE `fid` ='.$fid.' AND `id_lang` ='.$language['id_lang']);

		

		}

			

		break;

	}

	

	

}//End function updateForm($type)









public static function updateField($type=0){

	//Retrieve all parametters

		$fid			 =	intval(Tools::getValue('fid'));

		$fdid			 =	intval(Tools::getValue('fid'));

		$defaultLanguage = intval(Configuration::get('PS_LANG_DEFAULT'));

		$languages = Language::getLanguages();

		$mytoken = Tools::getValue('mytoken');

		$task = Tools::getValue('task');

		

		//SAME 

			$fid				=	intval(Tools::getValue('fid'));

			$fdid				=	intval(Tools::getValue('fdid'));

			$fields_type		=	Tools::getValue('fields_type','');

			$fields_id			=	addslashes(Tools::getValue('fields_id',''));

			$fields_name		=	addslashes(Tools::getValue('fields_name',''));

			$fields_require		=	intval(Tools::getValue('fields_require',0));

			$confirmation		=	intval(Tools::getValue('confirmation',0));

			$fields_valid		=	Tools::getValue('fields_valid','none');

			$fields_default		=	addslashes(Tools::getValue('fields_default',''));

			$fields_suppl		=	addslashes(Tools::getValue('fields_suppl',''));

			$order				=	intval(Tools::getValue('order',0));

			$published			=	intval(Tools::getValue('published',1));

			

			

			$fields_id		= str_replace(' ','_',$fields_id);

			$fields_name	= str_replace(' ','_',$fields_name);

	

	

	 switch($type){

		case 0:

		//Insert

		

		//Check if form is already exists

		

		

		

		//Insert Now insert

		Db::getInstance()->AutoExecute(_DB_PREFIX_.'contactform_item', array(

				'fdid' => '',

				'fid' => $fid,

				'fields_id' => $fields_id, 

				'fields_name' => $fields_name,

				'confirmation' => $confirmation,

				'fields_valid' => $fields_valid, 

				'fields_type' => $fields_type, 

				'fields_style' => '', 

				'err_style' => '',

				'fields_suppl' => $fields_suppl,

				'fields_require' => $fields_require,

				'order' => $order,

				'published' => $published), 'INSERT');

		

		$mylastid=mysql_insert_id();

		//Fulfill the lang table

		if (!$languages)

			return false;

		

		foreach ($languages as $language){

			

			//Test allvalue

			if (empty($_POST['fields_title_'.$language['id_lang']]))

					$fields_title =  addslashes(Tools::getValue('fields_title_'.$defaultLanguage));

			else

					$fields_title =  addslashes(Tools::getValue('fields_title_'.$language['id_lang']));

					

			if (empty($_POST['fields_desc_'.$language['id_lang']]))

					$fields_desc =  addslashes(Tools::getValue('fields_desc_'.$defaultLanguage));

			else

					$fields_desc =  addslashes(Tools::getValue('fields_desc_'.$language['id_lang']));

					

			if (empty($_POST['confirmation_txt_'.$language['id_lang']]))

					$confirmation_txt =  addslashes(Tools::getValue('confirmation_txt_'.$defaultLanguage));

			else

					$confirmation_txt =  addslashes(Tools::getValue('confirmation_txt_'.$language['id_lang']));

					

			if (empty($_POST['fields_default_'.$language['id_lang']]))

					$fields_default =  addslashes(Tools::getValue('fields_default_'.$defaultLanguage));

			else

					$fields_default =  addslashes(Tools::getValue('fields_default_'.$language['id_lang']));

					

			if (empty($_POST['error_txt_'.$language['id_lang']]))

					$error_txt =  addslashes(Tools::getValue('error_txt_'.$defaultLanguage));

			else

					$error_txt =  addslashes(Tools::getValue('error_txt_'.$language['id_lang']));

					

			if (empty($_POST['error_txt2_'.$language['id_lang']]))

					$error_txt2 =  addslashes(Tools::getValue('error_txt2_'.$defaultLanguage));

			else

					$error_txt2 =  addslashes(Tools::getValue('error_txt2_'.$language['id_lang']));			

					

				

			

		@Db::getInstance()->AutoExecute(_DB_PREFIX_.'contactform_item_lang', array(

			'fdid' => $mylastid,

			'id_lang' => intval($language['id_lang']), 

			'fields_title' => $fields_title,

			'fields_desc' => $fields_desc,

			'confirmation_txt' => $confirmation_txt,

			'fields_default' => $fields_default,

			'error_txt' => $error_txt,

			'error_txt2' => $error_txt2

			), 'INSERT');

		

		}

			

		break;

		case 1:

			//Update 

			

			$fid				=	intval(Tools::getValue('fid'));

			$fdid				=	intval(Tools::getValue('fdid'));

			$fields_type		=	Tools::getValue('fields_type','');

			$fields_id			=	addslashes(Tools::getValue('fields_id',''));

			$fields_name		=	addslashes(Tools::getValue('fields_name',''));

			$fields_require		=	intval(Tools::getValue('fields_require',0));

			$confirmation		=	intval(Tools::getValue('confirmation',0));

			$fields_valid		=	Tools::getValue('fields_valid','none');

			$fields_default		=	addslashes(Tools::getValue('fields_default',''));

			$fields_suppl		=	addslashes(Tools::getValue('fields_suppl',''));

			$order				=	intval(Tools::getValue('order',0));

			$published			=	intval(Tools::getValue('published',1));

			

			

			@Db::getInstance()->ExecuteS('UPDATE `'._DB_PREFIX_.'contactform_item` SET `fields_id` = "'.$fields_id .'",`fields_name` = "'.$fields_name.'",`confirmation` = '.$confirmation.',`fields_valid` = "'.$fields_valid.'",`fields_type` = "'.$fields_type .'",`fields_suppl` = "'.$fields_suppl .'" ,`fields_require` = '.$fields_require.',`order` = '.$order.' ,`published` = '.$published.' WHERE `fdid` ='.$fdid);

			

			/*Multilanguage */

			

			foreach ($languages as $language){

			

			//Test allvalue

			if (empty($_POST['fields_title_'.$language['id_lang']]))

					$fields_title =  addslashes(Tools::getValue('fields_title_'.$defaultLanguage));

			else

					$fields_title =  addslashes(Tools::getValue('fields_title_'.$language['id_lang']));

			

			if (empty($_POST['fields_desc_'.$language['id_lang']]))

					$fields_desc =  addslashes(Tools::getValue('fields_desc_'.$defaultLanguage));

			else

					$fields_desc =  addslashes(Tools::getValue('fields_desc_'.$language['id_lang']));

					

			if (empty($_POST['confirmation_txt_'.$language['id_lang']]))

					$confirmation_txt =  addslashes(Tools::getValue('confirmation_txt_'.$defaultLanguage));

			else

					$confirmation_txt =  addslashes(Tools::getValue('confirmation_txt_'.$language['id_lang']));

					

			if (empty($_POST['fields_default_'.$language['id_lang']]))

					$fields_default =  addslashes(Tools::getValue('fields_default_'.$defaultLanguage));

			else

					$fields_default =  addslashes(Tools::getValue('fields_default_'.$language['id_lang']));

					

			if (empty($_POST['error_txt_'.$language['id_lang']]))

					$error_txt =  addslashes(Tools::getValue('error_txt_'.$defaultLanguage));

			else

					$error_txt =  addslashes(Tools::getValue('error_txt_'.$language['id_lang']));

					

			if (empty($_POST['error_txt2_'.$language['id_lang']]))

					$error_txt2 =  addslashes(Tools::getValue('error_txt2_'.$defaultLanguage));

			else

					$error_txt2 =  addslashes(Tools::getValue('error_txt2_'.$language['id_lang']));

					



				

		

		@Db::getInstance()->ExecuteS('UPDATE `'._DB_PREFIX_.'contactform_item_lang` SET `fields_title` = "'.$fields_title.'",`fields_desc` = "'.$fields_desc.'",`confirmation_txt` = "'.$confirmation_txt.'",`fields_default` = "'.$fields_default.'",`error_txt` = "'.$error_txt.'",`error_txt2` = "'.$error_txt2.'"  WHERE `fdid` ='.$fdid.' AND `id_lang` ='.$language['id_lang']);

		

		}

			

		break;

	}

	

	

}//End function updateForm($type)





public static function _verifMail($address){

		$Syntaxe='#^[\w.-]+@[\w.-]+\.[a-zA-Z]{2,6}$#';

   		if(preg_match($Syntaxe,$address))

   			{return true;}

   		else

   		{	return false;}

	}

	

public static function addfield($mypath,$fid){

	if(empty($fid))

		$fid = intval(Tools::getValue('fid'));

	if($fid == 0 || empty($fid)|| $fid==''){

		return self::_errFormat('Create first a form and then you can add new fields in that form').' '.'<a href="javascript:history.back()"><img title="'.self::l('Back').'" src="'.$mypath.'img/previous1.png"></a>';

	}

	else{

		return self::showFieldForm($mypath,$fid);	

	}

	

}





public static function showFieldForm($mypath,$fid){

	

	global $cookie;

	$mytoken=Tools::getValue('token');

	$fid=intval(Tools::getValue('fid'));

	$fdid=intval(Tools::getValue('fdid'));

	$task=Tools::getValue('task');

	

	$fields_id		=	Tools::getValue('fields_id','');

	$fields_name	=	Tools::getValue('fields_name','');

	$fields_type	=	Tools::getValue('fields_type','');

	$fields_valid	=	Tools::getValue('fields_type','');

	$fields_default	=	Tools::getValue('fields_default','');

	$fields_suppl	=	Tools::getValue('fields_suppl','');

	$confirmation	=	intval(Tools::getValue('confirmation',0));

	$fields_require	=	intval(Tools::getValue('fields_require',0));

	$published		=	intval(Tools::getValue('published',1));

	



	$defaultLanguage = intval(Configuration::get('PS_LANG_DEFAULT'));

	$languages = Language::getLanguages();

	$iso = Language::getIsoById($defaultLanguage);

	//$divLangName = 'fields_title¤fields_desc¤confirmation_txt¤error_txt¤fields_default¤error_txt2';

	$divLangName = 'fields_title¤fields_desc¤confirmation_txt¤error_txt¤fields_default';

//Retrieve all filelds for 

$Fields = Db::getInstance()->ExecuteS('SELECT *  FROM `'._DB_PREFIX_.'contactform_item`	 WHERE `fdid`='.$fdid);



$output ='';



$output .='<link rel="stylesheet" type="text/css" href="'.$mypath .'library/validform/css/errors.css" media="all"/>';

$output .='<script type="text/javascript" src="'.$mypath .'library/validform/js/jquery-1.4.2.min.js"></script>';

$output .='<script type="text/javascript" src="'.$mypath .'library/validform/js/jquery.form-validation-and-hints.js"></script>';

$output  .= CFtoolbar::toolbar('editform',$mypath);





$output .='<fieldset><legend><img src="'.$mypath.'img/edition.png" alt="" title="" />'.self::l('New field').'</legend>';

	

$output  .= '

<script language="JavaScript" type="text/JavaScript">



function supinput()

{

  var c = document.getElementById("ttype");

  var a = document.getElementById("dest" );

  var b = document.getElementById("dest2" );

  var m = document.getElementById("aff" );

	if(c.value=="captcha"){

		    a.style.display = "block";

			b.style.display = "block";

    		m.style.display = "none";

	}

	else if(a.value=="enCours"){

		bloccalendar.style.display = "block";

		bloccaptcha.style.display = "none";

	}

	else{

		a.style.display = "none";

		b.style.display = "none";

    	m.style.display = "none";

	}

}

</script>

';







$output .='	

<script type="text/javascript">

			id_language = Number('.$defaultLanguage.');

</script>



<form action="'.$_SERVER['REQUEST_URI'].'" method="post">

<table>



<tr>

<td>'.self::l('Form Id').' :</td>

<td><input type="text" name="fid" value="'.$fid.'" size="3" disabled></td>

</tr>







<tr>

<td valign="top">'.self::l('Fields Type').' :</td>

<td valign="top">

<select onChange="supinput()" required="true"  name="fields_type" id="ttype" >';

	$output .='<option value="text" '.($fdid != 0 ? ($Fields[0]['fields_type']=='text'?'selected':'' ): '').'>'.self::l('text').'</option>';

	$output .='<option value="password" '.($fdid != 0 ? ($Fields[0]['fields_type']=='password'?'selected':'' ): '').'>'.self::l('password').'</option>';

	$output .='<option value="email" '.($fdid != 0 ? ($Fields[0]['fields_type']=='email'?'selected':'' ): '').'>'.self::l('email').'</option>';

	$output .='<option value="radio" '.($fdid != 0 ? ($Fields[0]['fields_type']=='radio'?'selected':'' ): '').'>'.self::l('radio').'</option>';

	$output .='<option value="checkbox" '.($fdid != 0 ? ($Fields[0]['fields_type']=='checkbox'?'selected':'' ): '').'>'.self::l('checkbox').'</option>';

	$output .='<option value="calendar" '.($fdid != 0 ? ($Fields[0]['fields_type']=='calendar'?'selected':'' ): '').'>'.self::l('calendar').'</option>';

	$output .='<option value="textarea" '.($fdid != 0 ? ($Fields[0]['fields_type']=='textarea'?'selected':'' ): '').'>'.self::l('textarea').'</option>';

	$output .='<option value="select" '.($fdid != 0 ? ($Fields[0]['fields_type']=='select'?'selected':'' ): '').'>'.self::l('select').'</option>';

	$output .='<option value="button" '.($fdid != 0 ? ($Fields[0]['fields_type']=='button'?'selected':'' ): '').'>'.self::l('button').'</option>';

	$output .='<option value="imagebtn" '.($fdid != 0 ? ($Fields[0]['fields_type']=='imagebtn'?'selected':'' ): '').'>'.self::l('image button').'</option>';

	$output .='<option value="submitbtn" '.($fdid != 0 ?($Fields[0]['fields_type']=='submitbtn'?'selected':'' ): '').'>'.self::l('submit button').'</option>';

	$output .='<option value="resetbtn" '.($fdid != 0 ? ($Fields[0]['fields_type']=='resetbtn'?'selected':'' ): '').'>'.self::l('reset button').'</option>';

	$output .='<option value="fileup" '.($fdid != 0 ? ($Fields[0]['fields_type']=='fileup'?'selected':'' ): '').'>'.self::l('file upload').'</option>';

	$output .='<option value="captcha" '.($fdid != 0 ? ($Fields[0]['fields_type']=='captcha'?'selected':'' ): '').'>'.self::l('captcha').'</option>';

	$output .='<option value="separator" '.($fdid != 0 ? ($Fields[0]['fields_type']=='separator'?'selected':'' ): '').'>'.self::l('separator').'</option>';

	$output .='<option value="country" '.($fdid != 0 ? ($Fields[0]['fields_type']=='country'?'selected':'' ): '').'>'.self::l('country').'</option>';







$output .='</select>';



/*$output .='</br>

<div id="bloccaptcha" style="display:'.($fdid != 0 ? ($Fields[0]['fields_type']=='captcha'?'block':'none' ): 'none').'">

'.self::l('Text displayed if the code does not match').' :<br>';

foreach ($languages as $language)

				{

					$custalias=Db::getInstance()->ExecuteS('SELECT `error_txt2` FROM `'._DB_PREFIX_.'contactform_item_lang` WHERE `fdid`='.$fdid.' AND `id_lang`='.$language['id_lang']);

					if(!empty($custalias[0]['error_txt2']))

						$defaultfields_title =$custalias[0]['error_txt2'];

					else

						$defaultfields_title ='';

					$output .= '

					<div id="error_txt2_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').';float: left;">

						<input type="text" name="error_txt2_'.$language['id_lang'].'" id="error_txt2_'.$language['id_lang'].'" size="45" value="'.($fdid != 0 ? $defaultfields_title : (isset($_POST['error_txt2_'.$language['id_lang']])?$_POST['error_txt2_'.$language['id_lang']]:''  )).'" />

					</div>';

				 }

				$output .= self::displayFlags($languages, $defaultLanguage, $divLangName, 'error_txt2', true);







$output .='</div>

<div id="bloccalendar" style="display:'.($fdid != 0 ? ($Fields[0]['fields_type']=='calendar'?'block':'none' ): 'none').'">

Test

</div>

';

*/



$output .='</td>

</tr>



<tr>

<td>'.self::l('Field Id').' :</td>

<td><div class="field required" style=" background:none; border:none">

<input type="text" class="text verifyText" name="fields_id"  value="'.($fdid != 0 ? $Fields[0]['fields_id'] : $fields_id).'" >&nbsp;<sup>*</sup>

<span class="iferror">'.self::l('Field required').'</span>

</div></td>

</tr>







<tr>

<td>'.self::l('Field Name').' :</td>

<td><div class="field required" style=" background:none; border:none">

<input type="text" class="text verifyText" name="fields_name"  value="'.($fdid != 0 ? $Fields[0]['fields_name'] : $fields_name).'" >&nbsp;<sup>*</sup>

<span class="iferror">'.self::l('Field required').'</span>

</div></td>

</tr>';



$output  .= '<tr>';

	   $output  .= '<td>'.self::l('Field Title').':</td>';

	  $output  .= '<td>';

	  foreach ($languages as $language)

				{

					$custalias=Db::getInstance()->ExecuteS('SELECT `fields_title` FROM `'._DB_PREFIX_.'contactform_item_lang` WHERE `fdid`='.$fdid.' AND `id_lang`='.$language['id_lang']);

					if(!empty($custalias[0]['fields_title']))

						$defaultfields_title =$custalias[0]['fields_title'];

					else

						$defaultfields_title ='';

					$output .= '

					<div id="fields_title_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').';float: left;">

						<input type="text" name="fields_title_'.$language['id_lang'].'" id="fields_title_'.$language['id_lang'].'" size="45" value="'.($fdid != 0 ? $defaultfields_title : (isset($_POST['fields_title_'.$language['id_lang']])?$_POST['fields_title_'.$language['id_lang']]:''  )).'" />

					</div>';

				 }

				$output .= self::displayFlags($languages, $defaultLanguage, $divLangName, 'fields_title', true);

	  

	  $output  .= '</td>';

	  $output  .= '</tr>';

	  



$output  .= '<tr>';

$output.='<td valign="top">'.self::l('Description').' :</td>';

$output .='<td>';

			foreach ($languages as $language){

				$fields_desclang=Db::getInstance()->ExecuteS('SELECT `fields_desc` FROM `'._DB_PREFIX_.'contactform_item_lang` WHERE `fdid`='.$fdid.' AND `id_lang`='.$language['id_lang']);

					if(!empty($fields_desclang[0]['fields_desc']))

						$defaulfields_desc =$fields_desclang[0]['fields_desc'];

					else

						$defaulfields_desc ='';

				$output .= '

					<div id="fields_desc_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').'; float: left;">

						<textarea  cols="50" rows="5" name="fields_desc_'.$language['id_lang'].'" >'.($fdid != 0 ? $defaulfields_desc : (isset($_POST['fields_desc_'.$language['id_lang']])?$_POST['fields_desc_'.$language['id_lang']]:''  )).'</textarea></div>';}

			$output .= self::displayFlags($languages, $defaultLanguage, $divLangName, 'fields_desc', true);

$output .='</td>';

$output .='</tr>';	





$output  .= '<tr>

<td>'.self::l('Mandatory').' :</td>

<td><div class="field required" style=" background:none; border:none">

<input  type="radio" name="fields_require" value="1" '.($fdid != 0 ? ($Fields[0]['fields_require']==1?'checked':'' ): ($fields_require==1?'checked':'')).'>'.self::l('Yes').'

<input  type="radio" name="fields_require" value="0" '.($fdid != 0 ? ($Fields[0]['fields_require']==0?'checked':'' ): ($fields_require==0?'checked':'')).'>'.self::l('No').'

</div></td>

</tr>';



//Javascript functions

$output  .= '

<script language="JavaScript" type="text/JavaScript">



function afficher()

{

  var a = document.getElementById("dest" );

  var b = document.getElementById("dest2" );

  var m = document.getElementById("aff" );

    a.style.display = "block";

	b.style.display = "block";

    m.style.display = "none";

}

 

function masquer() 

{ 

  var a = document.getElementById("dest" ); 

  var b = document.getElementById("dest2" );

  var m = document.getElementById("aff" ); 

 a.style.display = "none"; 

 b.style.display = "none"; 

 m.style.display = "block"; 

}



function initial() 

{ 

  var a = document.getElementById("dest" ); 

  var b = document.getElementById("dest2" ); 

  var m = document.getElementById("aff" ); 

 a.style.display = "none"; 

 b.style.display = "none";

 m.style.display = "none"; 

}



</script>';



$output  .= '<tr>

<td valign="top">'.self::l('Confirmation').' :</td>

<td>

<div class="field required" style=" background:none; border:none">

<input OnClick="javascript:afficher();" type="radio" name="confirmation" value="1" '.($fdid != 0 ? ($Fields[0]['confirmation']==1?'checked':'' ): ($confirmation==1?'checked':'')).'>'.self::l('Yes').'

<input OnClick="javascript:masquer();" type="radio" name="confirmation" value="0" '.($fdid != 0 ? ($Fields[0]['confirmation']==0?'checked':'' ): ($confirmation==0?'checked':'')).'>'.self::l('No').'

</div>

<div id="dest" style="display:'.($fdid != 0 ? ($Fields[0]['confirmation']==1?'block':'none' ): ($confirmation==1?'block':'none')).';"><br>

'.self::l('Confirmation title').' :<br>';



foreach ($languages as $language)

				{

					$custalias=Db::getInstance()->ExecuteS('SELECT `confirmation_txt` FROM `'._DB_PREFIX_.'contactform_item_lang` WHERE `fdid`='.$fdid.' AND `id_lang`='.$language['id_lang']);

					if(!empty($custalias[0]['confirmation_txt']))

						$defaultfields_title =$custalias[0]['confirmation_txt'];

					else

						$defaultfields_title ='';

					$output .= '

					<div id="confirmation_txt_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').';float: left;">

						<input type="text" name="confirmation_txt_'.$language['id_lang'].'" id="confirmation_txt_'.$language['id_lang'].'" size="45" value="'.($fdid != 0 ? $defaultfields_title : (isset($_POST['confirmation_txt_'.$language['id_lang']])?$_POST['confirmation_txt_'.$language['id_lang']]:''  )).'" />

					</div>';

				 }

				$output .= self::displayFlags($languages, $defaultLanguage, $divLangName, 'confirmation_txt', true);

$output  .= '</div>';





/*$output .='<br><div id="dest2" style="display:'.($fdid != 0 ? ($Fields[0]['confirmation']==1?'block':'none' ): ($confirmation==1?'block':'none')).';"><br>

'.self::l('Errors if value not equal').' :<br>';



foreach ($languages as $language)

				{

					$custalias=Db::getInstance()->ExecuteS('SELECT `error_txt2` FROM `'._DB_PREFIX_.'contactform_item_lang` WHERE `fdid`='.$fdid.' AND `id_lang`='.$language['id_lang']);

					if(!empty($custalias[0]['error_txt2']))

						$defaultfields_title =$custalias[0]['error_txt2'];

					else

						$defaultfields_title ='';

					$output .= '

					<div id="error_txt2_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').';float: left;">

						<input type="text" name="error_txt2_'.$language['id_lang'].'" id="error_txt2_'.$language['id_lang'].'" size="45" value="'.($fdid != 0 ? $defaultfields_title : (isset($_POST['error_txt2_'.$language['id_lang']])?$_POST['error_txt2_'.$language['id_lang']]:''  )).'" />

					</div>';

				 }

				$output .= self::displayFlags($languages, $defaultLanguage, $divLangName, 'error_txt2', true);

$output  .= '</div>';*/









$output  .='<div id="aff">

</div>

</td>

</tr>';



$output  .= '<tr>

<td>'.self::l('Validation').' :</td>

<td>

<select required="true"  name="fields_valid" >';

	$output .='<option value="none" '.($fdid != 0 ? ($Fields[0]['fields_valid']=='none'?'selected':'' ): '').'>'.self::l('None').'</option>';

	$output .='<option value="email" '.($fdid != 0 ? ($Fields[0]['fields_valid']=='email'?'selected':'' ): '').'>'.self::l('email').'</option>';

	$output .='<option value="numeric" '.($fdid != 0 ? ($Fields[0]['fields_valid']=='numeric'?'selected':'' ): '').'>'.self::l('Numeric (0-9)').'</option>';

	$output .='<option value="alphanum" '.($fdid != 0 ? ($Fields[0]['fields_valid']=='alphanum'?'selected':'' ): '').'>'.self::l('alphanumeric').'</option>';

	$output .='<option value="alpha" '.($fdid != 0 ? ($Fields[0]['fields_valid']=='alpha'?'selected':'' ): '').'>'.self::l('alpha(a-z,A-Z)').'</option>';

	$output .='<option value="url" '.($fdid != 0 ? ($Fields[0]['fields_valid']=='url'?'selected':'' ): '').'>'.self::l('url').'</option>';

$output .='</select></td>

</tr>';





	$output .='<tr>';

	$output .='<td valign="top">'.self::l('Default value').':</td>';

	$output .='<td>';

	 foreach ($languages as $language)

				{

					$custalias=Db::getInstance()->ExecuteS('SELECT `fields_default` FROM `'._DB_PREFIX_.'contactform_item_lang` WHERE `fdid`='.$fdid.' AND `id_lang`='.$language['id_lang']);

					if(!empty($custalias[0]['fields_default']))

						$defaultfields_default =$custalias[0]['fields_default'];

					else

						$defaultfields_default ='';

	$output .='<div id="fields_default_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').';float: left;">

	

	<textarea name="fields_default_'.$language['id_lang'].'" id="fields_default_'.$language['id_lang'].'" cols="50" rows="5" >'.($fdid != 0 ? $defaultfields_default : (isset($_POST['fields_default_'.$language['id_lang']])?$_POST['fields_default_'.$language['id_lang']]:''  )).'</textarea>

	</div>

	';

				}

	

	$output .= self::displayFlags($languages, $defaultLanguage, $divLangName, 'fields_default', true);

	

	$output .=self::info($mypath,'Separate items with ";" (semicolon) for radio button,checkbox and input select').'</td>';

	$output .='</tr>';

	

	$output .='<tr>';

	$output .='<td>'.self::l('Additional Attributes').':</td>';

	$output .='<td><input type="text" name="fields_suppl" size="45" value="'.($fdid != 0 ? htmlentities($Fields[0]['fields_suppl']) : htmlentities($fields_suppl)).'">

	'.self::info($mypath,'You can add others parameters to the field. Example : size="10" or style="width:20px; color:#336699;"').'

	</td>';

	$output .='</tr>';

	

	$output  .= '<tr>';

	   $output  .= '<td>'.self::l('Error message').':</td>';

	  $output  .= '<td>';

	  foreach ($languages as $language)

				{

					$custalias=Db::getInstance()->ExecuteS('SELECT `error_txt` FROM `'._DB_PREFIX_.'contactform_item_lang` WHERE `fdid`='.$fdid.' AND `id_lang`='.$language['id_lang']);

					if(!empty($custalias[0]['error_txt']))

						$defaulterror_txt =$custalias[0]['error_txt'];

					else

						$defaulterror_txt ='';

					$output .= '

					<div id="error_txt_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').';float: left;">

						<input type="text" name="error_txt_'.$language['id_lang'].'" id="error_txt_'.$language['id_lang'].'" size="45" value="'.($fdid != 0 ? $defaulterror_txt : (isset($_POST['error_txt_'.$language['id_lang']])?$_POST['error_txt_'.$language['id_lang']]:''  )).'" />

					</div>';

				 }

				$output .= self::displayFlags($languages, $defaultLanguage, $divLangName, 'error_txt', true);

				$output .=self::info($mypath,'Message that appears when the field is not filled correctly (Only for basic style)');

	  

	  $output  .= '</td>';

	  $output  .= '</tr>';

	  

	  

	  

	  $myallorder=array();

				$orderExists = Db::getInstance()->ExecuteS('SELECT `order`  FROM `'._DB_PREFIX_.'contactform_item` WHERE `fid`='.$fid.' ORDER BY `order` ASC ');

				foreach($orderExists as $orderExist){

					array_push($myallorder,intval($orderExist['order']));

				}

				if(count($myallorder)>0)

					$maxorder=$myallorder[count($myallorder)-1];

				else

					$maxorder=0;

				

				$neworder=$maxorder+1;

	

	$output .='<tr>';

	$output .='<td>'.self::l('Order').':</td>';

	$output .='<td>

	<div class="field required" style=" background:none; border:none">

	<input class="text verifyInteger" type="text" name="order" size="6" value="'.($fdid != 0 ? $Fields[0]['order'] : $neworder).'">

				<span class="iferror">'.self::l('This value must be numeric').'</span>

	</div>

			</td>';

	$output .='</tr>';

	

	$output .='<tr>';

	$output .='<td>'.self::l('Publish').':</td>';

	$output .='<td>

		<input '.($fdid != 0 ? ($Fields[0]['published']==1?'checked':'' ): ($published==1?'checked':'')).' type="radio" name="published" value="1"  >'.self::l('Yes').'

		<input '.($fdid != 0 ? ($Fields[0]['published']==0?'checked':'' ): ($published==0?'checked':'')).' type="radio" name="published" value="0" >'.self::l('No').'';

	$output .='</tr>';







$output  .= '<tr>

	<td></td>

	<td><p><input class="button" type="submit" value="'.self::l('    Save    ').'" name="submitfield" /></p></td>

</tr>

</table>';





$output .='</fieldset>';

return $output;

}



 public  static function _errFormat($errmsg){

	global $cookie;

  $output ='<div style="border:1px solid #999999; background-color:#FFDFDF; width:99%; margin-bottom:20px; padding:5px">';

  $output .=self::l($errmsg);

  $output .='</div>';

  return $output;

 }





public static function showfieldList($mypath,$fid){

	

			global $cookie;

		$output ='';

		$mytoken=Tools::getValue('token');

		$fid=intval(Tools::getValue('fid'));

		$fdid=intval(Tools::getValue('fdid'));

		$task=Tools::getValue('task');

		$defaultLanguage = intval(Configuration::get('PS_LANG_DEFAULT'));

		$languages = Language::getLanguages();

		

		$forderby=Tools::getValue('forderby','order');

		$asc=Tools::getValue('asc','ASC');

		if($asc=='') $asc='ASC';

		if($forderby=='') $forderby='order';

		

		$url='index.php?tab=AdminModules&configure=contactform&token='.$mytoken.'&task=showfieldList&fid='.$fid;

		$url2='index.php?tab=AdminModules&configure=contactform&token='.$mytoken.'&task=editform&fid='.$fid;

		

		$output .='<link rel="stylesheet" type="text/css" href="'.$mypath.'library/dragdrop/isocraprint.css" />';

		$output .='<script src="'.$mypath.'library/dragdrop/jquery_002.js" type="text/javascript"></script>';

		$output .='<script src="'.$mypath.'library/dragdrop/jquery.js" type="text/javascript"></script>';

		

		$ListFields = Db::getInstance()->ExecuteS('SELECT cfit.*, cflit.*

											 FROM `'._DB_PREFIX_.'contactform_item` cfit 

											 LEFT JOIN `'._DB_PREFIX_.'contactform_item_lang` cflit  ON cfit.`fdid` = cflit.`fdid` 

											 WHERE cflit.`id_lang`='.$cookie->id_lang.' AND  cfit.`fid`='.$fid.'

											 ORDER BY cfit.'.$forderby.' '.$asc.'

											 ');

		if($asc=='ASC')

			$asc='DESC';

		elseif($asc=='DESC')

			$asc='ASC';

		else

			$asc='ASC';

		$nbForm=count($ListFields);

		$paramsForm = Db::getInstance()->ExecuteS('SELECT * FROM `'._DB_PREFIX_.'contactform`  WHERE `fid`='.$fid);

		

		$output.='<script type="text/javascript">

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

</script>

	 <script>

	$(document).ready(function() {

	// Initialise the first table (as before)

	// Initialise the second table specifying a dragClass and an onDrop function that will display an alert

	$("#table-2").tableDnD({

	    onDragClass: "myDragClass",

	    onDrop: function(table, row) {

            var rows = table.tBodies[0].rows;

            var debugStr = "Row dropped was "+row.id+". New order: ";

			var order="";

			var neworder = document.getElementById("neworder");

            for (var i=0; i<rows.length; i++) {

                debugStr += rows[i].id+" ";

				order += rows[i].id+" ";

            }

	        $("#debugArea").html(debugStr);

			neworder.value = order;

	    },

		onDragStart: function(table, row) {

			$("#debugArea").html("Started dragging row "+row.id);

		}

	});

    

});

	</script>





';

		

		$output  .= CFtoolbar::toolbar('editform',$mypath);

		$output.='<fieldset><legend><img src="'.$mypath.'img/listform.png" alt="" title="" />'.self::l('List fields').'</legend>';

		$output.='<div>';

		$output.= '<b><a href="'.$url2.'"><img  src="'.$mypath.'img/next.png" ><img src="'.$mypath.'img/next.png" >'.self::l('Form name') .'</b> : '.$paramsForm[0]['formname'].'</a><br>';

		$output.= '<b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.self::l('ID') .'</b> : '.$paramsForm[0]['fid'].'<br><br>';

		$output.='<form id ="frm1" name="frm1" method="post" action="'.$_SERVER['REQUEST_URI'].'" >';

		$output.='<table id="table-2" width="100%" class="table" cellspacing="0" cellpadding="0">';

		$output.='<thead>	<tr class="nodrag nodrop">

							<th><input type="checkbox" name="checkall" onclick="checkedAll(frm1);"></th>

							<th><a href="'.$url.'&asc='.$asc.'&forderby=order">'.self::l('Order').'</a></th>

							<th><input type="image" name="upFieldorder" src="'.$mypath.'img/save.png"></th>

							<th><a href="'.$url.'&asc='.$asc.'&forderby=fields_id">'.self::l('Field Id').'</a></th>

							<th><a href="'.$url.'&asc='.$asc.'&forderby=fields_name">'.self::l('Field Name').'</a></th>

							<th><a href="'.$url.'&asc='.$asc.'&forderby=fields_title">'.self::l('Field Title').'</a></th>

							<th><a href="'.$url.'&asc='.$asc.'&forderby=fields_type">'.self::l('Type').'</a></th>

							<th><a href="'.$url.'&asc='.$asc.'&forderby=fields_desc">'.self::l('Field Desc').'</a></th>

							<th><a href="'.$url.'&asc='.$asc.'&forderby=confirmation">'.self::l('Confirmation').'</a></th>

							<th><a href="'.$url.'&asc='.$asc.'&forderby=fields_valid">'.self::l('Validation').'</a></th>

							<th><a href="'.$url.'&asc='.$asc.'&forderby=fields_require">'.self::l('Require').'</a></th>

							<th align="center"><a href="'.$url.'&asc='.$asc.'&forderby=published">'.self::l('Published').'</a></th>

							<th style=" width:60px"></th>

							</tr></thead>';

		foreach($ListFields as $ListField){

			$adminurl='index.php?tab=AdminModules&configure=contactform&token='.$mytoken;

			$url='index.php?tab=AdminModules&configure=contactform&token='.$mytoken.'&task=addfield&fid='.$ListField['fid'].'&fdid='.$ListField['fdid'].'';

			$delurl='index.php?tab=AdminModules&configure=contactform&token='.$mytoken.'&task=delfield&fid='.$ListField['fid'].'&fdid='.$ListField['fdid'].'';

			$urlstatus='index.php?tab=AdminModules&configure=contactform&token='.$mytoken.'&task=changestatus&status='.$ListField['published'].'&fid='.$ListField['fid'].'&fdid='.$ListField['fdid'].'';

			$output.='<tr style="cursor: move;" class="alt" id="'.$ListField['fdid'].'">';

			$output.='<td><input type="checkbox" name="actlink['.$ListField['fdid'].']" value="1"></td>';

			$output.='	<td align="center" style="background:url('.$mypath.'img/move.png) no-repeat center">

							</td>';

			$output.='<td><input name="order_'.$ListField['fdid'].'" type="text" value="'.$ListField['order'].'" size="3" ></a></td>';

			$output.='<td><a href="'.$url.'">'.$ListField['fields_id'].'</td></a>';

			$output.='<td><a href="'.$url.'">'.$ListField['fields_name'].'</td></a>';

			$output.='<td><a href="'.$url.'">'.$ListField['fields_title'].'</a></td>';

			$output.='<td><a href="'.$url.'">'.$ListField['fields_type'].'</a></td>';

			$output.='<td><a href="'.$url.'">'.self::_substrStr(15,$ListField['fields_desc']).'</a></td>';

			$output.='<td><a href="'.$url.'">'.($ListField['confirmation']==1?self::l('Yes'):self::l('No')).'</a></td>';

			$output.='<td><a href="'.$url.'">'.$ListField['fields_valid'].'</a></td>';

			$output.='<td><a href="'.$url.'">'.($ListField['fields_require']==1?self::l('Yes'):self::l('No')).'</a></td>';

			

			$output.='<td align="center">

							<a title="'.($ListField['published']==1?self::l('Unpublished'):self::l('Published')).'" href="'.$urlstatus.'" ><img alt="'.self::l('Published').'" src="'.$mypath.'img/'.($ListField['published']==1?'ok.png':'forbbiden.gif').'"></a>

						 </td>';			 



			

			

			$output.='<td>

							<a title="'.self::l('Edit Field').'" href="'.$url.'"><img alt="'.self::l('Edit Field').'" src="'.$mypath.'img/edition.png"></a>

							<a title="'.self::l('Delete Field').'" href="'.$delurl.'" onclick="return(confirm(\''.self::l('Do you really want  to delete this field').'\'));"><img alt="'.self::l('Delete Field').'" src="'.$mypath.'img/delete.png"></a>

						 </td>';

			

						 

			$output.='</tr>';

		}

		$output.='</table>';

		$output.='<input type="hidden" name="fid" value="'.$fid.'">';

		$output.='<input style="margin:10px;" class="button" type="submit" name="deleteselectfld" value="'.self::l('Delete selected').'" onclick="return(confirm(\''.self::l('Do you really want  to delete fields selected?').'\'));"> ';

		$output .='<input type="hidden" value="2.2|1.1|3.3|4.4|" name="neworder" id="neworder" />';

		$output.='<input style="margin:10px;" class="button" type="submit" name="submitorder" value="'.self::l('Drag and change order').'">'.self::info($mypath,'Drag row first to change position then clic here');

		$output.='</form>';

		$output.='</div>';

		$output.='</fieldset>';

	

	return $output;

	

	

}//End showfieldlist



public static function _substrStr($max_caracteres,$txt){

  		$texte = substr($txt, 0, $max_caracteres);

  		return $texte." ...";

}



  public function _delForm($fid){

  

  	global $cookie;

	$mytoken=Tools::getValue('token');

	$defaultLanguage = intval(Configuration::get('PS_LANG_DEFAULT'));

	$languages = Language::getLanguages();

	$cmp=0;

	$output = '';

	if($fid=='')

		$fid=intval(Tools::getValue('fid'));

	

		if($fid!=0){

				//Deleting fields in the form

				

				$listfields=Db::getInstance()->ExecuteS('SELECT * FROM `'._DB_PREFIX_.'contactform_item` WHERE `fid`='.$fid);

				

				foreach ($listfields as $field){

					Db::getInstance()->ExecuteS('DELETE FROM `'._DB_PREFIX_.'contactform_item_lang` WHERE `fdid`='.$field['fdid']);

				}

				Db::getInstance()->ExecuteS('DELETE FROM `'._DB_PREFIX_.'contactform_item` WHERE `fid`='.$fid);

				

				Db::getInstance()->ExecuteS('DELETE FROM `'._DB_PREFIX_.'contactform` WHERE `fid`='.$fid);

				Db::getInstance()->ExecuteS('DELETE FROM `'._DB_PREFIX_.'contactform_lang` WHERE `fid`='.$fid);

		}

		

		for($i=0; $i<count($languages); $i++){

			$delform=Db::getInstance()->ExecuteS('SELECT * FROM `'._DB_PREFIX_.'contactform` WHERE `fid`='.$fid.'');

			if(count($delform)>0)

				$cmp++;

		}

		if($cmp==0)

			$statut=0;

		else

			$statut=1;

		header("location:index.php?tab=AdminModules&configure=contactform&&task=showformList2&token=".$mytoken."&statut=".$statut);

  }//End delform

  

  

 public function _delField($fid,$fdid){

  

  	global $cookie;

	$mytoken=Tools::getValue('token');

	$defaultLanguage = intval(Configuration::get('PS_LANG_DEFAULT'));

	$languages = Language::getLanguages();

	

	if($fdid=='')	

		$fdid=intval(Tools::getValue('fdid'));

		

	$cmp=0;

	

		if($fdid!=0){

			$Fpple=Db::getInstance()->ExecuteS('DELETE FROM `'._DB_PREFIX_.'contactform_item` WHERE `fdid`='.$fdid);

			$Flang=Db::getInstance()->ExecuteS('DELETE FROM `'._DB_PREFIX_.'contactform_item_lang` WHERE `fdid`='.$fdid);

		}

		

		if($Fpple && Flang)

			$statut=0;

		else

			$statut=1;

		

		header("location:index.php?tab=AdminModules&configure=contactform&token=".$mytoken."&task=showfieldList2&fid=".$fid."&statut=".$statut);

  }//End del field

  



public static function _changestatus($fdid,$updatestatus){

$defaultLanguage = intval(Configuration::get('PS_LANG_DEFAULT'));

$languages 	= 		Language::getLanguages();

$fid=intval(Tools::getValue('fid'));

$mytoken=Tools::getValue('token');

if($fdid=='')

	$fdid=intval(Tools::getValue('fdid'));



		@Db::getInstance()->ExecuteS(' UPDATE `'._DB_PREFIX_.'contactform_item` SET `published`= '.$updatestatus.' WHERE `fdid`='.$fdid);



	header("location:index.php?tab=AdminModules&configure=contactform&&task=showfieldList&token=".$mytoken."&fid=".$fid);



}





public  static function _ferrFormat($errmsg){

	global $cookie;

  $output ='<div style="border:1px solid #999999; background-color:#FFDFDF; width:99%; margin-bottom:20px; padding:5px">';

		$output .='<font color=red>'.CFtools::l($errmsg).'</font>:<br><br>';

  $output .='</div>';

  return $output;

 }

  





}//End classes



?>