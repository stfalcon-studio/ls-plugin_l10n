<?php

class PluginL10n_ActionSearch extends ActionSearch
{
    public function Init()
    {
        $sLang = Config::Get('lang.current') ? Config::Get('lang.current') : 'russian';
        $this->sTypesEnabled['topics']['topic_lang'] = crc32(strtolower($sLang));
        $this->sTypesEnabled['comments']['comment_topic_lang'] = crc32(strtolower($sLang));
        $this->SetDefaultEvent('index');
        $this->Viewer_AddHtmlTitle($this->Lang_Get('plugin.l10n.search'));
    }

    /**
     * Поиск и формирование результата
     *
     * @param unknown_type $aReq
     * @param unknown_type $iLimit
     * @return unknown
     */
    protected function PrepareResults($aReq, $iLimit)
    {
        /**
         *  Количество результатов по типам
         */
        foreach ($this->sTypesEnabled as $sType => $aExtra) {
            $cacheKey = Config::Get('module.search.entity_prefix') . "searchResult_{$sType}_{$aReq['q']}_1_1";
            $this->Cache_Delete($cacheKey);
            $aRes['aCounts'][$sType] = intval($this->Sphinx_GetNumResultsByType($aReq['q'], $sType, $aExtra));
        }
        if ($aRes['aCounts'][$aReq['sType']] == 0) {
            /**
             *  Объектов необходимого типа не найдено
             */
            unset($this->sTypesEnabled[$aReq['sType']]);
            /**
             * Проверяем отсальные типы
             */
            foreach (array_keys($this->sTypesEnabled) as $sType) {
                if ($aRes['aCounts'][$sType]) {
                    Router::Location(Router::GetPath('search') . $sType . '/?q=' . $aReq['q']);
                }
            }
        } elseif (($aReq['iPage'] - 1) * $iLimit <= $aRes['aCounts'][$aReq['sType']]) {
            /**
             * Ищем
             */
            $iOffset = ($aReq['iPage'] - 1) * $iLimit;
            $cacheKey = Config::Get('module.search.entity_prefix')
                . "searchResult_{$aReq['sType']}_{$aReq['q']}_{$iOffset}_{$iLimit}";
            $this->Cache_Delete($cacheKey);
            $this->aSphinxRes = $this->Sphinx_FindContent(
                $aReq['q'],
                $aReq['sType'],
                ($aReq['iPage'] - 1) * $iLimit,
                $iLimit,
                $this->sTypesEnabled[$aReq['sType']]
            );
            /**
             * Возможно демон Сфинкса не доступен
             */
            if (false === $this->aSphinxRes) {
                return false;
            }

            $this->bIsResults = true;
            /**
             * Формируем постраничный вывод
             */
            $aPaging = $this->Viewer_MakePaging(
                $aRes['aCounts'][$aReq['sType']],
                $aReq['iPage'],
                $iLimit,
                Config::Get('pagination.pages.count'),
                Router::GetPath('search') . $aReq['sType'],
                array(
                    'q' => $aReq['q']
                )
            );
            $this->Viewer_Assign('aPaging', $aPaging);
        }

        $this->SetTemplateAction('results');
        $this->Viewer_AddHtmlTitle($aReq['q']);
        $this->Viewer_Assign('bIsResults', $this->bIsResults);

        return $aRes;
    }

}