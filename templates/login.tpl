<div class="step">
    <div class="left divine-small">
        <div class="shadowbox">
            <h2>Sign In</h2>
            {if $error}<p class="error">{$error|escape}</p>{/if}
            {include file="login-form.tpl" user=$user}
        </div>
    </div>
    <div class="left divine-large">
        <div class="content">
            <h2>No Account?</h2>
            <p>Go <a href="{#WEB_ROOT#}/signup">set one up</a>. It's fast and easy, and you'll be uploading files in no time. We promise.</p>
        </div>
    </div>
</div>