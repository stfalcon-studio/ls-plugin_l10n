<?php

/**
 * Модуль User плагина L10n
 */
class PluginL10n_ModuleUser extends PluginL10n_Inherit_ModuleUser
{

    /**
     * Сохранить языковые настройки пользователя
     *
     * @param ModuleUser_EntityUser $oUser
     * @return boolean
     */
    public function UpdateUserLang(ModuleUser_EntityUser $oUser)
    {
        // чистим зависимые кеши
        $this->Cache_Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, array('user_update'));
        $this->Cache_Delete("user_{$oUser->getId()}");

        return $this->oMapper->UpdateUserLang($oUser);
    }

    public function UpdateUserRole(ModuleUser_EntityUser $oUser)
    {
        // чистим зависимые кеши
        $this->Cache_Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, array('user_update'));
        $this->Cache_Delete("user_{$oUser->getId()}");

        return $this->oMapper->UpdateUserRole($oUser);
    }
}