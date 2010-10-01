<html>
	<head>
		<title>{$title}</title>
	</head>
	<body>
		<table>
{section name=news_list loop=$news_obj}
			<tr class="news{if $news_obj[news_list].STICKY} news_sticky{/if}">
				<td></td>
				<td></td>
				<td></td>
				<td></td>
			</tr>
{/section}
		</table>
	</body>
</html>