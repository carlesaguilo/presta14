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
	{foreach from=$blockCategTree.children item=child name=blockCategTree}
		{if $smarty.foreach.blockCategTree.last}
			{include file="$branche_tpl_path" node=$child last='true'}
		{else}
			{include file="$branche_tpl_path" node=$child}
		{/if}
	{/foreach}
	</ul>
</div>
<!-- /TM Categories -->