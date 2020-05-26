<?php
/**
 * @copyright   &copy; 2005-2020 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Kevin MASSY <reidlos@phpboost.com>
 * @version     PHPBoost 5.3 - last update: 2020 05 26
 * @since       PHPBoost 3.0 - 2011 04 20
 * @contributor Julien BRISWALTER <j1.seth@phpboost.com>
 * @contributor Arnaud GENET <elenwii@phpboost.com>
 * @contributor xela <xela@phpboost.com>
 * @contributor mipel <mipel@phpboost.com>
 * @contributor Sebastien LARTIGUE <babsolune@phpboost.com>
*/

class AdminLangsNotInstalledListController extends AdminController
{
	private $lang;
	private $view;
	private $form;
	private $submit_button;

	public function execute(HTTPRequestCustom $request)
	{
		$this->init();

		$this->save($request);
		$this->upload_form();

		if ($this->submit_button->has_been_submited() && $this->form->validate())
		{
			$this->upload();
		}

		$this->build_view();

		$this->view->put('UPLOAD_FORM', $this->form->display());

		return new AdminLangsDisplayResponse($this->view, $this->lang['langs.add_lang']);
	}

	private function build_view()
	{
		$phpboost_version = GeneralConfig::load()->get_phpboost_major_version();
		$not_installed_langs = $this->get_not_installed_langs();
		$lang_number = 1;
		foreach($not_installed_langs as $lang)
		{
			$configuration = $lang->get_configuration();
			$author_email = $configuration->get_author_mail();
			$author_website = $configuration->get_author_link();

			$this->view->assign_block_vars('langs_not_installed', array(
				'C_AUTHOR_EMAIL' => !empty($author_email),
				'C_AUTHOR_WEBSITE' => !empty($author_website),
				'C_COMPATIBLE' => $configuration->get_compatibility() == $phpboost_version,
				'C_HAS_PICTURE' => $configuration->has_picture(),
				'LANG_NUMBER' => $lang_number,
				'ID' => $lang->get_id(),
				'PICTURE_URL' => $configuration->get_picture_url()->rel(),
				'NAME' => $configuration->get_name(),
				'VERSION' => $configuration->get_version(),
				'AUTHOR' => $configuration->get_author_name(),
				'AUTHOR_EMAIL' => $author_email,
				'AUTHOR_WEBSITE' => $author_website,
				'COMPATIBILITY' => $configuration->get_compatibility(),
				'AUTHORIZATIONS' => Authorizations::generate_select(Lang::ACCES_LANG, array('r-1' => 1, 'r0' => 1, 'r1' => 1), array(2 => true), $lang->get_id())
			));
			$lang_number++;
		}
		$not_installed_langs_number = count($not_installed_langs);
		$this->view->put_all(array(
			'C_MORE_THAN_ONE_LANG_AVAILABLE' => $not_installed_langs_number > 1,
			'C_LANG_AVAILABLE' => $not_installed_langs_number > 0,
			'LANGS_NUMBER' => $not_installed_langs_number
		));
	}

	private function init()
	{
		$this->lang = LangLoader::get('admin-langs-common');
		$this->view = new FileTemplate('admin/langs/AdminLangsNotInstalledListController.tpl');
		$this->view->add_lang($this->lang);
	}

	private function get_not_installed_langs()
	{
		$langs_not_installed = array();
		$folder_containing_phpboost_langs = new Folder(PATH_TO_ROOT .'/lang/');
		foreach($folder_containing_phpboost_langs->get_folders() as $folder)
		{
			$folder_name = $folder->get_name();
			if (!LangsManager::get_lang_existed($folder_name))
			{
				try
				{
					$langs_not_installed[$folder_name] = new Lang($folder_name);
				}
				catch (IOException $ex)
				{
					continue;
				}
			}
		}

		usort($langs_not_installed, array(__CLASS__, 'callback_sort_langs_by_name'));

		return $langs_not_installed;
	}

	private static function callback_sort_langs_by_name(Lang $lang1, Lang $lang2)
	{
		if (TextHelper::strtolower($lang1->get_configuration()->get_name()) > TextHelper::strtolower($lang2->get_configuration()->get_name()))
		{
			return 1;
		}
		return -1;
	}

	private function save(HTTPRequestCustom $request)
	{
		$lang_number = 1;
		foreach ($this->get_not_installed_langs() as $lang)
		{
			if ($request->get_string('add-' . $lang->get_id(), false) || ($request->get_string('add-selected-langs', false) && $request->get_value('add-checkbox-' . $lang_number, 'off') == 'on'))
			{
				$authorizations = Authorizations::auth_array_simple(Lang::ACCES_LANG, $lang->get_id());
				$this->install_lang($lang->get_id(), $authorizations);
			}
			$lang_number++;
		}
	}

	private function install_lang($id_lang, $authorizations = array())
	{
		LangsManager::install($id_lang, $authorizations);
		$error = LangsManager::get_error();
		if ($error !== null)
		{
			$this->view->put('MSG', MessageHelper::display($error, MessageHelper::WARNING, 10));
		}
		else
		{
			$this->view->put('MSG', MessageHelper::display(LangLoader::get_message('process.success', 'status-messages-common'), MessageHelper::SUCCESS, 10));
		}
	}

	private function upload_form()
	{
		$form = new HTMLForm('upload_lang', '', false);

		$fieldset = new FormFieldsetHTML('upload', $this->lang['langs.upload_lang']);
		$form->add_fieldset($fieldset);

		$fieldset->add_field(new FormFieldFree('warnings', '', $this->lang['langs.add.warning_before_install'],
			array('class' => 'full-field')
		));

		$fieldset->add_field(new FormFieldFilePicker('file', StringVars::replace_vars($this->lang['langs.upload_description'], array('max_size' => File::get_formated_size(ServerConfiguration::get_upload_max_filesize()))),
			array('class' => 'full-field', 'authorized_extensions' => 'gz|zip')
		));

		$this->submit_button = new FormButtonDefaultSubmit();
		$form->add_button($this->submit_button);

		$this->form = $form;
	}

	private function upload()
	{
		$folder_phpboost_langs = PATH_TO_ROOT . '/lang/';
		if (!is_writable($folder_phpboost_langs))
		{
			$is_writable = @chmod($dir, 0777);
		}
		else
		{
			$is_writable = true;
		}

		if ($is_writable)
		{
			$uploaded_file = $this->form->get_value('file');
			if ($uploaded_file !== null)
			{
				$upload = new Upload($folder_phpboost_langs);
				$upload->disableContentCheck();
				if ($upload->file('upload_lang_file', '`([a-z0-9()_-])+\.(gz|zip)+$`iu'))
				{
					$archive = $folder_phpboost_langs . $upload->get_filename();

					if ($upload->get_extension() == 'gz')
					{
						include_once(PATH_TO_ROOT . '/kernel/lib/php/pcl/pcltar.lib.php');
						$archive_content = PclTarList($upload->get_filename());
					}
					else
					{
						include_once(PATH_TO_ROOT . '/kernel/lib/php/pcl/pclzip.lib.php');
						$zip = new PclZip($archive);
						$archive_content = $zip->listContent();
					}

					$lang_name = TextHelper::substr($upload->get_filename(), 0, TextHelper::strpos($upload->get_filename(), '.'));
					$archive_root_content = array();
					$required_files = array('/config.ini', '/admin-common.php', '/common.php');
					foreach ($archive_content as $element)
					{
						if (TextHelper::strpos($element['filename'], $lang_name) === 0)
						{
							$element['filename'] = str_replace($lang_name . '/', '', $element['filename']);
							$archive_root_content[0] = array('filename' => $lang_name, 'folder' => 1);
						}
						if (TextHelper::substr($element['filename'], -1) == '/')
							$element['filename'] = TextHelper::substr($element['filename'], 0, -1);
						if (TextHelper::substr_count($element['filename'], '/') == 0)
							$archive_root_content[] = array('filename' => $element['filename'], 'folder' => ((isset($element['folder']) && $element['folder'] == 1) || (isset($element['typeflag']) && $element['typeflag'] == 5)));
						if (isset($archive_root_content[0]))
						{
							$name_in_archive = str_replace($archive_root_content[0]['filename'] . '/', '/', $element['filename']);

							if (in_array($name_in_archive, $required_files) || in_array('/' . $name_in_archive, $required_files))
							{
								unset($required_files[array_search($name_in_archive, $required_files)]);
							}
						}
					}

					if ($archive_root_content[0]['folder'] && empty($required_files))
					{
						$lang_id = $archive_root_content[0]['filename'];
						if (!LangsManager::get_lang_existed($lang_id))
						{
							if ($upload->get_extension() == 'gz')
								PclTarExtract($upload->get_filename(), $folder_phpboost_langs);
							else
								$zip->extract(PCLZIP_OPT_PATH, $folder_phpboost_langs, PCLZIP_OPT_SET_CHMOD, 0755);

							$this->install_lang($lang_id, array('r-1' => 1, 'r0' => 1, 'r1' => 1));
						}
						else
						{
							$this->view->put('MSG', MessageHelper::display(LangLoader::get_message('element.already_exists', 'status-messages-common'), MessageHelper::NOTICE));
						}
					}
					else
					{
						$this->view->put('MSG', MessageHelper::display(LangLoader::get_message('error.invalid_archive_content', 'status-messages-common'), MessageHelper::NOTICE));
					}

					$uploaded_file = new File($archive);
					$uploaded_file->delete();
				}
				else
				{
					$this->view->put('MSG', MessageHelper::display(LangLoader::get_message('upload.invalid_format', 'status-messages-common'), MessageHelper::NOTICE));
				}
			}
			else
			{
				$this->view->put('MSG', MessageHelper::display(LangLoader::get_message('upload.error', 'status-messages-common'), MessageHelper::NOTICE));
			}
		}
	}
}
?>
