<!-- MODULE Block footer -->
<ul id="footer_links">
	<!-- {if !$PS_CATALOG_MODE}<li class="first_item"><a href="{$link->getPageLink('prices-drop.php')}" title="{l s='Specials' mod='blockcms'}">{l s='' mod='blockcms'}</a></li>{/if}
    <li class="{if $PS_CATALOG_MODE}first_{/if}item"><a href="{$link->getPageLink('new-products.php')}" title="{l s='New products' mod='blockcms'}">{l s='New products' mod='blockcms'}</a></li>

	{if !$PS_CATALOG_MODE}<li class="item"><a href="{$link->getPageLink('best-sales.php')}" title="{l s='Top sellers' mod='blockcms'}">{l s='' mod='blockcms'}</a></li>{/if} -->
	
	{if $display_stores_footer}<li class="item"><a href="{$link->getPageLink('stores.php')}" title="{l s='Our stores' mod='blockcms'}">{l s='Our stores' mod='blockcms'}</a></li>{/if}
	
	<!--<li class="item"><a href="{$link->getPageLink('contact-form.php', true)}" title="{l s='Contact us' mod='blockcms'}">{l s='Contact us' mod='blockcms'}</a></li>-->
	
	{foreach from=$cmslinks item=cmslink}
		{if $cmslink.meta_title != ''}
			<li class="item"><a href="{$cmslink.link|addslashes}" title="{$cmslink.meta_title|escape:'htmlall':'UTF-8'}">{$cmslink.meta_title|escape:'htmlall':'UTF-8'}</a></li>
		{/if}
	{/foreach}
	
<!--    <li class="item"><a href="{$link->getPageLink('sitemap.php')}"{if $page_name == 'sitemap'} class="active"{/if}>{l s='Sitemap' mod='blockcms'}</a></li> -->
  
</ul>

<ul class="extraLinks">
  	
  	<li class="item"><a href="http://www.qweb.es/empresas-de-productos-naturales.html" target="_blank" title="Directorio de Empresas de Productos naturales"> <img src="http://www.qweb.es/certqweb-www.incienso-incense.com.gif" width="97" height="31" border="0" align="absmiddle" alt="Directorio de Empresas de Productos naturales" /> </a></li>
  	
	{if $display_poweredby}
		<!--<li class="last_item"><a href="{$link->getPageLink('index.php')}" class="footer_logo"><img class="logo" src="{$img_dir}logo_footer.png?{$img_update_time}" alt="{$shop_name|escape:'htmlall':'UTF-8'}" /></a> &copy; {$smarty.now|date_format:"%Y"} </li>
		<li>Developed by <a href="mailto:carles.aguilo@gmail.com">Carles Aguiló</a> & <a href="http://www.carmepujol.eu" target="_blank">Carme Pujol</a></li>-->
        <li class="last_item"><a href="{$link->getPageLink('index.php')}" class="footer_logo">H&B Incienso Natural</a> &copy; {$smarty.now|date_format:"%Y"} </li>
		<li>Web: <a href="mailto:carles.aguilo@gmail.com">Carles Aguiló</a> & <a href="http://www.carmepujol.eu" target="_blank">Carme Pujol</a></li>
	{/if}
</ul>
<!-- /MODULE Block footer -->