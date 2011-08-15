<?php

/**
 * Маппер User модуля User плагина L10n
 */
class PluginL10n_ModuleUser_MapperUser extends PluginL10n_Inherit_ModuleUser_MapperUser
{

    /**
     * Сохранить языковые настройки пользователя
     *
     * @param ModuleUser_EntityUser $oUser
     * @return boolean
     */
    public function UpdateUserLang(ModuleUser_EntityUser $oUser)
    {
        $sql = 'UPDATE
                    ' . Config::Get('db.table.user') . '
                SET
                    user_lang = ?
                WHERE
                    user_id = ?d';
        $this->oDb->query($sql, $oUser->getUserLang(), $oUser->getId());

        return true;
    }

}