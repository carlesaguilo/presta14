<!-- Block languages module -->
<div id="languages_block_top">
	<ul id="first-languages">
		{foreach from=$languages key=k item=language name="languages"}
			<li {if $language.iso_code == $lang_iso}class="selected_language"{/if}>
				{if $language.iso_code != $lang_iso}
				    {assign var=indice_lang value=$language.id_lang}
					{if isset($lang_rewrite_urls.$indice_lang)}
						<a href="{$lang_rewrite_urls.$indice_lang}" title="{$language.name}">
					{else}
						<a href="{$link->getLanguageLink($language.id_lang)}" title="{$language.name}">
					{/if}

				{/if}
					{$language.name}
				{if $language.iso_code != $lang_iso}
					</a>
				{/if}
			</li>
		{/foreach}
	</ul>
</div>
<script type="text/javascript">
	$('ul#first-languages li:not(.selected_language)').css('opacity', 0.3);
	$('ul#first-languages li:not(.selected_language)').hover(function(){ldelim}
		$(this).css('opacity', 1);
	{rdelim}, function(){ldelim}
		$(this).css('opacity', 0.3);
	{rdelim});
</script>
<!-- /Block languages module -->