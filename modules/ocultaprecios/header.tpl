{if $cookie->isLogged()}
{else}
    <link rel="stylesheet" type="text/css" href="{$module_dir}css/css.css" />
  	
  
    <script type="text/javascript">
  	
	
    $(document).ready(function($) {literal}{{/literal}
    $('.price_container').remove();
    $('.ajax_cart_total').remove();
    $('#old_price_display').remove();
    $('#old_price').remove();
    $('#reduction_percent').remove();
    $('.price-discount').remove();
    $('.reduction').remove();
    $('.price').remove();
    $('#add_to_cart .exclusive').remove();
    $('#product_list .ajax_add_to_cart_button').remove();
    $('#featured-products_block_center .ajax_add_to_cart_button').remove();
    
    $('#product_list .button').before('<div class="registreselistado">{$texto}</div>');
	$('#buy_block #add_to_cart').before('<div class="registreseproducto">{$texto}</div>');
	$('#featured-products_block_center .product_image').after('<div class="registresehome">{$texto}</div>');
	$('#order-detail-content').after('<div class="registresecarrito">{$texto}</div>');
    
   {literal}}{/literal});

</script>


{/if}