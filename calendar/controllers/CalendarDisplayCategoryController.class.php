<?php
/**
 * @copyright   &copy; 2005-2020 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Julien BRISWALTER <j1.seth@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2020 12 11
 * @since       PHPBoost 4.0 - 2013 08 21
 * @contributor Sebastien LARTIGUE <babsolune@phpboost.com>
*/

class CalendarDisplayCategoryController extends ModuleController
{
	private $lang;
	private $view;

	private $category;

	public function execute(HTTPRequestCustom $request)
	{
		$this->check_authorizations();

		$this->init();

		$this->build_view($request);

		return $this->generate_response();
	}

	private function init()
	{
		$this->lang = LangLoader::get('common', 'calendar');
		$this->view = new FileTemplate('calendar/CalendarDisplaySeveralEventsController.tpl');
		$this->view->add_lang($this->lang);
	}

	private function build_view(HTTPRequestCustom $request)
	{
		$year = $request->get_getint('year', date('Y'));
		$month = $request->get_getint('month', date('n'));
		$day = $request->get_getint('day', date('j'));

		if (!checkdate($month, $day, $year))
		{
			$this->view->put('MESSAGE_HELPER', MessageHelper::display($this->lang['calendar.error.invalid.date'], MessageHelper::ERROR));

			$year = date('Y');
			$month = date('n');
			$day = date('j');
		}

		$this->view->put_all(array(
			'CALENDAR' => CalendarAjaxCalendarController::get_view(false, $year, $month),
			'EVENTS' => CalendarAjaxEventsController::get_view($year, $month, $day)
		));

		return $this->view;
	}

	private function check_authorizations()
	{
		$id_cat = $this->get_category()->get_id();
		if (!CategoriesAuthorizationsService::check_authorizations($id_cat)->read())
		{
			$error_controller = PHPBoostErrors::user_not_authorized();
			DispatchManager::redirect($error_controller);
		}
	}

	private function get_category()
	{
		if ($this->category === null)
		{
			$id = AppContext::get_request()->get_getint('id_category', 0);
			if (!empty($id))
			{
				try {
					$this->category = CategoriesService::get_categories_manager('calendar')->get_categories_cache()->get_category($id);
				} catch (CategoryNotFoundException $e) {
					$error_controller = PHPBoostErrors::unexisting_page();
					DispatchManager::redirect($error_controller);
				}
			}
			else
			{
				$this->category = CategoriesService::get_categories_manager('calendar')->get_categories_cache()->get_category(Category::ROOT_CATEGORY);
			}
		}
		return $this->category;
	}

	private function generate_response()
	{
		$response = new SiteDisplayResponse($this->view);
		$graphical_environment = $response->get_graphical_environment();

		if ($this->get_category()->get_id() != Category::ROOT_CATEGORY)
			$graphical_environment->set_page_title($this->get_category()->get_name(), $this->lang['module.title']);
		else
			$graphical_environment->set_page_title($this->lang['module.title']);

		$graphical_environment->get_seo_meta_data()->set_description(StringVars::replace_vars($this->lang['calendar.seo.description.root'], array('site' => GeneralConfig::load()->get_site_name())));
		$graphical_environment->get_seo_meta_data()->set_canonical_url(CalendarUrlBuilder::home());

		$breadcrumb = $graphical_environment->get_breadcrumb();
		$breadcrumb->add($this->lang['module.title'], CalendarUrlBuilder::home());

		return $response;
	}

	public static function get_view()
	{
		$object = new self();
		$object->init();
		$object->check_authorizations();
		$object->build_view(AppContext::get_request());
		return $object->view;
	}
}
?>
