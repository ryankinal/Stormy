<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
	
		<title>{$title|escape} | stor.me</title>
		<link rel="stylesheet" href="{#WEB_ROOT#}/styles/haGrid.css" />
		<link rel="stylesheet" href="{#WEB_ROOT#}/styles/skin.css" />
		{foreach from=$styles item=style}
			<link rel="stylesheet" href="{#WEB_ROOT#}/{$style|escape}" />
		{/foreach}
		{foreach from=$scripts item=script}
			<script type="text/javascript" src="{#WEB_ROOT#}/{$script|escape}"></script>
		{/foreach}
	</head>
	<body>
		<div class="container">
			<header>
				<h1 class="left"><a href="{#WEB_ROOT#}">Stormy <img src="{#WEB_ROOT#}/images/storme.png" alt="Stor.me" /></a></h1>
				<nav class="right">
					{include file=$nav}
				</nav>
			</header>
			<section class="clear">
				{$content}
			</section>
			<footer class="clear">
				&copy; store.me 2011 &mdash; <a href="{#WEB_ROOT#}/about">about</a>
			</footer>
		</div>
	</body>
</html>