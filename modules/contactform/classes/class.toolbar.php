<?php



class CFtoolbar {

	

	

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

	

	static public function file_exists_cache($filename)

	{

		if (!isset(self::$file_exists_cache[$filename]))

			self::$file_exists_cache[$filename] = file_exists($filename);

		return self::$file_exists_cache[$filename];

	}

	

	public static function toolbar($type,$mypath){

		

	global $cookie;

	$defaultLanguage = intval(Configuration::get('PS_LANG_DEFAULT'));

	$languages = Language::getLanguages();

	$mytoken=Tools::getValue('token');

	$task=Tools::getValue('task');

	$fid=intval(Tools::getValue('fid'));

	$fdid=intval(Tools::getValue('fdid'));

	

	$closetask=Tools::getValue('task');

	

	switch($type){

		case 'showform':

			$size='75%';

		break;

		case 'editform':

			if($fid!=0)

				$size="55%";

			else

				$size="85%";

		break;

		case 'restoreform':

			$size="95%";

		break;

		case 'settings':

			$size="60%";

		break;

		case 'classic':

			$size="95%";

		break;

		case 'seedetails':

			$size="95%";

		break;

		default:

			$size = '60%';

		break;

		

  	}

	

		$output ='<fieldset>

				<div style="float:right">

				<table width="900px">

				<tr align="center">

				<td width="'.$size.'" align="left">

				'.self::_barHomepage($mypath).

				'</td>';

				

				switch($type){

					

					//-----------------------Settings---------------------------------

					case 'settings':

						$output .='

	

			<td>

				<a href="index.php?tab=AdminModules&configure=contactform&token='.$mytoken.'&task=editcss"><img title="'.self::l('Edit Css').'" src="'.$mypath.'img/css.png"></a>

			</td>';

			

			/*$output .='<td><a href="index.php?tab=AdminModules&configure=contactform&token='.$mytoken.'&task=seo"><img title="'.self::l('Seo').'" src="'.$mypath.'img/seo.png"></a></td>';*/

			



			

			

	$output .='	<td><a href="index.php?tab=AdminModules&configure=contactform&token='.$mytoken.'"><img title="'.self::l('Close').'"  src="'.$mypath.'img/cancel.png"></a></td>

		</tr><tr align="center"><td></td>';

	$output .='<td><a href="index.php?tab=AdminModules&configure=contactform&token='.$mytoken.'&task=editcss">'.self::l('Edit Css').'</a></td>';

	//$output .='<td><a href="index.php?tab=AdminModules&configure=contactform&token='.$mytoken.'&task=seo">'.self::l('Seo').'</a></td>';

	$output .='<td><a title="'.self::l('Close').'" href="index.php?tab=AdminModules&configure=contactform&token='.$mytoken.'">'.self::l('Close').'</a></td>

		</tr>';

					break;

					//--------------------end settings -------------------------------

					//---------------------------------------Show form ---------------------------------

					case 'showform':

					

							$output.='	<td align="center"><a href="index.php?tab=AdminModules&configure=contactform&task=editform&token='.$mytoken.'"><img title="'.self::l('New form').'" src="'.$mypath.'img/addform.png"></a></td>

				<td align="center"><a href="javascript:history.back()"><img title="'.self::l('Back').'" src="'.$mypath.'img/previous1.png"></a></td>

				<td align="center"><a href="index.php?tab=AdminModules&configure=contactform&token='.$mytoken.'"><img title="'.self::l('Close').'" src="'.$mypath.'img/cancel.png"></a></td>

	</tr>

	<tr >

		<td></td>

		<td align="center"><a href="index.php?tab=AdminModules&configure=contactform&task=editform&token='.$mytoken.'">'.self::l('New form').'</a></td>

		<td align="center"><a href="javascript:history.back()">'.self::l('Back').'</a></td>

		<td align="center"><a href="index.php?tab=AdminModules&configure=contactform&token='.$mytoken.'">'.self::l('Close').'</a></td>

	</tr>';

					

					break;

					//--------------------------------------End show form ------------------------------

					

					case 'editform':

						

							

							if($fid!=0){

	$output .='<td>

				<a href="index.php?tab=AdminModules&configure=contactform&token='.$mytoken.'&task=addfield&fid='.$fid.'"><img title="'.self::l('New field').'" src="'.$mypath.'img/add.png"></a>

				</td>

				<td>

				<a href="index.php?tab=AdminModules&configure=contactform&token='.$mytoken.'&task=showfieldList&fid='.$fid.'"><img title="'.self::l('List Fields').'" src="'.$mypath.'img/list.png"></a>

			</td>

			<td>

			<a target="_blank" href="'.__PS_BASE_URI__.'contact-form.php?fid='.$fid.'"><img title="'.self::l('Preview').'" src="'.$mypath.'img/preview.png"></a>

			</td>';}

	$output .='	

					<td><a href="index.php?tab=AdminModules&configure=contactform&token='.$mytoken.'&task=showformList"><img title="'.self::l('List Form').'" src="'.$mypath.'img/list.png"></a></td>

					<td><a href="index.php?tab=AdminModules&configure=contactform&token='.$mytoken.'&task=showformList"><img title="'.self::l('Close').'" src="'.$mypath.'img/cancel.png"></a></td>

		</tr>

		<tr align="center">

			<td></td>';

		

		if($fid!=0){	

		$output .='

		<td><a href="index.php?tab=AdminModules&configure=contactform&token='.$mytoken.'&task=addfield&fid='.$fid.'">'.self::l('New field').'</a></td>

			<td><a href="index.php?tab=AdminModules&configure=contactform&token='.$mytoken.'&task=showfieldList&fid='.$fid.'">'.self::l('List Fields').'</a></td>

			<td><a target="_blank" href="'.__PS_BASE_URI__.'contact-form.php?fid='.$fid.'">'.self::l('Preview').'</a></td>';

			}

			

		$output .='	

				<td><a href="index.php?tab=AdminModules&configure=contactform&token='.$mytoken.'">'.self::l('List Form').'</a></td>

				<td><a href="index.php?tab=AdminModules&configure=contactform&token='.$mytoken.'&task=showformList">'.self::l('Close').'</a></td>

		</tr>';	

				break;	

				case 'exportform':

									

			$output .='<td><input type="image" name="subSavesql" src="'.$mypath.'img/save.png">

			</td>

			<td>

			<a href="index.php?tab=AdminModules&configure=contactform&token='.$mytoken.'&task=saveSql"><img src="'.$mypath.'img/altsave.png"></a>

			</td>

			

			';

			

	$output .='	<td><a href="index.php?tab=AdminModules&configure=contactform&token='.$mytoken.'"><img title="'.self::l('Close').'" src="'.$mypath.'img/cancel.png"></a></td>

		</tr>

		<tr align="center">

			<td></td>

		

			<td>'.self::l('   Save   ').'</td>

			<td><a href="index.php?tab=AdminModules&configure=contactform&token='.$mytoken.'&task=saveSql">'.self::l('Backup Alternative').'</a></td>';

			

		$output .='<td><a href="index.php?tab=AdminModules&configure=contactform&token='.$mytoken.'">'.self::l('Close').'</a></td>

		</tr>';

				break;

				

				case 'restoreform':

					$output .='	<td><a title="'.self::l('Close').'" href="index.php?tab=AdminModules&configure=contactform&token='.$mytoken.'"><img title="'.self::l('Close').'" src="'.$mypath.'img/cancel.png"></a></td>

		</tr>

		<tr align="center">

			<td></td>';

		$output .='<td><a title="'.self::l('Close').'" href="index.php?tab=AdminModules&configure=contactform&token='.$mytoken.'">'.self::l('Close').'</a></td>

		</tr>';



				break;

				case 'classic':

						$output .='	<td><a title="'.self::l('Close').'" href="index.php?tab=AdminModules&configure=contactform&token='.$mytoken.'"><img title="'.self::l('Close').'" src="'.$mypath.'img/cancel.png"></a></td>

		</tr>

		<tr align="center">

			<td></td>';

		$output .='<td><a title="'.self::l('Close').'" href="index.php?tab=AdminModules&configure=contactform&token='.$mytoken.'">'.self::l('Close').'</a></td>

		</tr>';



				break;

				case 'seedetails':

						$output .='	<td><a title="'.self::l('Close').'" href="index.php?tab=AdminModules&configure=contactform&token='.$mytoken.'&task=seedata"><img title="'.self::l('Close').'" src="'.$mypath.'img/cancel.png"></a></td>

		</tr>

		<tr align="center">

			<td></td>';

		$output .='<td><a title="'.self::l('Close').'" href="index.php?tab=AdminModules&configure=contactform&token='.$mytoken.'&task=seedata">'.self::l('Close').'</a></td>

		</tr>';



				break;



					

					default:

					

					break;

			

			

				}

				

				

		

		$output .='</table>		';

	$output .='</div></fieldset><br>';

		

		return $output;

		

		

		

	}//End function toolbar

	

public static function _barHomepage($link){



global $cookie;

$mytoken=Tools::getValue('token');

$defaultLanguage = intval(Configuration::get('PS_LANG_DEFAULT'));

$languages = Language::getLanguages();

$url='index.php?tab=AdminModules&configure=contactform&token='.$mytoken;



$output ='<link rel="stylesheet" type="text/css" href="'.$link.'library/homemenu/anylinkcssmenu.css" />';

$output .='<script type="text/javascript" src="'.$link.'library/homemenu/anylinkcssmenu.js" /></script>';

$output .='

	<script type="text/javascript">

	//anylinkcssmenu.init("menu_anchors_class") ////Pass in the CSS class of anchor links (that contain a sub menu)

	anylinkcssmenu.init("anchorclass")

	</script>

	<a href="index.php?tab=AdminModules&configure=contactform&token='.$mytoken.'" >

	<img alt="'.self::l('Home').'" title="'.self::l('Home').'" src="'.$link.'img/home.png"></a>

	

	<a href="index.php?tab=AdminModules&configure=contactform&token='.$mytoken.'" class="anchorclass myownclass" rel="submenu3">

	<img alt="Menu" title="Menu" src="'.$link.'img/kmenu.png"></a>

														

	<div id="submenu3" class="anylinkcsscols">';

$output .='<table class="homepage" border="1">';

	$output .='<tr>';

	$output .='<td>';

	$output .='<table class="homepage1" border="1" >';

		$output .='<tr>';

			$output .='<td><a href="'.$url.'&task=showformList">

							<img src="'.$link.'img/edit-mini.png"><br>'.self::l('Managing your form').'</a></td>';

			$output .='<td><a href="'.$url.'&task=seedata"><img src="'.$link.'img/see-mini.png"><br>'.self::l('See data').'</a></td>';

			$output .='<td><a href="'.$url.'&task=addsample"><img src="'.$link.'img/sample-mini.png"><br>'.self::l('Add sample data').'</a></td>';

		$output .='</tr>';

		$output .='<tr>';

			$output .='<td><a href="'.$url.'&task=exportForm"><img src="'.$link.'img/save-mini.png"><br>'.self::l('Save your form').'</a></td>';

			$output .='<td><a href="'.$url.'&task=restoreForm"><img src="'.$link.'img/store-mini.png"><br>'.self::l('Restore your Form').'</a></td>';

			$output .='<td><a href="'.$url.'&task=settings"><img src="'.$link.'img/settings-mini.png"><br>'.self::l('Settings').'</a></td>';

		$output .='</tr>';

	$output .='</table>';

	$output .='</td>';

	$output .='</tr>';	

	$output .='</table>';	

	



$output .='	</div></div>';

	return $output ;



}







	

}//End classes







?>