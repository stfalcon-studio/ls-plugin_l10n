<?php

/**
 * Плагин L10n. Хуки для топиков
 */
class PluginL10n_HookTopic extends Hook
{

    /**
     * Регистрируем нужные хуки
     *
     * @return void
     */
    public function RegisterHook()
    {
        // хук на страницу редактирования топика
        $this->AddHook('topic_edit_show', 'TopicEditShow', __CLASS__);
        // хук на проверку правильности заполнения полей топика
        $this->AddHook('check_topic_fields', 'CheckTopicFields', __CLASS__);
        // хук на страницу просмотра топика
        $this->AddHook('topic_show', 'TopicShow', __CLASS__);

        //хук на добавление перевода
        $this->AddHook('topic_add_after', 'TopicAddAfter', __CLASS__);
        $this->AddHook('topic_add_show', 'TopicAddShow', __CLASS__);

        // хук на форму добавления/редактирования топика
        $this->AddHook('template_form_add_topic_topic_begin',
                'TemplateFormAddTopicBegin', __CLASS__);
        $this->AddHook('template_form_add_topic_topic_end',
                'TemplateFormAddTopicEnd', __CLASS__);
        // хук на форму добавления/редактирования опроса
        $this->AddHook('template_form_add_topic_question_begin',
                'TemplateFormAddTopicBegin', __CLASS__);
        $this->AddHook('template_form_add_topic_question_end',
                'TemplateFormAddTopicEnd', __CLASS__);
        // хук на форму добавления/редактирования ссылки
        $this->AddHook('template_form_add_topic_link_begin',
                'TemplateFormAddTopicBegin', __CLASS__);
        $this->AddHook('template_form_add_topic_link_end',
                'TemplateFormAddTopicEnd', __CLASS__);
        $this->AddHook('template_html_head_end',
                'TemplateHtmlHeadEnd', __CLASS__);
    }

    /**
     * Хук на страницу редактирования топика
     * Добавляем информацию о языке топика в $_REQUEST
     *
     * @param array $aData
     */
    public function TopicEditShow($aData)
    {
        $oTopic = $aData['oTopic'];

        if (!isset($_REQUEST['submit_topic_publish']) and !isset($_REQUEST['submit_topic_save'])) {
            /**
             * Заполняем поля формы для редактирования
             * Только перед отправкой формы!
             */
            $_REQUEST['topic_lang'] = $oTopic->getTopicLang();
        }
    }

    /**
     * Хук на страницу создания топика
     *
     * @param array $aData
     */
    public function TopicAddShow()
    {
        if (!(Router::GetActionEvent() == 'add' && Router::GetParam(0) == 'translate')) {
            return;
        }
        if ($oTopicOriginal = $this->_getTopicOriginalByUrParams()) {
            // проверка на попытку добавить перевод к переводу
            if ($oTopicOriginal->getTopicOriginalId()) {
                $this->Message_AddErrorSingle(
                        $this->Lang_Get('l10n_topic_translate_not_original'), $this->Lang_Get('error'));
                return;
            }

            if (!$this->_IsAllowTopicTranslation($oTopicOriginal)) {
                $this->Message_AddErrorSingle(
                        $this->Lang_Get('l10n_topic_translate_not_exist'), $this->Lang_Get('error'));
                return;
            }

            // активным пунктом блогом в селекте будет блог топика-оригинала
            $_REQUEST['blog_id'] = $oTopicOriginal->getBlogId();
        } else {
            $this->Message_AddErrorSingle(
                    $this->Lang_Get('l10n_topic_translate_not_exist'), $this->Lang_Get('error'));
        }
    }

    /**
     * После добавления топика в БД заполняем поле origin_id
     *
     * @param array $aData
     * @return void
     */
    public function TopicAddAfter($aData)
    {
        $oTopic = $aData['oTopic'];

        if ($oTopicOriginal = $this->_getTopicOriginalByUrParams()) {
            if (!$this->Topic_SetTopicOriginal($oTopic, $oTopicOriginal)) {
                $this->Message_AddErrorSingle($this->Lang_Get('system_error'));
            }
        }
    }

    /**
     * Хук на показ топика
     * Загружает оригинальный топик или переводы
     *
     * @param array $aData
     */
    public function TopicShow($aData)
    {
        $oTopic = $aData['oTopic'];

        if ($this->PluginL10n_L10n_GetLangAliasFromUrl() != $this->PluginL10n_L10n_GetAliasByLang($oTopic->getTopicLang())) {
            $oBlog = $this->Blog_GetBlogL10n($oTopic->getBlog(), $oTopic->getTopicLang());
            $sBlogUrl = $oBlog->getUrl() ? $oBlog->getUrl() . '/' : '';
            $sCanonicalUrl = Router::GetPath('blog', $this->PluginL10n_L10n_GetAliasByLang($oTopic->getTopicLang())) . $sBlogUrl . $oTopic->getId() . '.html';
            $this->Viewer_Assign('sCanonicalUrl', $sCanonicalUrl);
        }

        if ($oTopic->getTopicOriginalId()) {
            // подгружаем оригинал топика
            $this->Viewer_Assign('oTopicOriginal', $this->Topic_GetTopicById($oTopic->getTopicOriginalId()));
            $this->_addTranslationBlock();
        } else {
            // подгружаем переводы топика
            $aResult = $this->Topic_GetTopicTranslatesByTopicId($oTopic->getId());
            if ($aTopics = $aResult['collection']) {
                $this->_addTranslationBlock();
            }

            $this->Viewer_Assign('aTopicTranslates', $aTopics);
        }
        if ($this->_IsAllowTopicTranslation($oTopic)) {
            $this->Viewer_AddBlock(
                    'right',
                    'L10nCreateTranslation',
                    array('plugin' => 'l10n'),
                    500
            );
        }
    }

    /**
     * Хук на проверку полей формы создания/редактирования топика
     *
     * @return boolean
     */
    public function CheckTopicFields($aData)
    {
        $btnOk = &$aData['bOk'];

        if (Router::GetActionEvent() == 'add' && Router::GetParam(0) == 'translate') {
            if (!$oTopicOriginal = $this->_getTopicOriginalByUrParams()) {
                $this->Message_AddError(
                        $this->Lang_Get('l10n_topic_translate_not_exist'), $this->Lang_Get('error'));
                $btnOk = false;
            }
        }

        if ($oTopicOriginal = $this->_getTopicOriginalByUrParams()) {
            // проверка или такой язык есть в списке доступных
            $sTopicLang = getRequest('topic_lang', null, 'post');
            if (!$this->PluginL10n_L10n_IsAllowedLang($sTopicLang)) {
                $this->Message_AddError(
                        $this->Lang_Get('l10n_unavaliable_lang'),
                        $this->Lang_Get('error'));
                $btnOk = false;
            }

            // проверка или язык топика-перевода не совпадает с языком топика-оригинала
            if ($sTopicLang == $oTopicOriginal->getTopicLang()) {
                $this->Message_AddError(
                        $this->Lang_Get('l10n_topic_translate_lang_error'),
                        $this->Lang_Get('error'));
                $btnOk = false;
            }

            // проверка или топик-оригинал сам не является топиком-переводом
            if ($oTopicOriginal->getTopicOriginalId()) {
                $this->Message_AddErrorSingle(
                        $this->Lang_Get('l10n_topic_translate_not_original'),
                        $this->Lang_Get('error'));
                $btnOk = false;
            }
        }

        return $aData;
    }

    /**
     * Получить топик-оригинал в зависимости от параметров присутствующих в URL
     *
     * @return ModuleTopic_EntityTopic|null
     */
    private function _getTopicOriginalByUrParams()
    {
        if (Router::GetActionEvent() == 'edit') {
            $oTopic = $this->Topic_GetTopicById(Router::GetParam(0));
            if ($oTopic->getTopicOriginalId()) {
                return $this->Topic_GetTopicById($oTopic->getTopicOriginalId());
            }
        } elseif (Router::GetActionEvent() == 'add' && Router::GetParam(0) == 'translate') {
            $iTopicId = Router::GetParam(1);
            return $this->Topic_GetTopicById($iTopicId);
        }

        return null;
    }

    /**
     * Добавление кода в начало формы создания/редактирования записи
     *
     * @return string
     */
    public function TemplateFormAddTopicBegin()
    {
        if ($oTopicOriginal = $this->_getTopicOriginalByUrParams()) {
            return '<div class="infomessage">' . $this->Lang_Get('l10n_its_translate_article') . ' "<a href="' . $oTopicOriginal->getUrl() . '">' . $oTopicOriginal->getTitle() . '</a>"</div><br />';
        }
    }

    /**
     * Добавление кода в начало формы создания/редактирования записи
     *
     * @return string
     */
    public function TemplateHtmlHeadEnd()
    {
        $sTemplatePath = Plugin::GetTemplatePath(__CLASS__) . 'link_canonical.tpl';
        if ($this->Viewer_TemplateExists($sTemplatePath)) {
            return $this->Viewer_Fetch($sTemplatePath);
        }
    }

    /**
     * Елемент выбора языка для формы добавления/редактирования записи
     *
     * @return string
     */
    public function TemplateFormAddTopicEnd()
    {
        $sExcludeLang = null;
        // нужно определить является топик переводом или нет
        if ($oTopicOriginal = $this->_getTopicOriginalByUrParams()) {
            $sExcludeLang = $oTopicOriginal->getTopicLang();
        }

        // для топика-перевода убираем с списка языков язык топика-оригинала
        $this->Viewer_Assign('aLangs', $this->PluginL10n_L10n_GetAllowedLangsToViewer($sExcludeLang));
        return $this->Viewer_Fetch(Plugin::GetTemplatePath('l10n') . 'actions/ActionTopic/form_element_select_lang.tpl');
    }

    /**
     * Проверяем, разрешено ли пользователю создавать перевод
     * @return boolean
     */
    protected function _IsAllowTranslation()
    {
        if (!$oUser = $this->User_GetUserCurrent()) {
            return false;
        }
        if (!$oUser->isAdministrator()) {
            return false;
        }
        return true;
    }

    /**
     * Проверяем, можно ли создавать переводы к данному топику
     * @param ModuleTopic_EntityTopic $oTopic
     * @return boolean
     */
    protected function _IsAllowTopicTranslation($oTopic)
    {
        if ($oTopic->getTopicOriginalId()) {
            return false;
        }

        $aResult = $this->Topic_GetTopicTranslatesByTopicId($oTopic->getId());
        $aLang = $this->PluginL10n_ModuleL10n_GetAllowedLangsToViewer($oTopic->getTopicLang());

        if (count($aLang) > $aResult['count']) {
            return $this->_IsAllowTranslation();
        }
        return false;
    }

    protected function _addTranslationBlock()
    {
        if ($priority = Config::Get('plugin.l10n.translate_block.priority')) {
            $this->Viewer_AddBlock(
                    'right',
                    'L10nTranslation',
                    array('plugin' => 'l10n'),
                    $priority
            );
        }
    }

}