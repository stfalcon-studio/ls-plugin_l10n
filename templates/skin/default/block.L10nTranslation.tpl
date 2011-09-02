<div class="block stream">
    <div class="tl"><div class="tr"></div></div>
    <div class="cl">
        <div class="cr">
                {if $oTopicOriginal}
                    <h2>{$aLang.l10n_topic_original}</h2>
                    <img src="{$sTemplateWebPathPluginL10n}images/flags/{$oTopicOriginal->getTopicLang()}.png">
                    <a href="{$oTopicOriginal->getUrl()}">{$oTopicOriginal->getTitle()}</a>
                {/if}
                {if $aTopicTranslates}
                    <h2>{$aLang.l10n_topic_translations}</h2>
                    <ul class="block-lang">
                    {foreach from=$aTopicTranslates item=oTopicTranslates}
                        <li>
                            <img src="{$sTemplateWebPathPluginL10n}images/flags/{$oTopicTranslates->getTopicLang()}.png">
                            <a href="{$oTopicTranslates->getUrl()}">{$oTopicTranslates->getTitle()}</a>
                        </li>
                    {/foreach}
                    </ul>
                {/if}
        </div>
    </div>
    <div class="bl"><div class="br"></div></div>
</div>