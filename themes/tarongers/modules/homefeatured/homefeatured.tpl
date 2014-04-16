<!-- MODULE Home Featured Products -->
<div id="featured_products">
	<h4>{l s='Featured products' mod='homefeatured'}</h4>
	{if isset($products) AND $products}
	<div class="block_content">
		<ul>
			{foreach from=$products item=product name=homeFeaturedProducts}
			<li class="ajax_block_product">
				<a class="product_image" href="{$product.link}" title="{$product.name|escape:html:'UTF-8'}"><img src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home')}" alt="{$product.name|escape:html:'UTF-8'}" /></a>
				<div>
					<h5><a class="product_link" href="{$product.link}" title="{$product.name|truncate:32:'...'|escape:'htmlall':'UTF-8'}">{$product.name|truncate:25:'...'|escape:'htmlall':'UTF-8'}</a></h5>
					<p class="product_desc"><a class="product_descr" href="{$product.link}" title="{l s='More' mod='homefeatured'}">{$product.description_short|strip_tags|truncate:100:'...'}</a></p>
					<span class="price">{if $product.show_price AND !isset($restricted_country_mode) AND !$PS_CATALOG_MODE}{if !$priceDisplay}{convertPrice price=$product.price}{else}{convertPrice price=$product.price_tax_exc}{/if}{/if}</span>
					<a class="button" href="{$product.link}" title="{l s='View' mod='homefeatured'}">{l s='View' mod='homefeatured'}</a>
					{if ($product.id_product_attribute == 0 OR (isset($add_prod_display) AND ($add_prod_display == 1))) AND $product.available_for_order AND !isset($restricted_country_mode) AND $product.minimal_quantity == 1 AND $product.customizable != 2 AND !$PS_CATALOG_MODE}
						{if ($product.quantity > 0 OR $product.allow_oosp)}
						<a class="exclusive ajax_add_to_cart_button" rel="ajax_id_product_{$product.id_product}" href="{$link->getPageLink('cart.php')}?qty=1&amp;id_product={$product.id_product}&amp;token={$static_token}&amp;add" title="{l s='Add to cart' mod='homefeatured'}">{l s='Add to cart' mod='homefeatured'}</a>
						{else}
						<span class="exclusive">{l s='Add to cart' mod='homefeatured'}</span>
						{/if}
					{/if}
				</div>
			</li>
			{/foreach}
		</ul>
	</div>
	{else}
	<p>{l s='No featured products' mod='homefeatured'}</p>
	{/if}
</div>
<!-- /MODULE Home Featured Products -->