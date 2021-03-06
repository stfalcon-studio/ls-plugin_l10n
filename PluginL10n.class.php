<?php

/* -------------------------------------------------------------------------------------
 * @Plugin Name: L10n
 * @Plugin URI: http://livestreetcms.com/addons/view/105/
 * @Description: Allows the user to choose the interface language
 * @Author: Веб студия stfalcon.com
 * @Author URI: http://stfalcon.com
 * @License: GNU GPL v2, http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * -------------------------------------------------------------------------------------
 */

class PluginL10n extends Plugin
{

	/**
	 * Указанные в массивы наследования будут переданы движку автоматически
	 * перед инициализацией плагина
	 */
	public $aInherits = array(
		'action' => array(
			'ActionSettings' => '_ActionSettings',
			'ActionSearch' => '_ActionSearch',
            'ActionBlog' => '_ActionBlog',
		),
		'module' => array(
			'ModuleBlog' => '_ModuleBlog',
			'ModuleTopic' => '_ModuleTopic',
			'ModuleComment' => '_ModuleComment',
			'ModuleUser' => '_ModuleUser',
			'PluginSitemap_ModuleSitemap' => 'PluginL10n_ModuleSitemap',
		),
		'entity' => array(
			'ModuleBlog_EntityBlog' => '_ModuleBlog_EntityBlog',
			'ModuleTopic_EntityTopic' => '_ModuleTopic_EntityTopic',
		),
		'mapper' => array(
			'ModuleBlog_MapperBlog' => '_ModuleBlog_MapperBlog',
			'ModuleTopic_MapperTopic' => '_ModuleTopic_MapperTopic',
			'ModuleUser_MapperUser' => '_ModuleUser_MapperUser',
			'ModuleComment_MapperComment' => '_ModuleComment_MapperComment',
		),
	);

	/**
	 * Активация плагина
	 *
	 * @return boolean
	 */
	public function Activate()
	{
		$resutls = $this->ExportSQL(dirname(__FILE__) . '/activate.sql');
		$this->Cache_Clean();
		return $resutls['result'];
	}

	/**
	 * Инициализация плагина
	 *
	 * @return void
	 */
	public function Init()
	{
		// путь к папке темплейтов плагина
		$this->Viewer_Assign('sTemplateWebPathPluginL10n', Plugin::GetTemplateWebPath(__CLASS__));
	}

	/**
	 * Деактивация плагина
	 *
	 * @return boolean
	 */
	public function Deactivate()
	{
		$resutls = $this->ExportSQL(dirname(__FILE__) . '/deactivate.sql');
		$this->Cache_Clean();
		return $resutls['result'];
	}

}