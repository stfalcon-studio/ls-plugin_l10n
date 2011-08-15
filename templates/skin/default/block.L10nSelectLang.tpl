<div class="block stream">
    <div class="tl"><div class="tr"></div></div>
    <div class="cl">
        <div class="cr">
            <h2>{$aLang.l10n_site_language}</h2>
            <ul class="block-lang">
                {foreach from=$aSiteLangs key=sLang item=sLangAlias}
                <li>
                    <a href="{cfg name='path.root.web'}/{$sLangAlias}/">
                        <img src="{$sTemplateWebPathPluginL10n}images/flags/{$sLang}.png">
                    </a>
                </li>
                {/foreach}
            </ul>
        </div>
    </div>
    <div class="bl"><div class="br"></div></div>
</div>
