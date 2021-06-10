<?php
/**
 * @copyright   &copy; 2005-2020 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Regis VIARRE <crowkait@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2021 06 10
 * @since       PHPBoost 1.3 - 2005 11 20
 * @contributor Julien BRISWALTER <j1.seth@phpboost.com>
 * @contributor Sebastien LARTIGUE <babsolune@phpboost.com>
 * @contributor Arnaud GENET <elenwii@phpboost.com>
*/

####################################################
#                     English                      #
####################################################

$lang['admin.administration']     = 'Administration';
$lang['admin.kernel']             = 'Kernel';
$lang['admin.warning']            = 'Warning';
$lang['admin.priority']           = 'Priority';
$lang['admin.priority.very.high'] = 'Immediate';
$lang['admin.priority.high']      = 'Urgent';
$lang['admin.priority.medium']    = 'Medium';
$lang['admin.priority.low']       = 'Low';
$lang['admin.priority.very.low']  = 'Very low';

// Advice
$lang['admin.advice']                              = 'Advice';
$lang['admin.advice.modules.management']           = '<a href="' . AdminModulesUrlBuilder::list_installed_modules()->rel() . '">Disable or uninstall modules</a> you don\'t need to free ressources on the website.';
$lang['admin.advice.check_modules.authorizations'] = 'Check the authorizations of all your modules and menus before opening your website to avoit guest or unauthorized users accessing protected areas.';
$lang['admin.advice.disable.debug.mode']           = '<a href="' . AdminConfigUrlBuilder::advanced_config()->rel() . '">Disable debug mode</a> to hide errors to users (the errors are loggued on the <a href="' . AdminErrorsUrlBuilder::logged_errors()->rel() . '">Loggued errors</a> page).';
$lang['admin.advice.disable.maintenance']          = '<a href="' . AdminMaintainUrlBuilder::maintain()->rel() . '">Disable maintenance</a> to allow the users to view your website.';
$lang['admin.advice.enable.url.rewriting']         = '<a href="' . AdminConfigUrlBuilder::advanced_config()->rel() . '">Enable URL rewriting</a> to have more readable urls (usefull for SEO).';
$lang['admin.advice.enable.output.gz']             = '<a href="' . AdminConfigUrlBuilder::advanced_config()->rel() . '">Enable Output pages compression</a> to gain performance.';
$lang['admin.advice.enable.apcu.cache']            = '<a href="' . AdminCacheUrlBuilder::configuration()->rel() . '">Enable APCu cache</a> to allow the RAM cache to be loaded on the server and not on the hard-drive and gain performance.';
$lang['admin.advice.save.database']                = 'Save your database frequently.';
$lang['admin.advice.optimize.database.tables']     = '<a href="' . AdminConfigUrlBuilder::advanced_config()->rel() . '">Enable auto tables optimization</a> or optimize occasionally your tables in the module <strong>Database</strong> (if it is installed) or in your database management tool to recover the wasted base space.';
$lang['admin.advice.password.security']            = 'Increase strength and length of passwords in <a href="' . AdminMembersUrlBuilder::configuration()->rel() . '">members configuration</a> to strengthen security.';
$lang['admin.advice.upgrade.php.version']          = '
    PHP version ' . ServerConfiguration::get_phpversion() . ' of your server is deprecated, there are no more security updates and it potentially contains vulnerabilities allowing a malicious person to attack your website.
    <br />Update your PHP version to ' . ServerConfiguration::RECOMMENDED_PHP_VERSION . ' minimum if your host allows it, the new versions are faster and more secure.
';

// Alerts
$lang['admin.alerts']               = 'Alerts';
$lang['admin.alerts.list']          = 'Alerts list';
$lang['admin.no.unread.alert']      = 'No unprocessed alerts';
$lang['admin.unread.alerts']        = 'There are some unprocessed alerts. You should go there to process them.';
$lang['admin.no.alert']             = 'No existing alert';
$lang['admin.display.all.alerts']   = 'See all alerts';
$lang['admin.fix.alert']            = 'Consider the alert as fixed';
$lang['admin.unfix.alert']          = 'Consider the alert as not fixed';
$lang['admin.warning.delete.alert'] = 'Are you sure you want to delete this alert?';
$lang['admin.unread.alerts']        = 'There are some unprocessed alerts. You should go there to process them.';
$lang['admin.see.all.alerts']       = 'See all alerts';

// Cache
$lang['admin.empty.cache'] = 'Empty cache';

// Configuration
    // General
$lang['admin.general.configuration'] = 'General configuration';

// Content
$lang['admin.forbidden.module']                  = 'Forbidden modules';
$lang['admin.comments.forbidden.module.clue']    = 'Select modules in which you do not want to enable comments';
$lang['admin.notation.forbidden.module.clue']    = 'Select modules in which you do not want to enable notation';
$lang['admin.new.content.forbidden.module.clue'] = 'Select modules in which you do not want to enable new-content tag';

// Errors lists
$lang['admin.errors'] = 'Errors';
$lang['admin.clear.list'] = 'Clear list';
$lang['admin.warning.clear'] = 'Erase all errors?';
    // Logged
$lang['admin.logged.errors'] = 'Logged errors';
$lang['admin.logged.errors.list'] = 'Logged errors list';
    // 404
$lang['admin.404.errors'] = '404 errors';
$lang['admin.404.errors.list'] = '404 errors list';
$lang['admin.404.requested.url'] = 'Requested url';
$lang['admin.404.from.url'] = 'Source url';

// Index
$lang['admin.quick.access']        = 'Quick Access';
$lang['admin.welcome.title']       = 'Welcome to the administration panel of your site';
$lang['admin.welcome.descritpion'] = 'The administration lets you manage content and configuration of your site<br />The home page lists the most common actions<br />Take time to read the tips to optimize the security of your site';
$lang['admin.website.management']  = 'Manage the website';
$lang['admin.customize.website']   = 'Customize the website';
$lang['admin.add.content']         = 'Add content';
$lang['admin.customize.theme']     = 'Customize a theme';
$lang['admin.add.article']         = 'Add article';
$lang['admin.add.news']            = 'Add news';
$lang['admin.last.comments']       = 'Last comments';
$lang['admin.writing.pad']         = 'Writing pad';
$lang['admin.writing.pad.clue']    = 'This form is provided to enter your personal notes.';

// Maintenance
$lang['admin.maintenance']         = 'Maintenance';
$lang['admin.maintenance.title']   = 'Website in maintenance';
$lang['admin.maintenance.content'] = 'The website is under maintenance. Thank you for your patience.';
$lang['admin.maintenance.delay']   = 'Time remaining:';
$lang['admin.disable.maintenance'] = 'Disable maintenance';
    // Form
$lang['admin.maintenance.type']                   = 'Put the website in maintenance';
$lang['admin.maintenance.type.during']            = 'Less than a day';
$lang['admin.maintenance.type.until']             = 'Many days';
$lang['admin.maintenance.type.unlimited']         = 'For an unspecified duration';
$lang['admin.maintenance.display.duration']       = 'Display maintenance duration';
$lang['admin.maintenance.admin.display.duration'] = 'Display maintenance duration to the administrator';
$lang['admin.maintenance.text']                   = 'Text to display when the website is under maintenance';
$lang['admin.maintenance.authorization']          = 'Permission to access to the website during maintenance';

// Updates
$lang['admin.updates']                = 'Updates';
$lang['admin.available.updates']      = 'Available updates';
$lang['admin.available.updates.clue'] = 'Updates are available<br />Please, update quickly';
$lang['admin.available.version']      = 'The %1$s %2$s is available in its %3$s version';
$lang['admin.kernel.update']          = 'PHPBoost\'s kernel %s is available';
$lang['admin.download.app']           = 'Download';
$lang['admin.download.pack']          = 'Complete pack';
$lang['admin.update.pack']            = 'Update pack';
$lang['admin.new.features']           = 'New features';
$lang['admin.improvements']           = 'Improvements';
$lang['admin.security.improvements']  = 'Security improvements';
$lang['admin.fixed.bugs']             = 'Fixed bugs';
$lang['admin.details']                = 'Details';
$lang['admin.more.details']           = 'More details';
$lang['admin.download.full.pack']     = 'Download the full pack';
$lang['admin.download.update.pack']   = 'Download the update pack';
$lang['admin.no.available.update']    = 'No update is available for the moment.';
$lang['admin.updates.check']          = 'Check for updates now!';
$lang['admin.php.version']            = '
    Can\'t check for updates.<br />
    Please upgrade to PHP version %s or above.<br />
    If you can\'t use PHP5, check for updates on our <a href="https://www.phpboost.com">official website</a>.
';

?>
