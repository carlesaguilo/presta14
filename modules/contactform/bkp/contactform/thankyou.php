<?php

$useSSL = true;

global $cookie;

require(dirname(__FILE__).'/config/config.inc.php');

include(dirname(__FILE__).'/header.php');

include _PS_MODULE_DIR_.'contactform/classes/class.front.php';

$fid=intval(Tools::getValue('fid'));



$lastparams = Db::getInstance()->ExecuteS('SELECT cf.`email`,cf.`formname`, cfl.*

											 FROM `'._DB_PREFIX_.'contactform` cf 

											 LEFT JOIN `'._DB_PREFIX_.'contactform_lang` cfl  ON cf.`fid` = cfl.`fid` 

											 WHERE cfl.`id_lang`='.$cookie->id_lang.' AND  cf.`fid`='.$fid.'

											 ');





echo CFfront::navigationPipe($fid);



echo '<div class="rte">'.$lastparams[0]['thankyou'].'</div>';

	if(!empty($lastparams[0]['returnurl']))

echo '<br><br><center><a href="'.$lastparams[0]['returnurl'].'" class="returnurl">'.CFtools::l('Back').'</a></center><br>';











include(dirname(__FILE__).'/footer.php');

?>