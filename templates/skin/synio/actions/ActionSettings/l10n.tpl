{include file='header.tpl'}

{include file='menu.settings.tpl'}

<form action="" method="POST" enctype="multipart/form-data" class="wrapper-content">
    <h3>{$aLang.plugin.l10n.l10n_settings_title}</h3>
    <input type="hidden" name="security_ls_key" value="{$LIVESTREET_SECURITY_KEY}" />
    <p>
        <label for="l10n_user_lang">{$aLang.plugin.l10n.l10n_settings_label}:
            <select name="l10n_user_lang">
                {foreach from=$aLangs key=sLangKey item=sLangText}
                    <option value="{$sLangKey}" {if $sLangKey==$oUserCurrent->getUserLang()}selected{/if}>
                        {$aLang.plugin.l10n.$sLangText}
                    </option>
                {/foreach}
            </select>
        </label>
    </p>
    <p>
        <input type="submit" value="{$aLang.settings_profile_submit}" name="l10n_settings_submit" class="button button-primary"/>
    </p>
</form>

{include file='footer.tpl'}