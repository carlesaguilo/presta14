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
<nav id="tmcategories">
	
	<ul id="cat" class="sf-menu">
		<!-- Hide HOME for later use ###
     	<li class="">
	 	<a href="/" >Inicio</a>
		</li>			 ### -->					
		<li {if ($smarty.get.id_cms == 4)} class="current" {/if}>
			<a href="cms.php?id_cms=4" >{l s='H&B' mod='tmcategories'}</a>
		</li>
        <li {if ($smarty.get.id_cms == 2)} class="current" {/if}>
			<a href="cms.php?id_cms=2" >{l s='Philosophy' mod='tmcategories'}</a>
		</li>
        <li {if $page_name == "category"} class="current"{/if}>
			<a href="category.php?id_category=45">{l s='Incenses' mod='tmcategories'}</a>
		</li>	
        <!--<li {if $page_name == "my-account"} class="current"{/if}>
			<a href="authentication.php?back=my-account.php">{l s='Buy' mod='tmcategories'}</a>
		</li>-->							
		<li {if ($smarty.get.id_cms == 11)} class="current" {/if}>
			<a href="cms.php?id_cms=11" >{l s='Point of Sale' mod='tmcategories'}</a>
		</li>	
        <li {if ($smarty.get.id_cms == 9)} class="current" {/if}>
			<a href="cms.php?id_cms=9" >{l s='We recommend' mod='tmcategories'}</a>
		</li>																							
		<li {if ($page_name == '')} class="current" {/if}>
			<a href="contact-form.php?fid=4" >{l s='Contact' mod='tmcategories'}</a>
	    </li>
        
        {if $logged}
            <li {if ($page_name == 'my-account') || ($page_name == 'history') || ($page_name == 'order-slip') || ($page_name == 'addresses') || ($page_name == 'address') || ($page_name == 'identity') || ($page_name == 'discount') || ($page_name == 'order')} class="current" {/if}>
                <a href="my-account.php" >{l s='My account' mod='tmcategories'}</a> 
            </li>
        {else}
            <li {if ($page_name == 'authentication')} class="current" {/if}>
            	<a href="authentication.php?back=my-account.php" >{l s='Log in' mod='tmcategories'}</a>   
            </li>
        {/if}							   	
	    <!--<li class="sub">
		<a href="">{l s='Where to Buy' mod='tmcategories'}</a>
				<ul class="subcat1">
					<li>
						<a href="/contact-form.php?fid=5">{l s='Point of Sale' mod='tmcategories'}</a>
					</li>	
	                <li>
						<a href="/cms.php?id_cms=7">{l s='E-Commerce' mod='tmcategories'}</a>
					</li>
	             </ul>		
	    </li>-->	
    </ul>
</nav>
<!-- /TM Categories -->