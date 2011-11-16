    </div>
    {foreach from=$aLangs key=sLangKey item=sLangText}
    <div class="content" style="display: none;">
        <p><label for="blog_title_{$sLangKey}">{$aLang.blog_create_title}:</label><br />
        <input type="text" id="blog_title_{$sLangKey}" name="blog_title_{$sLangKey}" value="{$_aRequest.$sLangKey[0]}" class="input-wide" /><br />
        <span class="note">{$aLang.blog_create_title_notice}</span></p>

        <p><label for="blog_url_{$sLangKey}">{$aLang.blog_create_url}:</label><br />
        <input type="text" id="blog_url_{$sLangKey}" name="blog_url_{$sLangKey}" value="{$_aRequest.$sLangKey[1]}" class="input-wide" {if $_aRequest.blog_id && !$oUserCurrent->isAdministrator()}disabled{/if} /><br />
        <span class="note">{$aLang.blog_create_url_notice}</span></p>

        <p><label for="blog_description_{$sLangKey}">{$aLang.blog_create_description}:</label><br />
        <textarea class="input-wide" name="blog_description_{$sLangKey}" id="blog_description_{$sLangKey}" rows="20">{$_aRequest.$sLangKey[2]}</textarea><br />
        <span class="note">{$aLang.blog_create_description_notice}</span></p>
    </div>
    {/foreach}
</div>
{if !$oConfig->GetValue('view.tinymce')}
    <script type="text/javascript">
	jQuery(document).ready(function($){
            // Подключаем редакторы для остальных языков
            {foreach from=$aLangs key=sLangKey item=sLangText}
                $('#blog_description_{$sLangKey}').markItUp(getMarkitupSettings());
            {/foreach}
	});
    </script>
{/if}
<script type="text/javascript" src="{$sTemplateWebPathPluginL10n}js/simpletab.js"></script>