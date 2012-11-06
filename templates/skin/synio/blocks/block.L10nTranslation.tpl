<div class="block stream">
    <div class="tl"><div class="tr"></div></div>
    <div class="cl">
        <div class="cr">
            {if $oTopicOriginal}
                <h2>{$aLang.plugin.l10n.l10n_topic_original}</h2>
                {assign var="sTopicLang" value=$oTopicOriginal->getTopicLang()}
                {assign var="l10n_lang_this" value="l10n_lang_$sTopicLang"}
                <div class="block-lang">
                    <img src="{$sTemplateWebPathPluginL10n}images/flags/{$sTopicLang}.png"
                         title="{$aLang.plugin.l10n.$l10n_lang_this}" alt="{$aLang.plugin.l10n.$l10n_lang_this}"/>
                    <a href="{$oTopicOriginal->getUrl()}">{$oTopicOriginal->getTitle()}</a>
                </div>
            {else if $aTopicTranslates}
                <h2>{$aLang.plugin.l10n.l10n_topic_translations}</h2>
                <ul class="block-lang">
                    {foreach from=$aTopicTranslates item=oTopicTranslate}
                        {assign var="sTopicLang" value=$oTopicTranslate->getTopicLang()}
                        {assign var="l10n_lang_this" value="l10n_lang_$sTopicLang"}
                        <li>
                            <img src="{$sTemplateWebPathPluginL10n}images/flags/{$sTopicLang}.png"
                                 title="{$aLang.plugin.l10n.$l10n_lang_this}" alt="{$aLang.plugin.l10n.$l10n_lang_this}"/>
                            <a href="{$oTopicTranslate->getUrl()}">{$oTopicTranslate->getTitle()}</a>
                        </li>
                    {/foreach}
                </ul>
            {/if}
        </div>
        {if $bAllowTopicTranslation}
            <div class="{if ($oTopicOriginal || $aTopicTranslates)}bottom{else}cr{/if}">
                <a href="{router page='topic'}add/translate/{$oTopic->getId()}" alt="{$aLang.plugin.l10n.l10n_topic_translate}" title="{$aLang.plugin.l10n.l10n_topic_translate}">{$aLang.plugin.l10n.l10n_topic_translate}</a>
            </div>
        {/if}

    </div>
    <div class="bl"><div class="br"></div></div>
</div>