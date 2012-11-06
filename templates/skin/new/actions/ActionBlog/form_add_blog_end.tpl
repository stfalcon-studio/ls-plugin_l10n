    </div>
    {foreach from=$aLangs key=sLangKey item=sLangText}
    <div class="content" style="display: none;">
        <p><label for="blog_title_{$sLangKey}">{$aLang.blog_create_title}:</label>
        <input type="text" id="blog_title_{$sLangKey}" name="blog_title_{$sLangKey}" value="{$_aRequest.$sLangKey[0]}" class="input-text input-width-full" /><br />
        <span class="note">{$aLang.blog_create_title_notice}</span></p>

        <p><label for="blog_url_{$sLangKey}">{$aLang.blog_create_url}:</label>
        <input type="text" id="blog_url_{$sLangKey}" name="blog_url_{$sLangKey}" value="{$_aRequest.$sLangKey[1]}" class="input-text input-width-full" {if $_aRequest.blog_id && !$oUserCurrent->isAdministrator()}disabled{/if} /><br />
        <span class="note">{$aLang.blog_create_url_notice}</span></p>

        <p><label for="blog_description_{$sLangKey}">{$aLang.blog_create_description}:</label>
        <textarea class="input-wide markitup-editor" name="blog_description_{$sLangKey}" id="blog_description_{$sLangKey}" rows="20">{$_aRequest.$sLangKey[2]}</textarea><br />
        <span class="note">{$aLang.blog_create_description_notice}</span></p>
    </div>
    {/foreach}
</div>
{literal}
    <script type="text/javascript">
        /**
         * Simple tabs
         */
        $(document).ready(function(){

            // Добавим сразу же видимость первым элементам табов
            var tabSw = $('#blog-l10n ul.tabs');
            $('li:first', tabSw).addClass ('active')
            .parents('#blog-l10n').find('div.content:first').show();
            tabSw.delegate('li:not(.active)', 'click', function() {
                $(this).addClass('active')
                .siblings().removeClass('active')
                .parents('#blog-l10n')
                .find('div.content').hide()
                .eq($(this).index()).show();
            });
        });
    </script>
{/literal}