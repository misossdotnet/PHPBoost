<?php
/**
 * @copyright   &copy; 2005-2021 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Benoit SAUTEL <ben.popeye@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2021 05 23
 * @since       PHPBoost 3.0 - 2009 12 08
 * @contributor Sebastien LARTIGUE <babsolune@phpboost.com>
*/

class GenerateXMLSitemapController extends AdminController
{
	public function execute(HTTPRequestCustom $request)
	{
		$view = new FileTemplate('sitemap/GenerateXMLSitemapController.tpl');
		$lang = LangLoader::get('common', 'sitemap');
		$view->add_lang($lang);

		try
		{
			SitemapXMLFileService::try_to_generate();
		}
		catch(IOException $ex)
		{
			$view->put_all(
				array('C_GOT_ERROR' => true)
			);
		}

		$view->put_all(array(
			'U_GENERATE' => SitemapUrlBuilder::get_xml_file_generation()->rel()
		));

		$response = new AdminSitemapResponse($view);
		$response->get_graphical_environment()->set_page_title($lang['sitemap.generate.xml'], $lang['sitemap.module.title']);
		return $response;
	}
}
?>
