<?php 

class DeleteOrders extends Module
{
	private $_html = '';
	private $_postErrors = array();

	function __construct()
	{
		$this->name = 'deleteorders';
		$this->tab = 'Tools';
		$this->version = 1.0;

		parent::__construct(); // The parent construct is required for translations

		$this->page = basename(__FILE__, '.php');
		$this->displayName = $this->l('Delete orders');
		$this->description = $this->l('Enable delete button in order page - www.catalogo-onlinersi.com.ar');
	}

	function install()
	{
		 if (!parent::install())
	return false;
		return true;
	}

	public function getContent()
	{
				$output = '<h2>'.$this->displayName.'</h2>';
		if (Tools::isSubmit('submitdelete'))
		{

			if (isset($errors) AND sizeof($errors))
				$output .= $this->displayError(implode('<br />', $errors));
				
			else
chmod("tabs/AdminOrders.php", 0777 ); 
if(ini_get("allow_url_fopen") == "0"){
ini_set("allow_url_fopen", "1");
}
$str = "";

if($fh = fopen('tabs/AdminOrders.php', 'r')){
while(!feof($fh)){
$str .= fgets ($fh);
}
$str = str_replace('$this->colorOnBackground = true;', '$this->colorOnBackground = true;$this->delete = true;', $str);
fclose($fh);
chmod("tabs/AdminOrders.php", 0644 ); 
} 
else {
die ("Error opening file in ".__FILE__." on line ".__LINE.".");
}

$x42 = fopen ("tabs/AdminOrders.php", "w");
      fwrite ($x42,$str);
				$output .= $this->displayConfirmation($this->l('Delete orders enabled'));
		    }
			elseif	(Tools::isSubmit('submitnodelete'))
		{
chmod("tabs/AdminOrders.php", 0777 ); 
if(ini_get("allow_url_fopen") == "0"){
ini_set("allow_url_fopen", "1");
}
$str = "";

if($fh = fopen('tabs/AdminOrders.php', 'r')){
while(!feof($fh)){
$str .= fgets ($fh);
}
$str = str_replace('$this->colorOnBackground = true;$this->delete = true;','$this->colorOnBackground = true;', $str);
fclose($fh);
chmod("tabs/AdminOrders.php", 0644 ); 
} else {
die ("Error opening file in ".__FILE__." on line ".__LINE.".");
}

$x42 = fopen ("tabs/AdminOrders.php", "w");
      fwrite ($x42,$str);
				$output .= $this->displayConfirmation($this->l('Delete orders disabled'));
		}
		return $output.$this->displayForm();
		
	
	}
	function deleteorders()
	{
chmod("tabs/AdminOrders.php", 0777 ); 
if(ini_get("allow_url_fopen") == "0"){
ini_set("allow_url_fopen", "1");
}
$str = "";

if($fh = fopen('AdminOrders.php', 'r')){
while(!feof($fh)){
$str .= fgets ($fh);
}
$str = str_replace('true;$this->delete = true;', '', $str);
fclose($fh);
} else {
die ("Error opening file in ".__FILE__." on line ".__LINE.".");
}

$x42 = fopen ("AdminOrders.php", "w");
      fwrite ($x42,$str);
		
    


	}

	public function displayForm()
	{
		global $cookie,$currentIndex;
		$output = '
		<form method="post" onsubmit="deleteorders(); ">
			<fieldset><legend><img src="'.$this->_path.'png.png" alt="" title="" />'.$this->l('Settings').'</legend>
				
		
	
		<p class="clear">'.$this->l('Enable delete button in order page').'</p>
				<center><input type="submit" name="submitdelete" value="'.$this->l('Enable delete orders').'" class="button" /></center>
				<center><input type="submit" name="submitnodelete" value="'.$this->l('Disable delete orders').'" class="button" /></center><br/>
				'.$this->l('For modules and themes, visit:').'<a href="http://www.catalogo-onlinersi.com.ar"> http://www.catalogo-onlinersi.com.ar</a>
			</fieldset>						
		</form>
		<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="MRASNL38GZZ7Y">
<input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypal.com/es_XC/i/scr/pixel.gif" width="1" height="1">
</form>';
		return $output;
	}


	
	

}
?>