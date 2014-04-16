<?php
include(dirname(__FILE__).'/../../config/config.inc.php');
require_once(dirname(__FILE__).'/../../init.php');

// Get data
$number = (intval(Tools::getValue('n')) ? intval(Tools::getValue('n')) : 10);
$orderByValues = array(0 => 'name', 1 => 'price', 2 => 'date_add', 3 => 'date_upd', 4 => 'position', 5 => 'manufacturer_name', 6 => 'quantity');
$orderWayValues = array(0 => 'ASC', 1 => 'DESC');
$orderBy = Tools::strtolower(Tools::getValue('orderby', $orderByValues[intval(Configuration::get('PS_PRODUCTS_ORDER_BY'))]));
$orderWay = Tools::strtoupper(Tools::getValue('orderway', $orderWayValues[intval(Configuration::get('PS_PRODUCTS_ORDER_WAY'))]));
if (!in_array($orderBy, $orderByValues))
	$orderBy = $orderByValues[0];
if (!in_array($orderWay, $orderWayValues))
	$orderWay = $orderWayValues[0];
$id_category = (intval(Tools::getValue('id_category')) ? intval(Tools::getValue('id_category')) : 1);
$products = Product::getProducts(intval($cookie->id_lang), 0, ($number > 10 ? $number : $number), $orderBy, $orderWay, $id_category, true);
$currency = new Currency(intval($cookie->id_currency));
$affiliate = (Tools::getValue('ac') ? '?ac='.intval(Tools::getValue('ac')) : '');

// Send feed
header("Content-Type:text/xml; charset=utf-8");
echo '<?xml version="1.0" encoding="UTF-8"?>'."\n";
?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
	<channel>
		<title>Tienda de Incienso - Incienso & Incense</title>
		<link>http://www.incienso-incense.com</link>
		<generator>incienso-incense.com</generator>
		<description>ncense & Incienso somo una empresa de importación y distribución de inciensos naturales que lleva más de una década trabajando con India.</description>
		<language>es</language>
		<atom:link href="http://www.incienso-incense.com/modules/feeder/rss.php" rel="self" type="application/rss+xml" />
		<image>
			<title>Tienda de Incienso - Incienso & Incense</title>	
			<url>http://www.incienso-incense.com/img/logo.jpg</url>
			<link>http://www.incienso-incense.com</link>
		</image>
<?php
	foreach ($products AS $product)
	{
		$image = Image::getImages(intval($cookie->id_lang), $product['id_product']);
		echo "\t\t<item>\n";
		echo "\t\t\t<title><![CDATA[".$product['name']." - ".html_entity_decode(Tools::displayPrice(Product::getPriceStatic($product['id_product']), $currency), ENT_COMPAT, 'UTF-8')." ]]></title>\n";
		echo "\t\t\t<description>";
		$cdata = true;
		if (is_array($image) AND sizeof($image))
		{
			echo "<![CDATA[<img src='"."http://www.incienso-incense.com/img/p/".$image[0]['id_product']."-".$image[0]['id_image']."-small.jpg' title='".str_replace('&', '', $product['name'])."' alt='thumb' />";
			$cdata = false;
		}
		if ($cdata)
			echo "<![CDATA[";
		echo $product['description_short']."]]></description>\n";
		
		echo "\t\t\t<link><![CDATA[http://www.incienso-incense.com".htmlspecialchars($link->getproductLink($product['id_product'], $product['link_rewrite'], Category::getLinkRewrite(intval($product['id_category_default']), $cookie->id_lang))).$affiliate."]]></link>\n";
		echo "\t\t\t<guid><![CDATA[http://www.incienso-incense.com".htmlspecialchars($link->getproductLink($product['id_product'], $product['link_rewrite']
, Category::getLinkRewrite(intval($product['id_category_default']), $cookie->id_lang))).$affiliate."]]></guid>\n";
                
		echo "\t\t</item>\n";
	}
?>
	</channel>
</rss>
