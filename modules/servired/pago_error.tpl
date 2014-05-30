{capture name=path}{l s='Payment ERROR' mod='servired'}{/capture}
{include file="$tpl_dir./breadcrumb.tpl"}
<div class="cms"  style="min-height: 100px; margin-top: 22px;">
<img src="{$this_path}modules/servired/error.gif" alt="Error in payment" /> 
<h2>{l s='Your credit card payment could not be accomplished' mod='servired'}</h2> 
<p>
{l s='We are sorry, but your payment has not been successfully accomplished. You can try again or choose another payment method. Remember that you can only use Visa and Mastercard credit cards, and Maestro debit cards as well (Spain only).' mod='servired'}
</p>
 
 
<p>{l s='There are several reasons for this to happen:' mod='servired'}</p>
	<ul>
		<li>{l s='You mistook any of the digits of your credit card. Make sure you introduce them well.' mod='servired'}</li>
		<li>{l s='Make sure your credit card has not expired and is valid. Maestro debit cards, for example, are only valid in Spain' mod='servired'}</li>
		<li>{l s='There has been a problem with our payment gateway provider.' mod='servired'}</li>
	</ul>
 
 
 
<p>{l s='In any case, you can contact us by mail or by phone and we will try to fix your problem together.' mod='servired'}</p>
 

<a href="{$base_dir_ssl}order.php?step=3" title="{l s='Pagos'}" class="pago_error">{l s='Try again' mod='servired'}</a>

</div>