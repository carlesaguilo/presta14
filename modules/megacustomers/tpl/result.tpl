{if $active}
<h2>{l s='Active Account Correctly' mod='megacustomers'}</h2>
{/if}
	{foreach from=$errors item=error name=byerror}
	<h4>{$error}</h4>
	{/foreach}