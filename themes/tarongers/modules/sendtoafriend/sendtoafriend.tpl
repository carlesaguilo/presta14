{capture name=path}{l s='Send to a friend' mod='sendtoafriend'}{/capture}
{include file="$tpl_dir./breadcrumb.tpl"}
<h1>{l s='Send to a friend' mod='sendtoafriend'}</h1>
<p class="bold">{l s='Send this page to a friend who might be interested in the item below.' mod='sendtoafriend'}.</p>
{include file="$tpl_dir./errors.tpl"}
{if isset($smarty.get.submited)}
	<p class="success">{l s='Your email has been sent successfully' mod='sendtoafriend'}</p>
{else}
	<form method="post" action="{$request_uri}" class="std sendtoafriend">
		<fieldset>
			<h3>{l s='Send a message' mod='sendtoafriend'}</h3>
			<div class="sendtoafriend_product">
				<a href="{$productLink}"><img src="{$link->getImageLink($product->link_rewrite, $cover.id_image, 'home')}" alt="" title="{$cover.legend|escape:'htmlall':'UTF-8'}" /></a>
				<h4><a class="product_link" href="{$productLink}">{$product->name|escape:'htmlall':'UTF-8'}</a></h4>
			</div>
			<p class="text">
				<label for="friend-name">{l s='Friend\'s name:' mod='sendtoafriend'}</label>
				<input type="text" id="friend-name" name="name" value="{if isset($smarty.post.name)}{$smarty.post.name|escape:'htmlall':'UTF-8'|stripslashes}{/if}" />
			</p>
			<p class="text">
				<label for="friend-address">{l s='Friend\'s email:' mod='sendtoafriend'}</label>
				<input type="text" id="friend-address" name="email" value="{if isset($smarty.post.name)}{$smarty.post.email|escape:'htmlall'|stripslashes}{/if}" />
			</p>
			<p class="submit">
				<input type="submit" name="submitAddtoafriend" value="{l s='send' mod='sendtoafriend'}" class="button" />
			</p>
		</fieldset>
	</form>
{/if}
<ul class="footer_links">
	<li><a href="{$productLink}"><img src="{$img_dir}icon/return.png" alt="" class="icon" /></a><a href="{$productLink}">{l s='Back to product page' mod='sendtoafriend'}</a></li>
</ul>