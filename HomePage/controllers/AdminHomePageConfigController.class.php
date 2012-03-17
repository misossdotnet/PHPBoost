<?php
/*##################################################
 *                      AdminHomePageConfigController.class.php
 *                            -------------------
 *   begin                : March 14, 2012
 *   copyright            : (C) 2012 K�vin MASSY
 *   email                : soldier.weasel@gmail.com
 *
 *
 ###################################################
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 ###################################################*/

class AdminHomePageConfigController extends AdminController
{
	private $lang;
	private $config;

	public function execute(HTTPRequest $request)
	{
		$this->init();
		$this->build_view();

		return $this->response();
	}
	
	private function init()
	{
		$this->lang = LangLoader::get('common', 'HomePage');
		$this->config = HomePageConfig::load();
	}

	private function build_view()
	{
		$view = new FileTemplate('HomePage/AdminHomePageConfigController.tpl');
		$view->add_lang($this->lang);
		
		$view->put_all(array(
			'CONFIGURATION' => $this->build_form_configuration()->display()
		));
		
		for ($i = 0; $i < $this->config->get_number_columns(); $i++) {
			$view->assign_block_vars('containers', array(
				'ID' => $i
			));
			
			$view->assign_block_vars('containers.elements', array(
				'ID' => $i,
				'PLUGIN' => 'TEST'
			));
		}

		$view->put_all(array(
			'WIDTH_CSS_CONTAINERS' => $this->get_width_containers(),
			'LIST_CONTAINERS' => $this->get_list_containers()
		));
		
		return $view;
	}
	
	private function get_list_containers()
	{
		switch ($this->config->get_number_columns()) {
			case 1:
				return "'container0'";
			break;
			case 2:
				return "'container0', 'container1'";
			break;
			case 3:
				return "'container0', 'container1', 'container2'";
			break;
		}
	}
	
	private function get_width_containers()
	{
		switch ($this->config->get_number_columns()) {
			case 1:
				return '98%';
			break;
			case 2:
				return '46%';
			break;
			case 3:
				return '20%';
			break;
		}
	}
	
	private function build_form_configuration()
	{
		$form = new HTMLForm('HomePageConfig');
		$fieldset = new FormFieldsetHorizontal('HomePageConfig');
		$form->add_fieldset($fieldset);
		$fieldset->add_field(new FormFieldSimpleSelectChoice('number_columns', $this->lang['number_columns'] . ' : ', $this->config->get_number_columns(), array(
			new FormFieldSelectChoiceOption('1', '1'),
			new FormFieldSelectChoiceOption('2', '2'),
			new FormFieldSelectChoiceOption('3', '3')
		)));
		return $form;
	}
	
	private function response()
	{
		$response = new AdminDisplayResponse($this->build_view());
		$env = $response->get_graphical_environment();
		$env->set_page_title($this->lang['config']);
		return $response;
	}
}
?>