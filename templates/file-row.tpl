<tr id="file-{$phile->getAttribute('file_id')|escape}">
	<td><a href="download/?id={$phile->getAttribute('file_id')}" rel="download">{$phile->getAttribute('name')|escape}</a></td>
	<td>{$phile->getAttribute('mime_type')|escape}</td>
	<td>{$phile->getAttribute('size')|escape}</td>
	<td>{$phile->getAttribute('created')|escape}</td>
	<td>
		<a href="#delete" class="delete" data-id="{$phile->getAttribute('file_id')|escape}">delete</a>
	</td>
</tr>