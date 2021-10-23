<?php
/**
 * @copyright   &copy; 2005-2021 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Julien BRISWALTER <j1.seth@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2021 04 23
 * @since       PHPBoost 4.0 - 2014 09 02
 * @contributor Sebastien LARTIGUE <babsolune@phpboost.com>
 * @contributor Mipel <mipel@phpboost.com>
*/

class FaqItemFormController extends ModuleController
{
	/**
	 * @var HTMLForm
	 */
	private $form;
	/**
	 * @var FormButtonSubmit
	 */
	private $submit_button;

	private $lang;
	private $form_lang;

	private $item;
	private $is_new_item;

	public function execute(HTTPRequestCustom $request)
	{
		$this->init();

		$this->check_authorizations();

		$this->build_form($request);

		$view = new StringTemplate('# INCLUDE FORM #');
		$view->add_lang($this->lang);

		if ($this->submit_button->has_been_submited() && $this->form->validate())
		{
			$this->save();
			$this->redirect();
		}

		$view->put('FORM', $this->form->display());

		return $this->generate_response($view);
	}

	private function init()
	{
		$this->lang = LangLoader::get('common', 'faq');
		$this->form_lang = LangLoader::get('form-lang');
	}

	private function build_form(HTTPRequestCustom $request)
	{
		$form = new HTMLForm(__CLASS__);
		$form->set_layout_title($this->get_item()->get_id() === null ? $this->lang['faq.add.item'] : ($this->lang['faq.edit.item']));

		$fieldset = new FormFieldsetHTML('faq', $this->form_lang['form.parameters']);
		$form->add_fieldset($fieldset);

		$fieldset->add_field(new FormFieldTextEditor('question', $this->lang['faq.form.question'], $this->get_item()->get_title(), array('required' => true)));

		if (CategoriesService::get_categories_manager()->get_categories_cache()->has_categories())
		{
			$search_category_children_options = new SearchCategoryChildrensOptions();
			$search_category_children_options->add_authorizations_bits(Category::CONTRIBUTION_AUTHORIZATIONS);
			$search_category_children_options->add_authorizations_bits(Category::WRITE_AUTHORIZATIONS);
			$fieldset->add_field(CategoriesService::get_categories_manager()->get_select_categories_form_field('id_category', $this->form_lang['form.category'], $this->get_item()->get_id_category(), $search_category_children_options));
		}

		$fieldset->add_field(new FormFieldRichTextEditor('answer', $this->lang['faq.form.answer'], $this->get_item()->get_content(), array('rows' => 15, 'required' => true)));

		if (CategoriesAuthorizationsService::check_authorizations($this->get_item()->get_id_category())->moderation())
		{
			$fieldset->add_field(new FormFieldCheckbox('approved', $this->form_lang['form.approve'], $this->get_item()->is_approved()));
		}

		$this->build_contribution_fieldset($form);

		$fieldset->add_field(new FormFieldHidden('referrer', $request->get_url_referrer()));

		$this->submit_button = new FormButtonDefaultSubmit();
		$form->add_button($this->submit_button);
		$form->add_button(new FormButtonReset());

		$this->form = $form;
	}

	private function build_contribution_fieldset($form)
	{
		$contribution = LangLoader::get('contribution-lang');
		if ($this->get_item()->get_id() === null && $this->is_contributor_member())
		{
			$fieldset = new FormFieldsetHTML('contribution', $contribution['contribution.contribution']);
			$fieldset->set_description(MessageHelper::display($contribution['contribution.warning'], MessageHelper::WARNING)->render());
			$form->add_fieldset($fieldset);

			$fieldset->add_field(new FormFieldRichTextEditor('contribution_description', $contribution['contribution.description'], '', array('description' => $contribution['contribution.description.clue'])));
		}
	}

	private function is_contributor_member()
	{
		return (!CategoriesAuthorizationsService::check_authorizations()->write() && CategoriesAuthorizationsService::check_authorizations()->contribution());
	}

	private function get_item()
	{
		if ($this->item === null)
		{
			$id = AppContext::get_request()->get_getint('id', 0);
			if (!empty($id))
			{
				try {
					$this->item = FaqService::get_item('WHERE id=:id', array('id' => $id));
				} catch (RowNotFoundException $e) {
					$error_controller = PHPBoostErrors::unexisting_page();
					DispatchManager::redirect($error_controller);
				}
			}
			else
			{
				$this->is_new_item = true;
				$this->item = new FaqItem();
				$this->item->init_default_properties(AppContext::get_request()->get_getint('id_category', Category::ROOT_CATEGORY));
			}
		}
		return $this->item;
	}

	private function check_authorizations()
	{
		$item = $this->get_item();

		if ($item->get_id() === null)
		{
			if (!$item->is_authorized_to_add())
			{
				$error_controller = PHPBoostErrors::user_not_authorized();
				DispatchManager::redirect($error_controller);
			}
		}
		else
		{
			if (!$item->is_authorized_to_edit())
			{
				$error_controller = PHPBoostErrors::user_not_authorized();
				DispatchManager::redirect($error_controller);
			}
		}
		if (AppContext::get_current_user()->is_readonly())
		{
			$controller = PHPBoostErrors::user_in_read_only();
			DispatchManager::redirect($controller);
		}
	}

	private function save()
	{
		$item = $this->get_item();

		$item->set_title($this->form->get_value('question'));
		$item->set_rewrited_title(Url::encode_rewrite($item->get_title()));

		if (CategoriesService::get_categories_manager()->get_categories_cache()->has_categories())
			$item->set_id_category($this->form->get_value('id_category')->get_raw_value());

		$item->set_content($this->form->get_value('answer'));

		if ($item->get_q_order() === null)
		{
			$number_questions_in_category = FaqService::count('WHERE id_category = :id_category', array('id_category' => $item->get_id_category()));
			$item->set_q_order($number_questions_in_category + 1);
		}

		if (CategoriesAuthorizationsService::check_authorizations($item->get_id_category())->moderation())
		{
			if ($this->form->get_value('approved'))
				$item->approve();
			else
				$item->unapprove();
		}
		else if (CategoriesAuthorizationsService::check_authorizations($item->get_id_category())->contribution() && !CategoriesAuthorizationsService::check_authorizations($item->get_id_category())->write())
			$item->unapprove();

		if ($item->get_id() === null)
		{
			$id = FaqService::add($item);
		}
		else
		{
			$id = $item->get_id();
			FaqService::update($item);
		}

		$this->contribution_actions($item, $id);

		FaqService::clear_cache();
	}

	private function contribution_actions(FaqItem $item, $id)
	{
		if ($item->get_id() === null)
		{
			if ($this->is_contributor_member())
			{
				$contribution = new Contribution();
				$contribution->set_id_in_module($id);
				$contribution->set_description(stripslashes($this->form->get_value('contribution_description')));
				$contribution->set_entitled($item->get_title());
				$contribution->set_fixing_url(FaqUrlBuilder::edit($id)->relative());
				$contribution->set_poster_id(AppContext::get_current_user()->get_id());
				$contribution->set_module('faq');
				$contribution->set_auth(
					Authorizations::capture_and_shift_bit_auth(
						CategoriesService::get_categories_manager()->get_heritated_authorizations($item->get_id_category(), Category::MODERATION_AUTHORIZATIONS, Authorizations::AUTH_CHILD_PRIORITY),
						Category::MODERATION_AUTHORIZATIONS, Contribution::CONTRIBUTION_AUTH_BIT
					)
				);
				ContributionService::save_contribution($contribution);
			}
		}
		else
		{
			$corresponding_contributions = ContributionService::find_by_criteria('faq', $id);
			if (count($corresponding_contributions) > 0)
			{
				foreach ($corresponding_contributions as $contribution)
				{
					$contribution->set_status(Event::EVENT_STATUS_PROCESSED);
					ContributionService::save_contribution($contribution);
				}
			}
		}
		$item->set_id($id);
	}

	private function redirect()
	{
		$item = $this->get_item();
		$category = $item->get_category();

		if ($this->is_new_item && $this->is_contributor_member() && !$item->is_approved())
		{
			DispatchManager::redirect(new UserContributionSuccessController());
		}
		elseif ($item->is_approved())
		{
			if ($this->is_new_item)
				AppContext::get_response()->redirect(FaqUrlBuilder::display($category->get_id(), $category->get_rewrited_name(), $item->get_id()), StringVars::replace_vars($this->lang['faq.message.success.add'], array('title' => $item->get_title())));
			else
				AppContext::get_response()->redirect(($this->form->get_value('referrer') ? $this->form->get_value('referrer') : FaqUrlBuilder::display($category->get_id(), $category->get_rewrited_name(), $item->get_id())), StringVars::replace_vars($this->lang['faq.message.success.edit'], array('title' => $item->get_title())));
		}
		else
		{
			if ($this->is_new_item)
				AppContext::get_response()->redirect(FaqUrlBuilder::display_pending_items(), StringVars::replace_vars($this->lang['faq.message.success.add'], array('title' => $item->get_title())));
			else
				AppContext::get_response()->redirect(($this->form->get_value('referrer') ? $this->form->get_value('referrer') : FaqUrlBuilder::display_pending_items()), StringVars::replace_vars($this->lang['faq.message.success.edit'], array('title' => $item->get_title())));
		}
	}

	private function generate_response(View $view)
	{
		$item = $this->get_item();

		$location_id = $item->get_id() ? 'faq-edit-'. $item->get_id() : '';

		$response = new SiteDisplayResponse($view, $location_id);
		$graphical_environment = $response->get_graphical_environment();

		$breadcrumb = $graphical_environment->get_breadcrumb();
		$breadcrumb->add($this->lang['faq.module.title'], FaqUrlBuilder::home());

		if ($item->get_id() === null)
		{
			$graphical_environment->set_page_title($this->lang['faq.add.item'], $this->lang['faq.module.title']);
			$breadcrumb->add($this->lang['faq.add.item'], FaqUrlBuilder::add($item->get_id_category()));
			$graphical_environment->get_seo_meta_data()->set_description($this->lang['faq.add.item']);
			$graphical_environment->get_seo_meta_data()->set_canonical_url(FaqUrlBuilder::add($item->get_id_category()));
		}
		else
		{
			if (!AppContext::get_session()->location_id_already_exists($location_id))
				$graphical_environment->set_location_id($location_id);

			$graphical_environment->set_page_title($this->lang['faq.edit.item'], $this->lang['faq.module.title']);
			$graphical_environment->get_seo_meta_data()->set_description($this->lang['faq.edit.item']);
			$graphical_environment->get_seo_meta_data()->set_canonical_url(FaqUrlBuilder::edit($item->get_id()));

			$categories = array_reverse(CategoriesService::get_categories_manager()->get_parents($item->get_id_category(), true));
			foreach ($categories as $id => $category)
			{
				if ($category->get_id() != Category::ROOT_CATEGORY)
					$breadcrumb->add($category->get_name(), FaqUrlBuilder::display_category($category->get_id(), $category->get_rewrited_name()));
			}
			$category = $item->get_category();
			$breadcrumb->add($item->get_title(), FaqUrlBuilder::display($category->get_id(), $category->get_rewrited_name(), $item->get_id()));
			$breadcrumb->add($this->lang['faq.edit.item'], FaqUrlBuilder::edit($item->get_id()));
		}

		return $response;
	}
}
?>
