<div class="file-row clear" id="file-{$phile->getAttribute('file_id')|escape}">
	<div class="left two-fifths file-name">
		<a href="{#WEB_ROOT#}/files/download/?id={$phile->getAttribute('file_id')}" rel="download">{$phile->getAttribute('name')|escape}</a>
	</div>
	<div class="left three-fifths file-stats">
		<div class="left quarter">{$phile->getAttribute('mime_type')|filetype|escape}</div>
		<div class="left quarter">{$phile->getAttribute('size')|filesize|escape}</div>
		<div class="left quarter">{$phile->getAttribute('created')|date_format:"%D"}</div>
		<div class="left quarter">
			<a href="#delete" class="delete" data-id="{$phile->getAttribute('file_id')|escape}">delete</a>
		</div>
	</div>
</div>