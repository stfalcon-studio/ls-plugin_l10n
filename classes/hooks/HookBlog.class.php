<?php

/**
 * Плагин L10n. Хуки для топиков
 */
class PluginL10n_HookBlog extends Hook {

    /**
     * Регистрируем нужные хуки
     *
     * @return void
     */
    public function RegisterHook() {
        // хук на форму добавления/редактирования топика
        $this->AddHook('template_form_add_blog_begin',
                'TemplateFormAddBlogBegin', __CLASS__);
        $this->AddHook('template_form_add_blog_end',
                'TemplateFormAddBlogEnd', __CLASS__);

        $this->AddHook('action_event_blog_before',
                'ActionEventBlogBefore', __CLASS__);


        $this->AddHook('blog_edit_after',
                'BlogEditAfter', __CLASS__);
        $this->AddHook('blog_add_after',
                'BlogEditAfter', __CLASS__);

        $this->AddHook('module_blog_createpersonalblog_after',
                'BlogEditAfter', __CLASS__);
    }

    /**
     * Метод вызывается при работе с ActionBlog
     *
     * @param array $aData
     */
    public function ActionEventBlogBefore($aData) {
        $sEvent = $aData['event'];

        $sCurrentLang = Config::Get('lang.current');

        // подключение js плагина и стилей для табов
        if (in_array($sEvent, array('edit', 'add'))) {
            $this->Viewer_AppendScript(
                    Plugin::GetTemplatePath(__CLASS__) . 'lib/external/MooTools_1.2/mootools-1.2.4-more.js');
            $this->Viewer_AppendScript(
                    Plugin::GetTemplatePath(__CLASS__) . 'lib/external/MooTools_1.2/plugins/TabPane/TabPane.js');
            $this->Viewer_PrependStyle(
                    Plugin::GetTemplatePath(__CLASS__) . 'lib/external/MooTools_1.2/plugins/TabPane/style.css');

            $this->Viewer_Assign('sCurrentLangText', 'l10n_lang_' . $sCurrentLang);
            $this->Viewer_Assign('aLangs', $this->PluginL10n_L10n_GetAllowedLangsToViewer($sCurrentLang));
        }

        // загрузка данных для редактирования топика
        if (in_array($sEvent, array('edit'))) {
            $sBlogId = Router::GetParam(0);
            if (!$oBlog = $this->Blog_GetBlogById($sBlogId)) {
                return;
            }

            $aLangs = $this->PluginL10n_L10n_GetAllowedLangs($sCurrentLang);
            foreach ($aLangs as $sLang) {
                $oBlog = $this->Blog_GetBlogL10n($oBlog, $sLang);
                $_REQUEST[$sLang][0] = $oBlog->getTitle();
                $_REQUEST[$sLang][1] = $oBlog->getUrl();
                $_REQUEST[$sLang][2] = $oBlog->getDescription();
            }
        }
    }

    /**
     * Сохранение данных для переводов
     *
     * @param array $aData
     */
    public function BlogEditAfter($aData) {
	// сохраняем для текущего языка
	$sCurrentLang = $this->Lang_GetLang();
        if (isset($aData['oBlog'])) {
	    // коллективный блог
            $oBlog = clone $aData['oBlog'];
            $bPersonalBlog = false;
	    
	    $sTitleText = getRequest('blog_title', null, 'post');
            $sDescriptionText = $this->Text_Parser(getRequest('blog_description', null, 'post'));
            $oBlog->setBlogUrlL10n(getRequest('blog_url', null, 'post'));
        } elseif(isset($aData['result']) && is_a($aData['result'], 'ModuleBlog_EntityBlog')) {
	    // персональный блог
            $oBlog = clone $aData['result'];
            $bPersonalBlog = true;
            $sOwnerLogin = (isset($aData['params'][0])) ? $aData['params'][0]->GetLogin() : '';
	    
            $sTitleText = $this->Lang_Get('blogs_personal_title') . ' ' . $sOwnerLogin;
            $sDescriptionText = $this->Lang_Get('blogs_personal_description');
	    /**
	     * @todo: Андрей прав. в персональных блогах null вызывает ошибку
             */
            $oBlog->setBlogUrlL10n(getRequest('blog_url', " ", 'post'));
        }

        $oBlog->setBlogLang($sCurrentLang);
        $oBlog->setBlogTitleL10n($sTitleText);
        $oBlog->setBlogDescriptionL10n($sDescriptionText);
        /**
         * @todo
         * - добавить проверку на существование локализации перед тем как сохранять урл
         * - добавить валидацию данных для переводов
         * - избавиться от дублирования данных в таблице blog & blog_l10n (в blog_l10n хранить только переводы)
         */
        $this->Blog_ReplaceBlogL10n($oBlog);

        // сохраняем переводы
        $aLangs = $this->PluginL10n_L10n_GetAllowedLangs($sCurrentLang);
        foreach ($aLangs as $sLang) {
	    $this->Lang_SetLang($sLang);
	    
            if ($bPersonalBlog) {
		// для персонального блога формируется название вида "Blog by username"
                $sTitleText = $this->Lang_Get('blogs_personal_title') . ' ' . $sOwnerLogin;
                $sDescriptionText = $this->Lang_Get('blogs_personal_description');
            } else {
                $sTitleText = getRequest('blog_title' . '_' . $sLang, null, 'post');
                $sDescriptionText = $this->Text_Parser(getRequest('blog_description' . '_' . $sLang, null, 'post'));
            }

            $oBlog->setBlogLang($sLang);
            $oBlog->setBlogTitleL10n($sTitleText);
            $oBlog->setBlogDescriptionL10n($sDescriptionText);
	    $oBlog->setBlogUrlL10n(getRequest('blog_url' . '_' . $sLang, null, 'post'));
            if (!$this->Blog_ReplaceBlogL10n($oBlog)) {
                $this->Message_AddError($this->Lang_Get('system_error'), $this->Lang_Get('error'));
            }
        }

	$this->Lang_SetLang($sCurrentLang);
    }

    /**
     * Шаблон с списком закладок. Цепляется на хук в начало формы редактирования блога
     *
     * @return string
     */
    public function TemplateFormAddBlogBegin() {
        return $this->Viewer_Fetch(Plugin::GetTemplatePath('l10n') . 'actions/ActionBlog/form_add_blog_begin.tpl');
    }

    /**
     * Шаблон с полями для переводов. Цепляется на хук в конец формы редактирования блога
     *
     * @return string
     */
    public function TemplateFormAddBlogEnd() {
        return $this->Viewer_Fetch(Plugin::GetTemplatePath('l10n') . 'actions/ActionBlog/form_add_blog_end.tpl');
    }

}
