{include file='header.tpl'}

{include file='menu.settings.tpl'}

<form action="" method="POST" enctype="multipart/form-data" class="wrapper-content">
    <input type="hidden" name="security_ls_key" value="{$LIVESTREET_SECURITY_KEY}" />
    <dl class="form-item">
        <dt>
            <label for="l10n_user_lang">
                {$aLang.plugin.l10n.l10n_settings_label}:
            </label>
        </dt>
        <dd>
            <select name="l10n_user_lang" id="l10n_user_lang" class="input-width-250">
                {foreach from=$aLangs key=sLangKey item=sLangText}
                    <option value="{$sLangKey}" {if $sLangKey==$oUserCurrent->getUserLang()}selected{/if}>
                        {$aLang.plugin.l10n.$sLangText}
                    </option>
                {/foreach}
            </select>
        </dd>
    </dl>
    <input type="submit" value="{$aLang.settings_profile_submit}" name="l10n_settings_submit" class="button button-primary"/>
</form>

{include file='footer.tpl'}