<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Documento sin título</title>
</head>

<body>
<div>
  <div>
  <?php

        $dir = '../../tools/smarty/compile/';
		$archivoAnterior = '../../tools/smarty/compile/index.php';
		  if (file_exists("../../tools/smarty/tmp")){ 
		  }
		  else
		  {
		mkdir('../../tools/smarty/tmp',0777); 
		  }
		copy($archivoAnterior,'../../tools/smarty/tmp/index.php'); 
		$handle = opendir($dir);
		while ($file = readdir($handle))
	 		{
  				if (is_file($dir.$file))
  				{
    		    unlink($dir.$file);
    		    }
			}
				copy('../../tools/smarty/tmp/index.php','../../tools/smarty/compile/index.php'); 

?>

  </div>
</div>
</body>
</html>
