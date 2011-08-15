<?php

/**
 * Блок выбора языка
 */
class PluginL10n_BlockL10nSelectLang extends Block {

    /**
     * Выполняется при вызове блока
     * 
     * @return void
     */
    public function Exec() {
        $this->Viewer_Assign('aSiteLangs', $this->PluginL10n_L10n_GetAllowedLangsAliases());
    }

}