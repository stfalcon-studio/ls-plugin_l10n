{include file='header.tpl' menu='settings' showWhiteBack=true}

<h1>{$aLang.l10n_settings_title}</h1>

<form action="" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="security_ls_key" value="{$LIVESTREET_SECURITY_KEY}" />
    <p>
        <label for="l10n_user_lang">{$aLang.l10n_settings_label}:</label>
        <select name="l10n_user_lang">
            {foreach from=$aLangs key=sLangKey item=sLangText}
                <option value="{$sLangKey}" {if $sLangKey==$oUserCurrent->getUserLang()}selected{/if}>
                    {$aLang.$sLangText}
                </option>
            {/foreach}
        </select>
    </p>
    <p>
        <input type="submit" value="{$aLang.settings_profile_submit}" name="l10n_settings_submit"/>
    </p>
</form>

{include file='footer.tpl'}