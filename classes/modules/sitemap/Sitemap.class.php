<?php
/*-------------------------------------------------------
*
*   LiveStreet Engine Social Networking
*   Copyright © 2008 Mzhelskiy Maxim
*
*--------------------------------------------------------
*
*   Official site: www.livestreet.ru
*   Contact e-mail: rus.engine@gmail.com
*
*   GNU General Public License, version 2:
*   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*
---------------------------------------------------------
*/

/**
 * Module for plugin Sitemap
 */
class PluginL10n_ModuleSitemap extends PluginL10n_Inherit_PluginSitemap_ModuleSitemap {

    /**
     * Добавляем язык к ключу кеша. Чтобы не перекрывались кеши разных наборов 
     * для разных языков
     * 
     * @return string
     */
    public function getCacheIdPrefix()
    {
        $sPreffix = $this->PluginL10n_L10n_GetLangForQuery()? $this->PluginL10n_L10n_GetLangForQuery() . '_' : '';
        return $sPreffix . parent::getCacheIdPrefix();
    }

}
