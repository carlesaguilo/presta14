<h2>{l s='Customer Options' mod='megacustomers'}</h2>
		<div style="width:100%">
        <br />
        <span>{l s='Apply Equivalence Tax' mod='megacustomers'}</span>
		<form class="std" method="POST" ><fieldset>
		<p class="radio" style="padding-left:10px;">	
					<input type="radio" name="id_equiv" id="id_equiv_on" value="1" {if $equivalence eq 1}checked="checked"{/if}/>
					<label class="t" for="id_equiv_on"> <img src="../../img/admin/enabled.gif" alt="{l s='Enabled' mod='megacustomers'}" title="{l s='Enabled' mod='megacustomers'}" /></label>
					<input type="radio" name="id_equiv" id="id_equiv_off" value="0" {if $equivalence eq 0}checked="checked{/if}/>
					<label class="t" for="id_equiv_off"> <img src="../../img/admin/disabled.gif" alt="{l s='Disabled' mod='megacustomers'}" title="{l s='Disabled' mod='megacustomers'}" /></label>
				</p>
                <br>
					<div class="margin-form">
						<input type="submit" class="button" name="submitEquivalence" value="{l s='Edit' mod='megacustomers'}" />
						
					</div>
			</fieldset></form></div>