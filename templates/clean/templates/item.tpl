{include file="header.html"}
<div class="itemPage">
{if $itemPage.error == "item"}
Item nicht gefunden
{else}
	<img class="item_image" src="{$icon_repo_large}{$itemPage.info.icon}.jpg" alt="" />
	<div class="item_headline">{$itemPage.info.name}</div>

	<div class="item_stats">
		{include file="item_stats.html"}
		{if $itemPage.info.model}<div class="item_model">
			<object width="400" height="300" type="application/x-shockwave-flash" data="http://static.wowhead.com/modelviewer/ModelView.swf" id="dsjkgbdsg2346" style="visibility: visible;">
				<param name="quality" value="high">
				<param name="allowscriptaccess" value="always">
				<param name="allowfullscreen" value="true">
				<param name="menu" value="false">
				<param name="bgcolor" value="#FFFFFF">
				<param name="flashvars" value="model={$itemPage.info.model.flash.model}&amp;modelType={$itemPage.info.model.flash.type}&amp;contentPath={$itemPage.info.model.flash.contentPath}">
			</object>
		</div>
		{/if}
	</div>
{/if}
</div>
{include file="footer.html"}