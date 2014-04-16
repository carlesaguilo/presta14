<!-- Block Viewed products -->
<div id="viewed-products_block_left" class="block products_block">
	<h4>{l s='Viewed products' mod='blockviewed'}</h4>
	<div class="block_content">
		<ul class="products clearfix">
			{foreach from=$productsViewedObj item=viewedProduct name=myLoop}
				<li class="bordercolor">
					<a class="products_block_img bordercolor" href="{$link->getProductLink($viewedProduct->id, $viewedProduct->link_rewrite, $viewedProduct->category_rewrite)}" title="{l s='More about' mod='blockviewed'} {$viewedProduct->name|escape:html:'UTF-8'}"><img src="{$link->getImageLink($viewedProduct->link_rewrite, $viewedProduct->cover, 'small')}" alt="{$viewedProduct->legend|escape:html:'UTF-8'}" /></a>
					<div>
						<h5><a class="product_link" href="{$link->getProductLink($viewedProduct->id, $viewedProduct->link_rewrite, $viewedProduct->category_rewrite)}" title="{l s='More about' mod='blockviewed'} {$viewedProduct->name|escape:html:'UTF-8'}">{$viewedProduct->name|escape:html:'UTF-8'|truncate:25:'...'}</a></h5>
						<p><a class="product_descr" href="{$link->getProductLink($viewedProduct->id, $viewedProduct->link_rewrite, $viewedProduct->category_rewrite)}" title="{l s='More about' mod='blockviewed'} {$viewedProduct->name|escape:html:'UTF-8'}">{$viewedProduct->description_short|strip_tags:'UTF-8'|truncate:30:'...'}</a></p>
					</div>
				</li>
			{/foreach}
		</ul>
	</div>
</div>
