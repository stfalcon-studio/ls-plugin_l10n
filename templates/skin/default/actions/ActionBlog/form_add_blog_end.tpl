    </div>
    {foreach from=$aLangs key=sLangKey item=sLangText}
    <div class="content">
        <p><label for="blog_title_{$sLangKey}">{$aLang.blog_create_title}:</label><br />
        <input type="text" id="blog_title_{$sLangKey}" name="blog_title_{$sLangKey}" value="{$_aRequest.$sLangKey[0]}" class="w100p" /><br />
        <span class="form_note">{$aLang.blog_create_title_notice}</span></p>

        <p><label for="blog_url_{$sLangKey}">{$aLang.blog_create_url}:</label><br />
        <input type="text" id="blog_url_{$sLangKey}" name="blog_url_{$sLangKey}" value="{$_aRequest.$sLangKey[1]}" class="w100p" {if $_aRequest.blog_id && !$oUserCurrent->isAdministrator()}disabled{/if} /><br />
        <span class="form_note">{$aLang.blog_create_url_notice}</span></p>

        <p><label for="blog_description_{$sLangKey}">{$aLang.blog_create_description}:</label><br />
        <textarea name="blog_description_{$sLangKey}" id="blog_description_{$sLangKey}" rows="20">{$_aRequest.$sLangKey[2]}</textarea><br />
        <span class="form_note">{$aLang.blog_create_description_notice}</span></p>
    </div>
    {/foreach}
</div>
<script type="text/javascript" src="{$sTemplateWebPathPluginL10n}lib/external/MooTools_1.2/mootools-1.2.4-more.js"></script>
<script type="text/javascript" src="{$sTemplateWebPathPluginL10n}lib/external/MooTools_1.2/plugins/TabPane/TabPane.js"></script>
<link rel="stylesheet" type="text/css" href="{$sTemplateWebPathPluginL10n}lib/external/MooTools_1.2/plugins/TabPane/style.css" media="all" />
{literal}
<script type="text/javascript">
    var myTabPane = new TabPane('blog-l10n', {tabSelector: 'li', contentSelector: 'div'});
</script>
{/literal}