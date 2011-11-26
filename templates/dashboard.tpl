<h2>Your Stuff</h2>
<div class="left three-quarters">
	<table>
		<thead>
			<tr>
				<th>Name</th>
				<th>Type</th>
				<th>Size</th>
				<th>Uploaded</th>
				<th></th>
			</tr>
		</thead>
		<tbody id="files">
			{foreach from=$files item=phile}
				{include file="file-row.tpl" phile=$phile}
			{foreachelse}
				<tr id="nofiles">
					<td colspan="5">No stuff! Upload some!</td>
				</tr>
			{/foreach}
		</tbody>
	</table>
	<input type="hidden" id="token" value="{$token}" />
</div>
<div class="left quarter">
	<div class="shadowbox">
		<h3>WTF?</h3>
		<p>There aren't any &quot;browse&quot; buttons here.</p>
		<p>Just <strong>drag &amp; drop</strong> your files into the browser.</p>
	</div>
	<div id="counters" class="shadowbox">
		<h3>Uploads</h3>
	</div>
</div>