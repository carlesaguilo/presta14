<?php

$useSSL = true;

global $cookie;

require(dirname(__FILE__).'/config/config.inc.php');

include(dirname(__FILE__).'/header.php');

include _PS_MODULE_DIR_.'contactform/classes/class.front.php';



$libpath=__PS_BASE_URI__.'modules/contactform/library/';

$imgpath=__PS_BASE_URI__.'modules/contactform/img/';

$libform=$libpath.'form/';

$libcal=$libpath.'calendar/';



//Navigation pipe





echo '

<link rel="stylesheet" href="'.$libform.'css/validationEngine.jquery.css" type="text/css"/>

'.(Configuration::get('CONTACTFORM_STYLE')==1?'<link rel="stylesheet" href="'.$libform.'css/niceform.css" type="text/css"/>':'<link rel="stylesheet" href="'.$libform.'css/template.css" type="text/css"/>').'

<script src="'.$libform.'js/languages/jquery.validationEngine-'.CFfront::getIsocode($cookie->id_lang).'.js" type="text/javascript" charset="utf-8"></script>

<script src="'.$libform.'js/jquery.validationEngine.js" type="text/javascript" charset="utf-8"></script>

        <script>

            jQuery(document).ready(function(){

                jQuery("#formID").validationEngine();

            });

        </script>

';



echo '<script type="text/javascript" src="'.$libcal.'src/jquery.dynDateTime.js"></script>

		<script type="text/javascript" src="'.$libcal.'lang/calendar-'.CFfront::getIsocode($cookie->id_lang).'.js"></script>

		<link rel="stylesheet" type="text/css" media="all" href="'.$libcal.'css/calendar-win2k-cold-1.css"  />

';

//====================================== TEST IF NO JQUERY==========================================

$fid=intval(Tools::getValue('fid'));

$error=0;

$errortxt=array();

if(Tools::isSubmit('submitform')){

				$tabFields=array();

				$tabFields['name']=array();

				$tabFields['label']=array();

				$tabFields['value']=array();

				$tabFields['fields_require']=array();

				$tabFields['confirmation']=array();

				$tabFields['fields_type']=array();

				$tabFields['fields_valid']=array();

				$tabFields['order']=array();

				

		$captcha			=	strtoupper(Tools::getValue('captcha'));

	

	$Listfields = Db::getInstance()->ExecuteS('SELECT cf.*, cfl.*

						FROM `'._DB_PREFIX_.'contactform_item` cf 

						LEFT JOIN `'._DB_PREFIX_.'contactform_item_lang` cfl  ON cf.`fdid` = cfl.`fdid` 

						WHERE cfl.`id_lang`='.$cookie->id_lang.' AND  cf.`fid`='.$fid.' AND cf.`published`=1

						ORDER BY cf.`order` ASC

						');

/*	echo '<pre>';

	var_dump($Listfields);

	echo '</pre>';*/	

	

//REtrieve all

		foreach($Listfields as $fields){

			${$fields['fields_name']}			=	Tools::getValue($fields['fields_name']);

			array_push( $tabFields['name'],$fields['fields_name']);

			array_push( $tabFields['label'],$fields['fields_title']);

			array_push( $tabFields['fields_require'],$fields['fields_require']);

			array_push( $tabFields['confirmation'],$fields['confirmation']);

			array_push( $tabFields['fields_type'],$fields['fields_type']);

			array_push( $tabFields['fields_valid'],$fields['fields_valid']);

			

			if($fields['fields_type']=='fileup'){

							if(!empty($_FILES[$fields['fields_name']]['type'])||$_FILES[$fields['fields_name']]['type']!='')

								$typename='+'.$_FILES[$fields['fields_name']]['type'];

							else

								$typename='';

							

							if(!empty($_FILES[$fields['fields_name']]['name'])||$_FILES[$fields['fields_name']]['name']!='')

								$namename='+'.$_FILES[$fields['fields_name']]['name'];

							else

								$namename='';

							

							array_push( $tabFields['value'],$_FILES[$fields['fields_name']]['tmp_name'].$typename.$namename);

					}

					else

							array_push( $tabFields['value'],${$fields['fields_name']});

			

			

			//Requierd field

			if($fields['fields_require']==1&&$fields['fields_type']!='fileup'&&$fields['fields_type']!='captcha'){

					if(${$fields['fields_name']}==''||empty(${$fields['fields_name']})){

						array_push($errortxt,'<b>'.$fields['fields_title'].': </b>'.(!empty($fields['error_txt'])?$fields['error_txt']:CFtools::l('Fields not properly completed')));

						$error++;

					}

			}

			

			if($fields['fields_require']==1&&$fields['fields_type']=='fileup'&&$fields['fields_type']!='captcha'){

					if(empty($_FILES[$fields['fields_name']]['tmp_name'])){

						array_push($errortxt,'<b>'.$fields['fields_title'].': </b>'. (!empty($fields['error_txt'])?$fields['error_txt']:CFtools::l('Fields not properly completed')) );

						$error++;

					}

			}

			

			//Email verification

			if($fields['fields_type']=='email' || $fields['fields_valid']=='email'){

				

				$Syntaxe='#^[\w.-]+@[\w.-]+\.[a-zA-Z]{2,6}$#';

				if(!preg_match($Syntaxe,${$fields['fields_name']}) ){

					array_push($errortxt,'<b>'.$fields['fields_title'].': </b>'.(!empty($fields['error_txt'])?$fields['error_txt']:CFtools::l('Invalid address email')));

					$error++;

				}

				

			}

			

			//Verify confirmation

			

			if($fields['fields_require']==1&&$fields['fields_type']!='fileup'&&$fields['fields_type']!='captcha'){

					if(${$fields['fields_name']}==''||empty(${$fields['fields_name']})){

						array_push($errortxt,'<b>'.$fields['fields_title'].': </b>'.CFtools::l('Required field'));

						$error++;

					}

			}

			

			//Captcha validation

			if($fields['fields_type']=='captcha'){

					if(Tools::getValue('re_mycaptcha')==''){

						array_push($errortxt,'<b>'.$fields['fields_title'].': </b>'.CFtools::l('Required field'));

						$error++;

					}

					

					

					if(strtolower(Tools::getValue('re_mycaptcha'))!=$_SESSION['securimage_code_value']){

						array_push($errortxt,'<b>'.$fields['fields_title'].': </b>'.CFtools::l('Code not matched'));

						$error++;

					}

					

			}

			

			

			if($fields['confirmation']==1  

					 && $fields['fields_type']!='captcha' 

					 && $fields['fields_type']!='password' 

					 && $fields['fields_type']!='calendar' 

					 && $fields['fields_type']!='radio' 

					 && $fields['fields_type']!='checkbox'

					 && $fields['fields_type']!='select'

					 && $fields['fields_type']!='button'

					 && $fields['fields_type']!='imagebtn'

					 && $fields['fields_type']!='submitbtn'

					 && $fields['fields_type']!='resetbtn'

					 && $fields['fields_type']!='fileup'

					 && $fields['fields_type']!='separator'){

				if(${$fields['fields_name']}!=Tools::getValue('re_'.$fields['fields_name'])){

					array_push($errortxt,'<b>'.$fields['fields_title'].': </b>'.CFtools::l('The value is not identical'));

					$error++;

				}

			}

			//File upload verification

			if($fields['fields_type']=='fileup'){

				

					if(!empty($_FILES[$fields['fields_name']]['tmp_name'])){

						$content_dir = dirname(__FILE__).'/modules/contactform/upload/'; // dossier où sera déplacé le fichier

						$tmp_file = $_FILES[$fields['fields_name']]['tmp_name'];

						$type_file = $_FILES[$fields['fields_name']]['type'];

						$name_file = $_FILES[$fields['fields_name']]['name'];

						$acceptedformat =array();

						

						$format=Configuration::get('CONTACTFORM_UPFORMAT');

						$tabformat = explode(',',$format);

						for($i=0;$i<count($tabformat);$i++){

							array_push($acceptedformat,trim($tabformat[$i]));

						}

						

						//Take uploaded file format

						$tformat = explode('.',$name_file);

						$fileformat = $tformat[count($tformat)-1];

						if(!in_array($fileformat,$acceptedformat)){

							array_push($errortxt,'<b>'.$fields['fields_title'].': </b>'.CFtools::l('Invalid format'));

							$error++;	

						}

					}//end if

				

			}

			

		}//End foreach

		

		//Test if there is error

		if($error>0 || count($errortxt)>0){

			echo CFfront::displayError($errortxt);

		}

		else{

		//If no error

				$tabFields=array();

				$tabFields['name']=array();

				$tabFields['label']=array();

				$tabFields['value']=array();

				$tabFields['fields_require']=array();

				$tabFields['confirmation']=array();

				$tabFields['fields_type']=array();

				$tabFields['fields_valid']=array();

				$tabFields['order']=array();

			foreach($Listfields as $fields){

				

				${$fields['fields_name']}			=	Tools::getValue($fields['fields_name']);

					array_push( $tabFields['name'],$fields['fields_name']);

					array_push( $tabFields['label'],$fields['fields_title']);

					array_push( $tabFields['fields_require'],$fields['fields_require']);

					array_push( $tabFields['confirmation'],$fields['confirmation']);

					array_push( $tabFields['fields_type'],$fields['fields_type']);

					array_push( $tabFields['fields_valid'],$fields['fields_valid']);

					

					if($fields['fields_type']=='fileup'){

							if(!empty($_FILES[$fields['fields_name']]['type'])||$_FILES[$fields['fields_name']]['type']!='')

								$typename='+'.$_FILES[$fields['fields_name']]['type'];

							else

								$typename='';

							

							if(!empty($_FILES[$fields['fields_name']]['name'])||$_FILES[$fields['fields_name']]['name']!='')

								$namename='+'.$_FILES[$fields['fields_name']]['name'];

							else

								$namename='';

							

							array_push( $tabFields['value'],$_FILES[$fields['fields_name']]['tmp_name'].$typename.$namename);

					}

					else

							array_push( $tabFields['value'],${$fields['fields_name']});

					

			}//End foreach

			CFfront::_SendMail($tabFields,$fid);

			

		}//En else

	

} //end submit tools



//====================================== END TEST IF NO JQUERY==========================================





//Retrieving formulaire id

if(empty($tabFields)){

				$tabFields=array();

				$tabFields['name']=array();

				$tabFields['label']=array();

				$tabFields['value']=array();

				$tabFields['fields_require']=array();

				$tabFields['confirmation']=array();

				$tabFields['fields_type']=array();

				$tabFields['fields_valid']=array();

				$tabFields['order']=array();

}

if($fid==0 || empty($fid)){

$Listforms = Db::getInstance()->ExecuteS('SELECT * FROM `'._DB_PREFIX_.'contactform` ');

if(count($Listforms)>0)

	$fid = $Listforms[0]['fid'];

else

	$fid = 0;

}





if($fid!=0){



	$Listfield = Db::getInstance()->ExecuteS('SELECT * FROM `'._DB_PREFIX_.'contactform_item` WHERE `fid` = '.$fid.' AND `published` = 1 ORDER BY `order` ASC');

	if(count($Listfield)==0)

		echo CFtools::_ferrFormat('There is no field in this form');

	else{

		switch(Configuration::get('CONTACTFORM_FORM')){

			case 0:

				echo CFfront::viewbasicForm($tabFields,$fid,$imgpath,$libpath);

			break;

			case 1:

				echo CFfront::viewForm($tabFields,$fid,$imgpath,$libpath);

			break;

			default:

				echo CFfront::viewbasicForm($tabFields,$fid,$imgpath,$libpath);

			break;

			

		}

		//echo CFfront::viewForm($fid,$imgpath,$libpath);

		//echo CFfront::viewbasicForm($fid,$imgpath,$libpath);

	}





}//End if($fid!=0){

else{

//There is no form

	echo CFtools::_ferrFormat('There is no form');



}

















include(dirname(__FILE__).'/footer.php');

?>