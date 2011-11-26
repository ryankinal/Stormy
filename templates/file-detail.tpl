<article>
	<h1>{$file->getAttribute('name')|escape}</h1>
	<dl>
		<dt>Size</dt>
		<dd>{$file->getAttribute('size')|escape}</dd>
		<dt>Uploaded</dt>
		<dd>{$file->getAttribute('uploaded')|escape}
	</dl>
	<form method="POST">
		<input type="hidden" name="token" value="{$token}" />
		<input class="delete" type="button" name="delete" value="Delete" />
	</form>
</article>