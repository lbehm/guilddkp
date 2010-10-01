<html>
	<head>
		<title>{$title}</title>
	</head>
	<body>
		<ul>
{section name=news_list loop=$news_obj}
			<li class="news{if $news_obj[news_list].STICKY} news_sticky{/if}">
				<span class="news_headline">{$news_obj[news_list].HEADLINE}</span><span class="news_date">{$news_obj[news_list].AUTHOR} @ {$news_obj[news_list].TIME}</span>
				<hr />
				<p>{$news_obj[news_list].MESSAGE}</p>
			</li>
{/section}
		</ul>
	</body>
</html>