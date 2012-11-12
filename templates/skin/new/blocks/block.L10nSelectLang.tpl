<div class="block stream">
    <div class="tl"><div class="tr"></div></div>
    <div class="cl">
        <div class="cr">
            <h2>{$aLang.plugin.l10n.l10n_site_language}</h2>
            <ul class="block-lang">
                {foreach from=$aSiteLangs key=sLang item=sLangAlias}
                    {assign var="l10n_lang_this" value="l10n_lang_$sLang"}
                    <li>
                        <a href="{cfg name='path.root.web'}/{$sLangAlias}/">
                            <img src="{$sTemplateWebPathPluginL10n}images/flags/{$sLang}.png"
                                 title="{$aLang.plugin.l10n.$l10n_lang_this}" alt="{$aLang.plugin.l10n.$l10n_lang_this}"/>
                        </a>
                    </li>
                {/foreach}
            </ul>
        </div>
    </div>
    <div class="bl"><div class="br"></div></div>
</div>