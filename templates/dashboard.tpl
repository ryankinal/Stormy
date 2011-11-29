<h2>Your Stuff &mdash; <span id="fileCount">{$filesCount|escape} file{if $filesCount != 1}s{/if}</span></h2>
<div class="left three-quarters">
	<div class="file-row file-header">
		<div class="left two-fifths file-name">Name</div>
		<div class="left three-fifths">
			<div class="left quarter">Type</div>
			<div class="left quarter">Size</div>
			<div class="left quarter">Uploaded</div>
		</div>
	</div>
	<div id="files">
		{foreach from=$files item=phile}
			{include file="file-row.tpl" phile=$phile}
		{foreachelse}
			<div class="file-row clear">No stuff! Upload some!</div>
		{/foreach}
	</div>
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