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
 * Сущность пользователя
 *
 * @package modules.user
 * @since   1.0
 */
class PluginL10n_ModuleUser_EntityUser extends PluginL10n_Inherit_ModuleUser_EntityUser
{
    public function hasRole($sRole)
    {
        $sCurrentRole = Config::Get('plugin.l10n.role.user');
        if ($this->_aData['user_role']) {
            $sCurrentRole = $this->_aData['user_role'];
        }

        if ($sCurrentRole === $sRole || $this->isAdministrator()) {
            return true;
        }

        return false;
    }
}