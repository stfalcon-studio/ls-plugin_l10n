<div id="blog-l10n">
    <ul class="tabs nav nav-pills mb-30">
        <li class="tab">{$aLang.plugin.l10n.$sCurrentLangText}</li>
        {foreach from=$aLangs key=sLangKey item=sLangText}
            <li class="tab">{$aLang.plugin.l10n.$sLangText}</li>
        {/foreach}
    </ul>
    <div class="content">
