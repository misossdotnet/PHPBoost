<?php
/**
 * @copyright   &copy; 2005-2020 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Julien BRISWALTER <j1.seth@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2020 12 21
 * @since       PHPBoost 4.0 - 2013 11 08
 * @contributor Sebastien LARTIGUE <babsolune@phpboost.com>
*/

class CalendarSuscribeController extends ModuleController
{
	private $item;

	public function execute(HTTPRequestCustom $request)
	{
		$item_id = $request->get_getint('event_id', 0);
		$current_user_id = AppContext::get_current_user()->get_id();

		if (!empty($item_id))
		{
			$this->get_item($item_id);

			$this->check_authorizations();

			if (!in_array($current_user_id, array_keys($this->item->get_participants())))
			{
				CalendarService::add_participant($item_id, $current_user_id);
				CalendarService::clear_cache();
			}

			$category = $this->item->get_content()->get_category();

			AppContext::get_response()->redirect($request->get_url_referrer() ? $request->get_url_referrer() : CalendarUrlBuilder::display_item($category->get_id(), $category->get_rewrited_name(), $item_id, $this->item->get_content()->get_rewrited_title()));
		}
		else
		{
			$error_controller = PHPBoostErrors::unexisting_page();
			DispatchManager::redirect($error_controller);
		}
	}

	private function get_item($item_id)
	{
		try {
			$this->item = CalendarService::get_item('WHERE id_event = :id', array('id' => $item_id));
		} catch (RowNotFoundException $e) {
			$error_controller = PHPBoostErrors::unexisting_page();
			DispatchManager::redirect($error_controller);
		}
	}

	private function check_authorizations()
	{
		if (!$this->item->get_content()->is_registration_authorized() || !$this->item->get_content()->is_authorized_to_register() || ($this->item->get_content()->is_registration_authorized() && $this->item->get_content()->get_max_registered_members() && $this->item->get_registered_members_number() == $this->item->get_content()->get_max_registered_members()))
		{
			$error_controller = PHPBoostErrors::user_not_authorized();
			DispatchManager::redirect($error_controller);
		}
		if (time() > $this->item->get_start_date()->get_timestamp())
		{
			$error_controller = new UserErrorController(LangLoader::get_message('error', 'status-messages-common'), LangLoader::get_message('calendar.suscribe.notice.expired.event.date', 'common', 'calendar'));
			DispatchManager::redirect($error_controller);
		}
		if (AppContext::get_current_user()->is_readonly())
		{
			$error_controller = PHPBoostErrors::user_in_read_only();
			DispatchManager::redirect($error_controller);
		}
	}
}
?>
