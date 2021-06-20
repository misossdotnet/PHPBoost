<?php
/**
 * @copyright   &copy; 2005-2020 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Julien BRISWALTER <j1.seth@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2021 06 20
 * @since       PHPBoost 4.0 - 2013 11 24
 * @contributor xela <xela@phpboost.com>
 * @contributor Sebastien LARTIGUE <babsolune@phpboost.com>
*/

class MediaTreeLinks implements ModuleTreeLinksExtensionPoint
{
	public function get_actions_tree_links()
	{
		$module_id = 'media';

		$lang = LangLoader::get('common', $module_id);
		$tree = new ModuleTreeLinks();

		$tree->add_link(new ModuleLink(LangLoader::get_message('category.categories.manage', 'category-lang'), CategoriesUrlBuilder::manage($module_id), CategoriesAuthorizationsService::check_authorizations(Category::ROOT_CATEGORY, $module_id)->manage()));
		$tree->add_link(new ModuleLink(LangLoader::get_message('category.category.add', 'category-lang'), CategoriesUrlBuilder::add(AppContext::get_request()->get_getint('id_category', Category::ROOT_CATEGORY), $module_id), CategoriesAuthorizationsService::check_authorizations(Category::ROOT_CATEGORY, $module_id)->manage()));

		$tree->add_link(new ModuleLink($lang['media.management'], MediaUrlBuilder::manage(), CategoriesAuthorizationsService::check_authorizations(Category::ROOT_CATEGORY, $module_id)->moderation()));
		$tree->add_link(new ModuleLink($lang['media.add.item'], MediaUrlBuilder::add(), CategoriesAuthorizationsService::check_authorizations(Category::ROOT_CATEGORY, $module_id)->moderation()));

		$tree->add_link(new AdminModuleLink(LangLoader::get_message('form.configuration', 'form-lang'), MediaUrlBuilder::configuration()));

		if (!CategoriesAuthorizationsService::check_authorizations(Category::ROOT_CATEGORY, $module_id)->moderation())
		{
			$tree->add_link(new ModuleLink($lang['media.add.item'], MediaUrlBuilder::add(), CategoriesAuthorizationsService::check_authorizations(Category::ROOT_CATEGORY, $module_id)->write() || CategoriesAuthorizationsService::check_authorizations(Category::ROOT_CATEGORY, $module_id)->contribution()));
		}

		if (ModulesManager::get_module($module_id)->get_configuration()->get_documentation())
			$tree->add_link(new ModuleLink(LangLoader::get_message('form.documentation', 'form-lang'), ModulesManager::get_module('media')->get_configuration()->get_documentation(), CategoriesAuthorizationsService::check_authorizations(Category::ROOT_CATEGORY, $module_id)->write() || CategoriesAuthorizationsService::check_authorizations(Category::ROOT_CATEGORY, $module_id)->contribution() || CategoriesAuthorizationsService::check_authorizations(Category::ROOT_CATEGORY, $module_id)->moderation()));

		return $tree;
	}
}
?>
