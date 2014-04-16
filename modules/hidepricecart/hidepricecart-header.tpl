{if $cookie->isLogged()}
{else}
<script type="text/javascript">
$(document).ready(function() {ldelim}
	
$.jGrowl('{l s='Please register or login to see the prices' mod='hidepricecart'} <br> <br> {l s=' Regístrate y conoce nuestras tarifas' mod='hidepricecart'}', {literal}{ life: 99500 }{/literal});

{rdelim});
</script>
{/if}