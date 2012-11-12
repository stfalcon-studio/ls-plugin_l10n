<p>
    <label for="topic_lang">{$aLang.plugin.l10n.l10n_topic_lang_label}:</label>
    <select name="topic_lang">
        {foreach from=$aLangs key=sLangKey item=sLangText}
            <option value="{$sLangKey}" 
                {if (isset($_aRequest.topic_lang) && $sLangKey==$_aRequest.topic_lang)
                    || (!isset($_aRequest.topic_lang) && $sLangKey==$oConfig->GetValue('lang.current'))}
                    selected
                {/if}>
                {$aLang.plugin.l10n.$sLangText}
            </option>
        {/foreach}
    </select>
</p>
