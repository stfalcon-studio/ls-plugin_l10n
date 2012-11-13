<p>
    <label for="l10n_user_lang">{$aLang.plugin.l10n.l10n_settings_label}:</label>
    <select name="l10n_user_lang">
        {foreach from=$aLangs key=sLangKey item=sLangText}
            <option value="{$sLangKey}" 
                {if isset($smarty.post.l10n_user_lang)}
                    {if $smarty.post.l10n_user_lang == $sLangKey}
                        selected
                    {/if}
                {elseif $oConfig->GetValue('lang.current') == $sLangKey}
                    selected
                {/if}>
                {$aLang.plugin.l10n.$sLangText}
            </option>
        {/foreach}
    </select>
</p>
