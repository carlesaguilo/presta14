<?php

/*-----------------------------------------------------------------------------

Autor: Javier Barredo

Autor E-Mail: naveto@gmail.com

Fecha: Mayo 2011

Version : 0.7v4

Agradecimientos: Yago Ferrer por su módulo de pago  que se utilizó como base de este módulo.

Alberto Fernández por su ayuda con los testeos y las imágenes.

Version: 0.85 (solo probada en PS1.4)

Adaptación a PS 1.4: David Vidal (chienandalu@gmail.com)



Notas para la versión de Servired 0.8 (21-5-2011)

--------------------------------



[-] Adaptación del módulo a la versión 1.4 de Prestashop:

  - El pago válido retorna a OrderConfirmation, de modo que sigue los cauces de los demás módulos de pago de Prestashop.

  - De este modo ahora el módulo Google Analytics puede ofrecer estadísticas de estos pagos. Antes no se registraban dichas conversiones.

  - Adaptada plantilla pago-correcto.tpl

  - Corregido bug en plantilla pago-error.tpl

  - Corregido fallos en instalación y desinstalación en versión 1.4

  - Corregido fallo de secure_key en PS 1.4

  - Corregida ruta de icono "personalización"

  - pago_correcto.php deja de ser necesario

  - Algunas modificaciones de gráficos



Released under the GNU General Public License

-----------------------------------------------------------------------------*/



if (!defined('_CAN_LOAD_FILES_'))

	exit;



class servired extends PaymentModule

{

	private	$_html = '';

	private $_postErrors = array();



	public function __construct(){

		$this->name = 'servired';

		$this->tab = 'payments_gateways';

		$this->version = '0.85';



		// Array config con los datos de configuración

		$config = Configuration::getMultiple(array('SERVIRED_URLTPV', 'SERVIRED_CLAVE', 'SERVIRED_NOMBRE', 'SERVIRED_CODIGO', 'SERVIRED_TERMINAL', 'SERVIRED_TIPOFIRMA', 'SERVIRED_MONEDA', 'SERVIRED_TRANS', 'SERVIRED_NOTIFICACION', 'SERVIRED_SSL', 'SERVIRED_ERROR_PAGO', 'SERVIRED_IDIOMAS_ESTADO'));

		// Establecer propiedades según los datos de configuración

		if (isset($config['SERVIRED_URLTPV']))

			$this->urltpv = $config['SERVIRED_URLTPV'];

		if (isset($config['SERVIRED_CLAVE']))

			$this->clave = $config['SERVIRED_CLAVE'];

		if (isset($config['SERVIRED_NOMBRE']))

			$this->nombre = $config['SERVIRED_NOMBRE'];

		if (isset($config['SERVIRED_CODIGO']))

			$this->codigo = $config['SERVIRED_CODIGO'];

		if (isset($config['SERVIRED_TERMINAL']))

			$this->terminal = $config['SERVIRED_TERMINAL'];

		if (isset($config['SERVIRED_TIPOFIRMA']))

			$this->tipofirma = $config['SERVIRED_TIPOFIRMA'];

		if (isset($config['SERVIRED_MONEDA']))

			$this->moneda = $config['SERVIRED_MONEDA'];

		if (isset($config['SERVIRED_TRANS']))

			$this->trans = $config['SERVIRED_TRANS'];

		if (isset($config['SERVIRED_NOTIFICACION']))

			$this->notificacion = $config['SERVIRED_NOTIFICACION'];

		if (isset($config['SERVIRED_SSL']))

			$this->ssl = $config['SERVIRED_SSL'];

		if (isset($config['SERVIRED_ERROR_PAGO']))

			$this->error_pago = $config['SERVIRED_ERROR_PAGO'];

		if (isset($config['SERVIRED_IDIOMAS_ESTADO']))

			$this->idiomas_estado = $config['SERVIRED_IDIOMAS_ESTADO'];





		parent::__construct();



		$this->page = basename(__FILE__, '.php');

		$this->displayName = $this->l('Servired');

		$this->description = $this->l('Aceptar pagos con tarjeta v&iacute;a Servired');



		// Mostrar aviso en la página principal de módulos si faltan datos de configuración.

		if (!isset($this->urltpv)

		OR !isset($this->clave)

		OR !isset($this->nombre)

		OR !isset($this->codigo)

		OR !isset($this->terminal)

		OR !isset($this->tipofirma)

		OR !isset($this->moneda)

		OR !isset($this->trans)

		OR !isset($this->notificacion)

		OR !isset($this->ssl)

		OR !isset($this->error_pago)

		OR !isset($this->idiomas_estado))





		$this->warning = $this->l('Faltan datos por configurar del m&oacute;dulo Servired.');

	}



	public function install()

	{

		// Valores por defecto al instalar el módulo

		if (!parent::install()

			OR !Configuration::updateValue('SERVIRED_URLTPV', 'https://sis-t.sermepa.es:25443/sis/realizarPago')

			OR !Configuration::updateValue('SERVIRED_NOMBRE', 'Escribe el nombre de tu tienda')

			OR !Configuration::updateValue('SERVIRED_TERMINAL', 1)

			OR !Configuration::updateValue('SERVIRED_TIPOFIRMA', 0)

			OR !Configuration::updateValue('SERVIRED_MONEDA', '978')

			OR !Configuration::updateValue('SERVIRED_TRANS', 0)

			OR !Configuration::updateValue('SERVIRED_NOTIFICACION', 0)

			OR !Configuration::updateValue('SERVIRED_SSL', 'no')

			OR !Configuration::updateValue('SERVIRED_ERROR_PAGO', 'no')

			OR !Configuration::updateValue('SERVIRED_IDIOMAS_ESTADO', '0')

			OR !$this->registerHook('payment')

			OR !$this->registerHook('paymentReturn'))

			return false;

		return true;

	}



	public function uninstall()

	{

	   // Valores a quitar si desinstalamos el módulo

		if (!Configuration::deleteByName('SERVIRED_URLTPV')

			OR !Configuration::deleteByName('SERVIRED_CLAVE')

			OR !Configuration::deleteByName('SERVIRED_NOMBRE')

			OR !Configuration::deleteByName('SERVIRED_CODIGO')

			OR !Configuration::deleteByName('SERVIRED_TERMINAL')

			OR !Configuration::deleteByName('SERVIRED_TIPOFIRMA')

			OR !Configuration::deleteByName('SERVIRED_MONEDA')

			OR !Configuration::deleteByName('SERVIRED_TRANS')

			OR !Configuration::deleteByName('SERVIRED_NOTIFICACION')

			OR !Configuration::deleteByName('SERVIRED_SSL')

			OR !Configuration::deleteByName('SERVIRED_ERROR_PAGO')

			OR !Configuration::deleteByName('SERVIRED_IDIOMAS_ESTADO')

			OR !parent::uninstall())

			return false;

		return true;

	}



	private function _postValidation(){

	    // Si al enviar los datos del formulario de configuración hay campos vacios, mostrar errores.

		if (isset($_POST['btnSubmit'])){

			if (empty($_POST['urltpv']))

				$this->_postErrors[] = $this->l('Se requiere la URL de llamada del entorno.');

			if (empty($_POST['clave']))

				$this->_postErrors[] = $this->l('Se requiere la Clave secreta de encriptaci&oacute;n.');

			if (empty($_POST['nombre']))

				$this->_postErrors[] = $this->l('Se requiere el Nombre del comercio.');

			if (empty($_POST['codigo']))

				$this->_postErrors[] = $this->l('Se requiere el N&uacute;mero de comercio (FUC).');

			if (empty($_POST['terminal']))

				$this->_postErrors[] = $this->l('Se requiere el N&uacute;mero de terminal.');

			if (empty($_POST['moneda']))

				$this->_postErrors[] = $this->l('Se requiere el Tipo de moneda.');



		}

	}



	private function _postProcess(){

	    // Actualizar la configuración en la BBDD

			if (isset($_POST['btnSubmit'])){

			Configuration::updateValue('SERVIRED_URLTPV', $_POST['urltpv']);

			Configuration::updateValue('SERVIRED_CLAVE', $_POST['clave']);

			Configuration::updateValue('SERVIRED_NOMBRE', $_POST['nombre']);

			Configuration::updateValue('SERVIRED_CODIGO', $_POST['codigo']);

			Configuration::updateValue('SERVIRED_TERMINAL', $_POST['terminal']);

			Configuration::updateValue('SERVIRED_TIPOFIRMA', $_POST['tipofirma']);

			Configuration::updateValue('SERVIRED_MONEDA', $_POST['moneda']);

			Configuration::updateValue('SERVIRED_TRANS', $_POST['trans']);

			Configuration::updateValue('SERVIRED_NOTIFICACION', $_POST['notificacion']);

			Configuration::updateValue('SERVIRED_SSL', $_POST['ssl']);

			Configuration::updateValue('SERVIRED_ERROR_PAGO', $_POST['error_pago']);

			Configuration::updateValue('SERVIRED_IDIOMAS_ESTADO', $_POST['idiomas_estado']);

		}



		$this->_html .= '<div class="conf confirm"><img src="../img/admin/ok.gif" alt="'.$this->l('ok').'" /> '.$this->l('Configuraci&oacute;n actualizada').'</div>';

	}



	private function _displayservired()

	{

	    // Aparición el la lista de módulos

		$this->_html .= '<img src="../modules/servired/servired.png" style="float:left; margin-right:15px;"><b>'.$this->l('Este m&oacute;dulo te permite aceptar pagos con tarjeta.').'</b><br /><br />

		'.$this->l('Si el cliente elije este modo de pago, podr&aacute; pagar de forma autom&aacute;tica.').'<br /><br /><br />';

	}



	private function _displayForm(){



		// Opciones para el select de monedas.

		$moneda = Tools::getValue('moneda', $this->moneda);

		$iseuro =  ($moneda == '978') ? ' selected="selected" ' : '';

		$isdollar = ($moneda == '840') ? ' selected="selected" ' : '';

		// Opciones para activar/desactivar SSL

		$ssl = Tools::getValue('ssl', $this->ssl);

		$ssl_si = ($ssl == 'si') ? ' checked="checked" ' : '';

		$ssl_no = ($ssl == 'no') ? ' checked="checked" ' : '';

		// Opciones para el comportamiento en error en el pago

		$error_pago = Tools::getValue('error_pago', $this->error_pago);

		$error_pago_si = ($error_pago == 'si') ? ' checked="checked" ' : '';

		$error_pago_no = ($error_pago == 'no') ? ' checked="checked" ' : '';

		// Opciones para activar los idiomas

		$idiomas_estado = Tools::getValue('idiomas_estado', $this->idiomas_estado);

		$idiomas_estado_si = ($idiomas_estado == 'si') ? ' checked="checked" ' : '';

		$idiomas_estado_no = ($idiomas_estado == 'no') ? ' checked="checked" ' : '';

	    // Opciones tipofirma

	  	  $tipofirma = Tools::getValue('tipofirma', $this->tipofirma);

	  	  $tipofirma_a =  ($tipofirma==0) ? ' checked="checked" ' : '';

	  	  $tipofirma_c =  ($tipofirma==1) ? ' checked="checked" '  : '';



	    // Opciones notificacion

	      $notificacion = Tools::getValue('notificacion', $this->notificacion);

		  $notificacion_s =  ($notificacion==1) ? ' checked="checked" '  : '';

		  $notificacion_n =  ($notificacion==0) ? ' checked="checked" '  : '';

		// Mostar formulario

		$this->_html .=

		'<form action="'.$_SERVER['REQUEST_URI'].'" method="post">

			<fieldset>

			<legend><img src="../img/admin/contact.gif" />'.$this->l('Configuraci&oacute;n del TPV').'</legend>

				<table border="0" width="680" cellpadding="0" cellspacing="0" id="form">

					<tr><td colspan="2">'.$this->l('Por favor completa la informaci&oacute;n requerida que te proporcionar&aacute; tu banco Servired.').'.<br /><br /></td></tr>

					<tr><td width="215" style="height: 35px;">'.$this->l('URL de llamada del entorno').'</td><td><input type="text" name="urltpv" value="'.Tools::getValue('urltpv', $this->urltpv).'" style="width: 330px;" /></td></tr>

					<tr><td width="215" style="height: 35px;">'.$this->l('Clave secreta de encriptaci&oacute;n').'</td><td><input type="text" name="clave" value="'.Tools::getValue('clave', $this->clave).'" style="width: 200px;" /></td></tr>

					<tr><td width="215" style="height: 35px;">'.$this->l('Nombre del comercio').'</td><td><input type="text" name="nombre" value="'.htmlentities(Tools::getValue('nombre', $this->nombre), ENT_COMPAT, 'UTF-8').'" style="width: 200px;" /></td></tr>

					<tr><td width="215" style="height: 35px;">'.$this->l('N&uacute;mero de comercio (FUC)').'</td><td><input type="text" name="codigo" value="'.Tools::getValue('codigo', $this->codigo).'" style="width: 200px;" /></td></tr>

					<tr><td width="215" style="height: 35px;">'.$this->l('N&uacute;mero de terminal').'</td><td><input type="text" name="terminal" value="'.Tools::getValue('terminal', $this->terminal).'" style="width: 80px;" /></td></tr>

					<tr><td width="215" style="height: 35px;">'.$this->l('Tipo de firma').'</td><td><input type="radio" name="tipofirma" id="tipofirma_c" value="1"'.$tipofirma_c.'/>'.$this->l('Completa').'<input type="radio" name="tipofirma" id="tipofirma_a" value="0"'.$tipofirma_a.'/>'.$this->l('Ampliada').'</td></tr>

					<tr><td width="215" style="height: 35px;">'.$this->l('Tipo de moneda').'</td><td><select name="moneda" style="width: 80px;"><option value=""></option><option value="978"'.$iseuro.'>EURO</option><option value="840"'.$isdollar.'>DOLLAR</option></select></td></tr>

					<tr><td width="215" style="height: 35px;">'.$this->l('Tipo de transacci&oacute;n').'</td><td><input type="text" name="trans" value="'.Tools::getValue('trans', $this->trans).'" style="width: 80px;" /></td></tr>

					</td></tr>

				</table>

			</fieldset>

			<br>

			<fieldset>

			<legend><img src="../img/admin/cog.gif" />'.$this->l('Personalizaci&oacute;n').'</legend>

			<table border="0" width="680" cellpadding="0" cellspacing="0" id="form">

		<tr>

			<td colspan="2">'.$this->l('Por favor completa los datos adicionales.').'.<br /><br /></td>

		</tr>

		<tr>

					<td width="215" style="height: 35px;">'.$this->l('Notificaci&oacute;n HTTP (Inactivo no procesa pedido ni vacia el carrito)').'</td>

						<td><input type="radio" name="notificacion" id="notificacion_1" value="1"'.$notificacion_s.'/><img src="../img/admin/enabled.gif"		alt="'.$this->l('Activado').'" title="'.$this->l('Activado').'" /><input type="radio" name="notificacion" id="notificacion_0" value="0"'.$notificacion_n.'/><img src="../img/admin/disabled.gif" alt="'.$this->l('Desactivado').'" title="'.$this->l('Desactivado').'" />

					</td>

				</tr>

		<tr>

		<td width="215" style="height: 35px;">'.$this->l('SSL en URL de validaci&oacute;n').'</td>

			<td>

			<input type="radio" name="ssl" id="ssl_1" value="si" '.$ssl_si.'/>

			<img src="../img/admin/enabled.gif" alt="'.$this->l('Activado').'" title="'.$this->l('Activado').'" />

			<input type="radio" name="ssl" id="ssl_0" value="no" '.$ssl_no.'/>

			<img src="../img/admin/disabled.gif" alt="'.$this->l('Desactivado').'" title="'.$this->l('Desactivado').'" />

			</td>

		</tr>

		<tr>

		<td width="215" style="height: 35px;">'.$this->l('En caso de error, permitir elegir otro medio de pago').'</td>

			<td>

			<input type="radio" name="error_pago" id="error_pago_1" value="si" '.$error_pago_si.'/>

			<img src="../img/admin/enabled.gif" alt="'.$this->l('Activado').'" title="'.$this->l('Activado').'" />

			<input type="radio" name="error_pago" id="error_pago_0" value="no" '.$error_pago_no.'/>

			<img src="../img/admin/disabled.gif" alt="'.$this->l('Desactivado').'" title="'.$this->l('Desactivado').'" />

			</td>

		</tr>

		<tr>

		<td width="215" style="height: 35px;">'.$this->l('Activar los idiomas en el TPV').'</td>

			<td>

			<input type="radio" name="idiomas_estado" id="idiomas_estado_si" value="si" '.$idiomas_estado_si.'/>

			<img src="../img/admin/enabled.gif" alt="'.$this->l('Activado').'" title="'.$this->l('Activado').'" />

			<input type="radio" name="idiomas_estado" id="idiomas_estado_no" value="no" '.$idiomas_estado_no.'/>

			<img src="../img/admin/disabled.gif" alt="'.$this->l('Desactivado').'" title="'.$this->l('Desactivado').'" />

			</td>

		</tr>

		</table>

			</fieldset>

			<br>

		<input class="button" name="btnSubmit" value="'.$this->l('Guardar configuraci&oacute;n').'" type="submit" />

		</form>';

	}



	public function getContent()

	{

	    // Recoger datos

		$this->_html = '<h2>'.$this->displayName.'</h2>';

		if (!empty($_POST))

		{

			$this->_postValidation();

			if (!sizeof($this->_postErrors))

				$this->_postProcess();

			else

				foreach ($this->_postErrors AS $err)

					$this->_html .= '<div class="alert error">'. $err .'</div>';

		}

		else

			$this->_html .= '<br />';

		$this->_displayservired();

		$this->_displayForm();

		return $this->_html;

	}



	public function hookPayment($params)

	{

		// Variables necesarias de fuera

		global $smarty, $cookie, $cart;



		// Valor de compra

		$id_currency = intval(Configuration::get('PS_CURRENCY_DEFAULT'));

		$currency = new Currency(intval($id_currency));

		$cantidad = number_format(Tools::convertPrice($params['cart']->getOrderTotal(true, 3), $currency), 2, '.', '');

		$cantidad = str_replace('.','',$cantidad);



		// El número de pedido es  los 8 ultimos digitos del ID del carrito + el tiempo MMSS.

		$numpedido = str_pad($params['cart']->id, 8, "0", STR_PAD_LEFT) . date(is);



		$codigo = Tools::getValue('codigo', $this->codigo);

		$moneda = Tools::getValue('moneda', $this->moneda);

		$trans = Tools::getValue('trans', $this->trans);



		$ssl = Tools::getValue('ssl', $this->ssl);

		if ($ssl=='no')

		$urltienda = 'http://'.$_SERVER['HTTP_HOST'].__PS_BASE_URI__.'modules/servired/respuesta_tpv.php';

		elseif($ssl=='si')

		$urltienda = 'https://'.$_SERVER['HTTP_HOST'].__PS_BASE_URI__.'modules/servired/respuesta_tpv.php';

		else

		$urltienda = 'ninguna';



		$clave = Tools::getValue('clave', $this->clave);



		// Cálculo del SHA1 $trans . $urltienda

		if(Tools::getValue('tipofirma', $this->tipofirma))

			$mensaje = $cantidad . $numpedido . $codigo . $moneda . $clave;

		else

			$mensaje = $cantidad . $numpedido . $codigo . $moneda . $trans . $urltienda . $clave;

		$firma = strtoupper(sha1($mensaje));



		$products = $params['cart']->getProducts();

		$productos = '';

		$id_cart = intval($params['cart']->id);



		//Activación de los idiomas del TPV

		$idiomas_estado = Tools::getValue('idiomas_estado', $this->idiomas_estado);

		if ($idiomas_estado==si){

			$ps_language = new Language(intval($cookie->id_lang));

			$idioma_web = $ps_language->iso_code;

			switch ($idioma_web) {

				case 'es':

				$idioma_tpv='001';

				break;

				case 'en':

				$idioma_tpv='002';

				break;

				case 'ca':

				$idioma_tpv='003';

				break;

				case 'fr':

				$idioma_tpv='004';

				break;

				case 'de':

				$idioma_tpv='005';

				break;

				case 'nl':

				$idioma_tpv='006';

				break;

				case 'it':

				$idioma_tpv='007';

				break;

				case 'sv':

				$idioma_tpv='008';

				break;

				case 'pt':

				$idioma_tpv='009';

				break;

				case 'pl':

				$idioma_tpv='011';

				break;

				case 'gl':

				$idioma_tpv='012';

				break;

				case 'eu':

				$idioma_tpv='013';

				break;

				default:

				$idioma_tpv='002';

			}

		}

		else {

			$idioma_tpv = '0';

		}



		foreach ($products as $product) {

			$productos .= $product['quantity'].' '.$product['name']."<br>";

		}

		$customer = new Customer((int)($cart->id_customer));

		$smarty->assign(array(

			'urltpv' => Tools::getValue('urltpv', $this->urltpv),

			'cantidad' => $cantidad,

			'moneda' => $moneda,

			'pedido' => $numpedido,

			'codigo' => $codigo,

			'terminal' => Tools::getValue('terminal', $this->terminal),

			'trans' => $trans,

			'titular' => ($cookie->logged ? $cookie->customer_firstname.' '.$cookie->customer_lastname : false),

            'nombre' => Tools::getValue('nombre', $this->nombre),

			'urltienda' => $urltienda,

			'notificacion' => Tools::getValue('notificacion', $this->notificacion),

			'productos' => $productos,

			'UrlOk' => 'http://'.$_SERVER['HTTP_HOST'].__PS_BASE_URI__.'order-confirmation.php?key='.$customer->secure_key.'&id_cart='. $id_cart .'&id_module='.(int)($this->id).'&id_order='.(int)($numpedido),

			'UrlKO' => 'http://'.$_SERVER['HTTP_HOST'].__PS_BASE_URI__.'modules/servired/pago_error.php',

			'firma' => $firma,

			'idioma_tpv' => $idioma_tpv,

			'this_path' => $this->_path

		));

		return $this->display(__FILE__, 'servired.tpl');

    }

    public function hookPaymentReturn($params)

	{

		if (!$this->active)

			return ;

		global $smarty;

		return $this->display(__FILE__, 'pago_correcto.tpl');

	}

}

?>