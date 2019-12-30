<?php
/**
 * @copyright   &copy; 2005-2020 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Julien BRISWALTER <j1.seth@phpboost.com>
 * @version     PHPBoost 5.3 - last update: 2019 12 21
 * @since       PHPBoost 4.1 - 2014 08 21
 * @contributor Kevin MASSY <reidlos@phpboost.com>
 * @contributor Arnaud GENET <elenwii@phpboost.com>
 * @contributor Sebastien LARTIGUE <babsolune@phpboost.com>
 * @contributor Mipel <mipel@phpboost.com>
*/

class AdminWebConfigController extends AdminModuleController
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
	private $admin_common_lang;

	/**
	 * @var WebConfig
	 */
	private $config;
	private $comments_config;
	private $content_management_config;

	public function execute(HTTPRequestCustom $request)
	{
		$this->init();

		$this->build_form();

		$tpl = new StringTemplate('# INCLUDE MSG # # INCLUDE FORM #');
		$tpl->add_lang($this->lang);

		if ($this->submit_button->has_been_submited() && $this->form->validate())
		{
			$this->save();
			$this->form->get_field_by_id('display_descriptions_to_guests')->set_hidden($this->config->get_category_display_type() == WebConfig::FULL_VIEW);
			$tpl->put('MSG', MessageHelper::display(LangLoader::get_message('message.success.config', 'status-messages-common'), MessageHelper::SUCCESS, 5));
		}

		$tpl->put('FORM', $this->form->display());

		return new DefaultAdminDisplayResponse($tpl);
	}

	private function init()
	{
		$this->config = WebConfig::load();
		$this->comments_config = CommentsConfig::load();
		$this->content_management_config = ContentManagementConfig::load();
		$this->lang = LangLoader::get('common', 'web');
		$this->admin_common_lang = LangLoader::get('admin-common');
	}

	private function build_form()
	{
		$form = new HTMLForm(__CLASS__);

		$fieldset = new FormFieldsetHTMLHeading('configuration', StringVars::replace_vars(LangLoader::get_message('configuration.module.title', 'admin-common'), array('module_name' => $this->get_module()->get_configuration()->get_name())));
		$form->add_fieldset($fieldset);

		$fieldset->add_field(new FormFieldNumberEditor('items_number_per_page', $this->admin_common_lang['config.items_number_per_page'], $this->config->get_items_number_per_page(),
			array('min' => 1, 'max' => 50, 'required' => true),
			array(new FormFieldConstraintIntegerRange(1, 50))
		));

		$fieldset->add_field(new FormFieldNumberEditor('categories_number_per_page', $this->admin_common_lang['config.categories_number_per_page'], $this->config->get_categories_number_per_page(),
			array('min' => 1, 'max' => 50, 'required' => true),
			array(new FormFieldConstraintIntegerRange(1, 50))
		));

		$fieldset->add_field(new FormFieldNumberEditor('categories_number_per_row', $this->admin_common_lang['config.categories.number.per.row'], $this->config->get_categories_number_per_row(),
			array('min' => 1, 'max' => 4, 'required' => true),
			array(new FormFieldConstraintIntegerRange(1, 4))
		));

		$fieldset->add_field(new FormFieldSimpleSelectChoice('items_default_sort', $this->admin_common_lang['config.items_default_sort'], $this->config->get_items_default_sort_field() . '-' . $this->config->get_items_default_sort_mode(), $this->get_sort_options()));

		$fieldset->add_field(new FormFieldSimpleSelectChoice('category_display_type', $this->lang['config.category_display_type'], $this->config->get_category_display_type(),
			array(
				new FormFieldSelectChoiceOption($this->lang['config.category_display_type.display_summary'], WebConfig::SUMMARY_VIEW),
				new FormFieldSelectChoiceOption($this->lang['config.category_display_type.display_all_content'], WebConfig::FULL_VIEW),
				new FormFieldSelectChoiceOption($this->lang['config.category_display_type.display_table'], WebConfig::TABLE_VIEW)
			),
			array('events' => array('click' => '
				if (HTMLForms.getField("category_display_type").getValue() != \'' . WebConfig::FULL_VIEW . '\') {
					HTMLForms.getField("display_descriptions_to_guests").enable();
				} else {
					HTMLForms.getField("display_descriptions_to_guests").disable();
				}'
			))
		));

		$fieldset->add_field(new FormFieldCheckbox('display_descriptions_to_guests', $this->lang['config.display_descriptions_to_guests'], $this->config->are_descriptions_displayed_to_guests(),
			array(
				'class' => 'custom-checkbox',
				'hidden' => $this->config->get_category_display_type() == WebConfig::FULL_VIEW
			)
		));

		$fieldset->add_field(new FormFieldRichTextEditor('root_category_description', $this->admin_common_lang['config.root_category_description'], $this->config->get_root_category_description(),
			array('rows' => 8, 'cols' => 47)
		));

                $fieldset->add_field(new FormFieldRichTextEditor('default_contents', $this->lang['web.default.contents'], $this->config->get_default_contents(),
			array('rows' => 8, 'cols' => 47)
		));

		$fieldset = new FormFieldsetHTML('menu', $this->lang['config.partners_menu']);
		$form->add_fieldset($fieldset);

		$fieldset->add_field(new FormFieldSimpleSelectChoice('partners_sort', $this->lang['config.partners_sort'], $this->config->get_partners_sort_field() . '-' . $this->config->get_partners_sort_mode(), $this->get_sort_options(),
			array('description' => $this->lang['config.partners_sort.explain'])
		));

		$fieldset->add_field(new FormFieldNumberEditor('partners_number_in_menu', $this->lang['config.partners_number_in_menu'], $this->config->get_partners_number_in_menu(),
			array('min' => 1, 'max' => 50, 'required' => true),
			array(new FormFieldConstraintIntegerRange(1, 50))
		));

		$fieldset_authorizations = new FormFieldsetHTML('authorizations_fieldset', LangLoader::get_message('authorizations', 'common'),
			array('description' => $this->admin_common_lang['config.authorizations.explain'])
		);
		$form->add_fieldset($fieldset_authorizations);

		$auth_settings = new AuthorizationsSettings(RootCategory::get_authorizations_settings());
		$auth_settings->build_from_auth_array($this->config->get_authorizations());
		$fieldset_authorizations->add_field(new FormFieldAuthorizationsSetter('authorizations', $auth_settings));

		$this->submit_button = new FormButtonDefaultSubmit();
		$form->add_button($this->submit_button);
		$form->add_button(new FormButtonReset());

		$this->form = $form;
	}

	private function get_sort_options()
	{
		$common_lang = LangLoader::get('common');

		$sort_options = array(
			new FormFieldSelectChoiceOption($common_lang['form.date.creation'] . ' - ' . $common_lang['sort.asc'], WebLink::SORT_DATE . '-' . WebLink::ASC),
			new FormFieldSelectChoiceOption($common_lang['form.date.creation'] . ' - ' . $common_lang['sort.desc'], WebLink::SORT_DATE . '-' . WebLink::DESC),
			new FormFieldSelectChoiceOption($common_lang['sort_by.alphabetic'] . ' - ' . $common_lang['sort.asc'], WebLink::SORT_ALPHABETIC . '-' . WebLink::ASC),
			new FormFieldSelectChoiceOption($common_lang['sort_by.alphabetic'] . ' - ' . $common_lang['sort.desc'], WebLink::SORT_ALPHABETIC . '-' . WebLink::DESC),
			new FormFieldSelectChoiceOption($this->lang['config.sort_type.visits'] . ' - ' . $common_lang['sort.asc'], WebLink::SORT_NUMBER_VISITS . '-' . WebLink::ASC),
			new FormFieldSelectChoiceOption($this->lang['config.sort_type.visits'] . ' - ' . $common_lang['sort.desc'], WebLink::SORT_NUMBER_VISITS . '-' . WebLink::DESC)
		);

		if ($this->comments_config->module_comments_is_enabled('web'))
		{
			$sort_options[] = new FormFieldSelectChoiceOption($common_lang['sort_by.number_comments'] . ' - ' . $common_lang['sort.asc'], WebLink::SORT_NUMBER_COMMENTS . '-' . WebLink::ASC);
			$sort_options[] = new FormFieldSelectChoiceOption($common_lang['sort_by.number_comments'] . ' - ' . $common_lang['sort.desc'], WebLink::SORT_NUMBER_COMMENTS . '-' . WebLink::DESC);
		}

		if ($this->content_management_config->module_notation_is_enabled('web'))
		{
			$sort_options[] = new FormFieldSelectChoiceOption($common_lang['sort_by.best_note'] . ' - ' . $common_lang['sort.asc'], WebLink::SORT_NOTATION . '-' . WebLink::ASC);
			$sort_options[] = new FormFieldSelectChoiceOption($common_lang['sort_by.best_note'] . ' - ' . $common_lang['sort.desc'], WebLink::SORT_NOTATION . '-' . WebLink::DESC);
		}

		return $sort_options;
	}

	private function save()
	{
		$this->config->set_items_number_per_page($this->form->get_value('items_number_per_page'));
		$this->config->set_categories_number_per_page($this->form->get_value('categories_number_per_page'));
		$this->config->set_categories_number_per_row($this->form->get_value('categories_number_per_row'));
		$this->config->set_category_display_type($this->form->get_value('category_display_type')->get_raw_value());

		$items_default_sort = $this->form->get_value('items_default_sort')->get_raw_value();
		$items_default_sort = explode('-', $items_default_sort);
		$this->config->set_items_default_sort_field($items_default_sort[0]);
		$this->config->set_items_default_sort_mode(TextHelper::strtolower($items_default_sort[1]));

		if ($this->config->get_category_display_type() != WebConfig::DISPLAY_ALL_CONTENT)
		{
			if ($this->form->get_value('display_descriptions_to_guests'))
			{
				$this->config->display_descriptions_to_guests();
			}
			else
			{
				$this->config->hide_descriptions_to_guests();
			}
		}

		$this->config->set_root_category_description($this->form->get_value('root_category_description'));

		$partners_sort = $this->form->get_value('partners_sort')->get_raw_value();
		$partners_sort = explode('-', $partners_sort);
		$this->config->set_partners_sort_field($partners_sort[0]);
		$this->config->set_partners_sort_mode($partners_sort[1]);
		$this->config->set_partners_number_in_menu($this->form->get_value('partners_number_in_menu'));
                $this->config->set_default_contents($this->form->get_value('default_contents'));
		$this->config->set_authorizations($this->form->get_value('authorizations')->build_auth_array());

		WebConfig::save();
		CategoriesService::get_categories_manager()->regenerate_cache();
		WebCache::invalidate();
	}
}
?>
