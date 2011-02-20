{include file="header.html"}
<div class="itemPage">
{if $itemPage.error == "item"}
Item nicht gefunden
{else}
	<img class="item item_image" src="{$itemPage.info.item_icon}" alt="" />
	<div class="item_headline {$itemPage.info.quality}">{$itemPage.info.name}</div>

	<div class="item_stats">
	</div>
	{if $itemPage.info.model}<div class="item_model">
		<object width="600" height="400" type="application/x-shockwave-flash" data="http://static.wowhead.com/modelviewer/ModelView.swf" id="dsjkgbdsg2346" style="visibility: visible;">
			<param name="quality" value="high">
			<param name="allowscriptaccess" value="always">
			<param name="allowfullscreen" value="true">
			<param name="menu" value="false">
			<param name="bgcolor" value="#FFFFFF">
			<param name="flashvars" value="model={$itemPage.info.model.flash.model}&amp;modelType={$itemPage.info.model.flash.type}&amp;contentPath={$itemPage.info.model.flash.contentPath}">
		</object>
	</div>
	{/if}
{/if}
</div>
{include file="footer.html"}