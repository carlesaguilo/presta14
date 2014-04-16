<li class="{if $node.children|@count > 0}sub{/if}{if isset($last) && $last == 'true'} last{/if}">
	<a href="{$node.link}" {if isset($currentCategoryId) && ($node.id == $currentCategoryId)}class="selected"{/if}>{$node.name|escape:html:'UTF-8'}</a>
	{if $node.children|@count > 0}
		<ul class="subcat1">
		{foreach from=$node.children item=child name='child'}
			<li {if $node.children|@count > 4 and $smarty.foreach.child.iteration <= 4} class="noborder"{/if}>
				<a href="{$child.link|escape:html:'UTF-8'}">{$child.name|escape:html:'UTF-8'}</a>
				{if $child.children|@count > 0}
				<ul class="subcat2">
					{foreach from=$child.children item=child2}
					<li><a href="{$child2.link|escape:html:'UTF-8'}">{$child2.name|escape:html:'UTF-8'}</a></li>
					{/foreach}
				</ul>
				{/if}
			</li>
		{/foreach}
		</ul>
	{/if}
</li>