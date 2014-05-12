<?php

/**
 * @inheritdoc
 */
class PluginL10n_ModuleACL extends PluginL10n_Inherit_ModuleACL
{
    /**
     * @inheritdoc
     */
    public function IsAllowBlog($oBlog, $oUser)
    {
        if ($oUser->hasRole(Config::Get('plugin.l10n.role.translator'))) {
            return true;
        }

        return parent::IsAllowBlog($oBlog, $oUser);
    }
}