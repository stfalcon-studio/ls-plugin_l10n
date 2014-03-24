{include file='header.tpl'}
<h1>{$aLang.plugin.l10n.l10n_not_translated_topics}</h1>

<table class="table table-blogs">
    <thead>
    <tr>
        <th class="cell-name">{$aLang.plugin.l10n.l10n_topic_name}</th>
        <th class="cell-readers">{$aLang.plugin.l10n.l10n_topic_language}</th>
        <th class="cell-rating align-center">{$aLang.plugin.l10n.l10n_additional_functions}</th>
    </tr>
    </thead>
    <tbody>
        {foreach from=$aTopicData item=oTopic}
            <tr>
                <td class="cell-name">
                    <a href="{$oTopic->getUrl()}">{$oTopic->getTopicTitle()}</a>
                </td>
                <td class="cell-name">
                    {$oTopic->getTopicLang()}
                </td>
                <td class="cell-name">
                    <a href="{router page='topic'}add/translate/{$oTopic->getId()}" alt="{$aLang.plugin.l10n.l10n_topic_translate}" title="{$aLang.plugin.l10n.l10n_topic_translate}">{$aLang.plugin.l10n.l10n_topic_translate}</a>
                </td>
            </tr>
        {/foreach}
    </tbody>
</table>

{include file='paging.tpl' aPaging=$aPaging}

{include file='footer.tpl'}
