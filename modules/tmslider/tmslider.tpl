<div id="tmslider">
<ul>
	{counter name=tmslider_counter start=0 skip=1 print=false}
	{foreach from=$xml->link item=home_link name=links}
	<li class="slide{counter name=tmslider_counter}">
		<div>{$home_link->desc}</div>
		<a href='{$home_link->url}'><img src='{$this_path}{$home_link->img}' alt="" /></a>
	</li>
	{/foreach}
</ul>
</div>