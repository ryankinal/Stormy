<div>
	<div class="left divine-large">
		<div class="shadowbox">
			<h2>Your information</h2>
			<dl>
				<dt>Joined</dt>
				<dd>{if $user}{$user->getAttribute('joined')|escape}{/if}</dd>
				<dt>Files</dt>
				<dd>{$filesCount|escape}</dd>
			</dl>
		</div>

		<div class="shadowbox">
			<h2>Change Your Password</h2>
			{if $passwordError}<p class="error">{$passwordError|escape}</p>{/if}
			{if $passwordMessage}<p class="message">{$passwordMessage|escape}</p>{/if}
			<form action="{#WEB_ROOT#}/account/update" method="POST">
				<dl>
					<dt><label for="password1">Password</label></dt>
					<dd><input type="password" name="password1" /></dd>
					<dt><label for="password2">Confirm</label></dt>
					<dd><input type="password" name="password2" /></dd>
				</dl>
				<input type="hidden" name="token" value="{$token}" />
				<input type="submit" value="Change" />
			</form>
		</div>
	</div>
	
	<div class="left divine-small">
		<div class="content">
			<h3>Delete Your Account</h3>
			<p>Don't want your stuff? Well, we can put a stop to all of it. Just click the button below.</p>
			<form action="{#WEB_ROOT#}/account/delete" method="POST">
				<input type="hidden" name="token" value="{$token|escape}" />
				<input type="hidden" name="id" {if $user}value="{$user->getAttribute('user_id')|escape}"{/if} />
				<input type="submit" value="Delete!" />
			</form>
			{if $deleteError}<p class="error">{$deleteError|escape}</p>{/if}
		</div>
	</div>
</div>