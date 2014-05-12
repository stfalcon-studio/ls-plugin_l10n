<div class="block block-content">
    <form action="{router page='admin'}role/" method="post">
        <input type="hidden" name="user_id" value="{$iUserId}"/>
        {if $bTranslator}
            <button type="submit" class="button button-action" name="remove" value="remove">{$aLang.plugin.l10n.l10n_unset_role_translator}</button>
        {else}
            <button type="submit" class="button button-action" name="add" value="add">{$aLang.plugin.l10n.l10n_set_role_translator}</button>
        {/if}
    </form>
</div>