<?php
/**
 * @copyright 	&copy; 2005-2019 PHPBoost
 * @license 	https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Julien BRISWALTER <j1.seth@phpboost.com>
 * @version   	PHPBoost 5.2 - last update: 2019 12 06
 * @since   	PHPBoost 4.0 - 2013 11 23
 * @contributor Arnaud GENET <elenwii@phpboost.com>
 * @contributor xela <xela@phpboost.com>
*/

class ForumTreeLinks implements ModuleTreeLinksExtensionPoint
{
	public function get_actions_tree_links()
	{
		$module_id = 'forum';

		$lang = LangLoader::get('common', $module_id);
		$tree = new ModuleTreeLinks();

		$manage_categories_link = new ModuleLink(LangLoader::get_message('categories.manage', 'categories-common'), CategoriesUrlBuilder::manage_categories($module_id), ForumAuthorizationsService::check_authorizations()->manage_categories());
		$manage_categories_link->add_sub_link(new ModuleLink(LangLoader::get_message('categories.manage', 'categories-common'), CategoriesUrlBuilder::manage_categories($module_id), ForumAuthorizationsService::check_authorizations()->manage_categories()));
		$manage_categories_link->add_sub_link(new ModuleLink(LangLoader::get_message('category.add', 'categories-common'), CategoriesUrlBuilder::add_category(AppContext::get_request()->get_getint('id_category', Category::ROOT_CATEGORY), $module_id), ForumAuthorizationsService::check_authorizations()->manage_categories()));
		$tree->add_link($manage_categories_link);

		$manage_ranks_link = new AdminModuleLink($lang['forum.manage_ranks'], ForumUrlBuilder::manage_ranks());
		$manage_ranks_link->add_sub_link(new AdminModuleLink($lang['forum.manage_ranks'], ForumUrlBuilder::manage_ranks()));
		$manage_ranks_link->add_sub_link(new AdminModuleLink($lang['forum.actions.add_rank'], ForumUrlBuilder::add_rank()));
		$tree->add_link($manage_ranks_link);

		$tree->add_link(new AdminModuleLink(LangLoader::get_message('configuration', 'admin-common'), ForumUrlBuilder::configuration()));

		$tree->add_link(new ModuleLink(LangLoader::get_message('moderation_panel', 'main'), ForumUrlBuilder::moderation_panel(), ForumAuthorizationsService::check_authorizations()->moderation()));

		$tree->add_link(new ModuleLink($lang['forum.no_answer'], ForumUrlBuilder::show_no_answer(), ForumAuthorizationsService::check_authorizations()->read()));

		$tree->add_link(new ModuleLink(LangLoader::get_message('module.documentation', 'admin-modules-common'), ModulesManager::get_module('forum')->get_configuration()->get_documentation(), ForumAuthorizationsService::check_authorizations()->moderation()));

		return $tree;
	}
}
?>
