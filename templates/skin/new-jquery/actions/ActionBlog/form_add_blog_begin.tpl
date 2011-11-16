<div id="blog-l10n">
    <ul class="tabs">
        <li class="tab">{$aLang.$sCurrentLangText}</li>
        {foreach from=$aLangs key=sLangKey item=sLangText}
            <li class="tab">{$aLang.$sLangText}</li>
        {/foreach}
    </ul>
    <div class="content">
