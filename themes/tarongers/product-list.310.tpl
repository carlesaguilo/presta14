{if isset($products)}
<div id="product_list_grid" class="bordercolor box visible">
<ul>
	{foreach from=$products item=product name=products}
	<li class="ajax_block_product bordercolor">
		<a href="{$product.link|escape:'htmlall':'UTF-8'}" class="product_img_link" title="{$product.name|escape:'htmlall':'UTF-8'}"><img src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home')}" alt="{$product.legend|escape:'htmlall':'UTF-8'}" {if isset($homeSize)} width="{$homeSize.width}" height="{$homeSize.height}"{/if} /></a>
		<h3><a class="product_link" href="{$product.link|escape:'htmlall':'UTF-8'}" title="{$product.name|escape:'htmlall':'UTF-8'}">{$product.name|truncate:30:'...'|escape:'htmlall':'UTF-8'}</a></h3>		
		{if (!$PS_CATALOG_MODE AND ((isset($product.show_price) && $product.show_price) || (isset($product.available_for_order) && $product.available_for_order)))}
			{if isset($product.show_price) && $product.show_price && !isset($restricted_country_mode)}<span class="price">{if !$priceDisplay}{convertPrice price=$product.price}{else}{convertPrice price=$product.price_tax_exc}{/if}</span>{/if}
		{/if}
	</li>
	{/foreach}
</ul>
</div>
<div id="product_list_list" class="box">
<ul class="bordercolor">
	{foreach from=$products item=product name=products}
	<li class="ajax_block_product bordercolor">
		<a href="{$product.link|escape:'htmlall':'UTF-8'}" class="product_img_link" title="{$product.name|escape:'htmlall':'UTF-8'}"><img src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home')}" alt="{$product.legend|escape:'htmlall':'UTF-8'}" {if isset($homeSize)} width="{$homeSize.width}" height="{$homeSize.height}"{/if} /></a>
		<div class="center_block">
			<div class="product_flags">
				{if isset($product.new) && $product.new == 1}<span class="new">{l s='New'}</span>{/if}
				{if isset($product.available_for_order) && $product.available_for_order && !isset($restricted_country_mode)}{if ($product.allow_oosp || $product.quantity > 0)}<span class="availability bordercolor">{l s='Available'}{elseif (isset($product.quantity_all_versions) && $product.quantity_all_versions > 0)}<span class="bordercolor">{l s='Product available with different options'}</span>{else}<span class="bordercolor">{l s='Out of stock'}</span>{/if}</span>{/if}
				{if isset($product.online_only) && $product.online_only}<span class="online_only bordercolor">{l s='Online only!'}</span>{/if}
			</div>
			<h3><a class="product_link" href="{$product.link|escape:'htmlall':'UTF-8'}" title="{$product.name|escape:'htmlall':'UTF-8'}">{$product.name|truncate:35:'...'|escape:'htmlall':'UTF-8'}</a></h3>
			<p class="product_desc"><a class="product_descr" href="{$product.link|escape:'htmlall':'UTF-8'}" title="{$product.description_short|truncate:360:'...'|strip_tags:'UTF-8'|escape:'htmlall':'UTF-8'}">{$product.description_short|truncate:400:'...'|strip_tags:'UTF-8'}</a></p>
		</div>																				 
		<div class="right_block">
			{if isset($product.on_sale) && $product.on_sale && isset($product.show_price) && $product.show_price && !$PS_CATALOG_MODE}<span class="on_sale">{l s='On sale!'}</span>
			{elseif isset($product.reduction) && $product.reduction && isset($product.show_price) && $product.show_price && !$PS_CATALOG_MODE}<span class="discount">{l s='Reduced price!'}</span>
			{/if}
			{if (!$PS_CATALOG_MODE AND ((isset($product.show_price) && $product.show_price) || (isset($product.available_for_order) && $product.available_for_order)))}
				{if isset($product.show_price) && $product.show_price && !isset($restricted_country_mode)}<span class="price">{if !$priceDisplay}{convertPrice price=$product.price}{else}{convertPrice price=$product.price_tax_exc}{/if}</span>{/if}
			{/if}
			{if ($product.id_product_attribute == 0 || (isset($add_prod_display) && ($add_prod_display == 1))) && $product.available_for_order && !isset($restricted_country_mode) && $product.minimal_quantity <= 1 && $product.customizable != 2 && !$PS_CATALOG_MODE}
				{if ($product.allow_oosp || $product.quantity > 0)}
{l s='Quantity :'}
    <input type="text" name="ajax_qty_to_add_to_cart[{$product.id_product|intval}]" id="quantity_wanted_{$product.id_product|intval}" class="text" value="{if isset($quantityBackup)}{$quantityBackup|intval}{else}1{/if}" size="2" maxlength="3" /> 

					<a class="exclusive ajax_add_to_cart_button" rel="ajax_id_product_{$product.id_product|intval}" href="{$link->getPageLink('cart.php')}?add&amp;id_product={$product.id_product|intval}{if isset($static_token)}&amp;token={$static_token}{/if}" title="{l s='Add to cart'}">{l s='Add to cart'}</a>
				{else}
					<span class="exclusive">{l s='Add to cart'}</span>
				{/if}
			{/if}
			<a class="button" href="{$product.link|escape:'htmlall':'UTF-8'}" title="{l s='View'}">{l s='View'}</a>
			{if isset($comparator_max_item) && $comparator_max_item}
				<p class="compare checkbox"><input type="checkbox" class="comparator" id="comparator_item_{$product.id_product}" value="comparator_item_{$product.id_product}" {if isset($compareProducts) && in_array($product.id_product, $compareProducts)}checked{/if}/> <label for="comparator_item_{$product.id_product}">{l s='Select to compare'}</label></p>
			{/if}				
		</div>
	</li>
	{/foreach}
</ul>
{if $comparator_max_item}
	<script type="text/javascript">
	// <![CDATA[
		var min_item = '{l s='Please select at least one product.' js=1}';
		var max_item = "{l s='You cannot add more than' js=1} {$comparator_max_item} {l s='product(s) in the product comparator' js=1}";
	//]]>
	</script>
	<form class="product_compare" method="get" action="{$link->getPageLink('products-comparison.php')}" onsubmit="true">
		<input type="submit" class="button" value="{l s='Compare'}" />
		<input type="hidden" name="compare_product_list" class="compare_product_list" value="" />
	</form>
{/if}
</div>
{/if}