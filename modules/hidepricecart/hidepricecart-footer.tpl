
{if $cookie->isLogged()}
{else}  
    {if $priceh == ""}
   {else}



   <style type="text/css">
	 {$priceh}{ display:none; visibility:hidden}

	</style>
    
   {/if}
   {if $addh == ""}
  {else}
    
   <style type="text/css">
	 {$addh}{ display:none; visibility:hidden}
     
     </style>
    
    
    {/if}
{/if}