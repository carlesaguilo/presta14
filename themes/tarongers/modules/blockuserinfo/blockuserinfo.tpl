<div class="headerTop">
	<!-- Block user information module HEADER -->
	
	<ul id="header_user">
		<li id="header_user_info">
			{l s='Hi' mod='blockuserinfo'},
			{if $cookie->isLogged()}
				<span>{$cookie->customer_firstname} {$cookie->customer_lastname}</span>
				(&nbsp;<a href="{$link->getPageLink('index.php')}?mylogout" title="{l s='Log me out' mod='blockuserinfo'}">{l s='Log out' mod='blockuserinfo'}</a>&nbsp;)
			{else}
				(&nbsp;<a href="{$link->getPageLink('my-account.php', true)}">{l s='Log in' mod='blockuserinfo'}</a>&nbsp;)
			{/if}
		</li>
		<li id="your_account"><a href="{$link->getPageLink('my-account.php', true)}" title="{l s='Your Account' mod='blockuserinfo'}">{l s='Your Account' mod='blockuserinfo'}</a></li>
	</ul>
	<div id="shopping_cart">
		{if !$PS_CATALOG_MODE}
		<a href="{$link->getPageLink("$order_process.php", true)}" title="{l s='Your Shopping Cart' mod='blockuserinfo'}"><strong>{l s='Cart:' mod='blockuserinfo'}</strong></a>
		<span class="ajax_cart_quantity{if $cart_qties == 0} hidden{/if}">{$cart_qties}</span>
		<span class="ajax_cart_product_txt{if $cart_qties != 1} hidden{/if}">{l s='product' mod='blockuserinfo'}</span>
		<span class="ajax_cart_product_txt_s{if $cart_qties < 2} hidden{/if}">{l s='products' mod='blockuserinfo'}</span>
		<span class="ajax_cart_no_product{if $cart_qties > 0} hidden{/if}">{l s='(empty)' mod='blockuserinfo'}</span>
		{/if}
	</div>
	<!-- /Block user information module HEADER -->
</div>