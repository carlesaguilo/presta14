<!-- TM Categories -->
<script type="text/javascript" src="{$module_dir}superfish.js"></script>
{literal}
<script type="text/javascript">
$(document).ready(function() {
$('ul.sf-menu').superfish({
delay: 1000,
animation: {opacity:'show',height:'show'},
speed: 'fast',
autoArrows: false,
dropShadows: false
});
});
</script>
{/literal}
<div class="clearblock"></div>
<div id="tmcategories">
	
	<ul id="cat" class="sf-menu">
						<li class="">
	<a href="/" >Inicio</a>
	</li>								<li class="">
	<a href="/cms.php?id_cms=4" >{l s='About' mod='tmcategories'}</a>
	</li>								<li class="">
	<a href="/cms.php?id_cms=1" >{l s='Inciense' mod='tmcategories'}</a>
	</li>								<li class="">
	<a href="/cms.php?id_cms=2" >{l s='Philosophy' mod='tmcategories'}</a>
	</li>								<li class="">
	<a href="/category.php?id_category=45">{l s='Catalog' mod='tmcategories'}</a>
	</li>								<li class="">
	<a href="/contact-form.php?fid=4" >{l s='Contact' mod='tmcategories'}</a>
    </li>							   	
    <li class="sub">
	<a href="">{l s='Where to Buy' mod='tmcategories'}</a>
			<ul class="subcat1">
					<li  class="noborder">
				<a href="/contact-form.php?fid=5">{l s='Point of Sale' mod='tmcategories'}</a>
							</li>	
                    <li  class="noborder">
				<a href="/cms.php?id_cms=7">{l s='E-Commerce' mod='tmcategories'}</a>
							</li>
             </ul>							
    </ul>
</div>
<!-- /TM Categories -->