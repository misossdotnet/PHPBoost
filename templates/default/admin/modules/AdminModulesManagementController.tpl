# INCLUDE MSG #
# START errors #
	# INCLUDE errors.MSG #
# END errors #

<div class="text-helper">{@H|modules.warning_before_delete}</div>
<form action="{REWRITED_SCRIPT}" method="post">
	<section id="installed-modules-container">
		<header class="legend">{@modules.installed_modules}</header>
		<div class="cell-flex cell-columns-3">
			# START modules_installed #
				<article class="cell addon# IF NOT modules_installed.C_IS_ACTIVATED # disabled-addon# ENDIF ## IF NOT modules_installed.C_COMPATIBLE # not-compatible# ENDIF #">
					<header class="cell-header">
						# IF C_MORE_THAN_ONE_MODULE_INSTALLED #
							# IF modules_installed.C_COMPATIBLE #
								<div class="mini-checkbox">
									<label class="checkbox" for="multiple-checkbox-{modules_installed.MODULE_NUMBER}">
										<input type="checkbox" id="multiple-checkbox-{modules_installed.MODULE_NUMBER}" name="delete-checkbox-{modules_installed.MODULE_NUMBER}"/>
										<span>&nbsp;</span>
									</label>
								</div>
							# ENDIF #
						# ENDIF #
						<div class="cell-name">{modules_installed.NAME}</div>
						<div class="addon-menu-container addon-with-menu">
							<a href="#" id="addon-menu-title-{modules_installed.MODULE_NUMBER}" class="addon-menu-title">
								# IF modules_installed.C_COMPATIBLE #
									# IF modules_installed.C_IS_ACTIVATED #
										${LangLoader::get_message('actions', 'admin-common')}
									# ELSE #
										${LangLoader::get_message('disabled', 'common')}
									# ENDIF #
								# ELSE #
									${LangLoader::get_message('not_compatible', 'admin-common')}
								# ENDIF #
							</a>
							<ul class="addon-menu-content">
								# IF modules_installed.C_COMPATIBLE #
									# IF modules_installed.C_IS_ACTIVATED #
										<li class="addon-menu-item"><button type="submit" class="submit" name="disable-{modules_installed.ID}" value="true">${LangLoader::get_message('disable', 'common')}</button></li>
									# ELSE #
										<li class="addon-menu-item"><button type="submit" class="submit" name="enable-{modules_installed.ID}" value="true">${LangLoader::get_message('enable', 'common')}</button></li>
									# ENDIF #
								# ENDIF #
								<li class="addon-menu-item"><button type="submit" class="submit alt" name="delete-{modules_installed.ID}" value="true">${LangLoader::get_message('uninstall', 'admin-common')}</button></li>
							</ul>
						</div>
					</header>
					<div class="cell-list">
						<ul>
							<li class="li-stretch">
								<img class="valign-middle" src="{PATH_TO_ROOT}/{modules_installed.ICON}/{modules_installed.ICON}.png" alt="{modules_installed.NAME}" />
							</li>
							<li class="li-stretch">
								<span class="text-strong">${LangLoader::get_message('version', 'admin')} :</span>
								{modules_installed.VERSION}
							</li>
							<li class="li-stretch">
								<span class="text-strong">${LangLoader::get_message('author', 'admin-common')} :</span>
								<span># IF modules_installed.C_AUTHOR_EMAIL #<a href="mailto:{modules_installed.AUTHOR_EMAIL}">{modules_installed.AUTHOR}</a># ELSE #{modules_installed.AUTHOR}# ENDIF # # IF modules_installed.C_AUTHOR_WEBSITE #<a href="{modules_installed.AUTHOR_WEBSITE}" class="basic-button smaller">Web</a># ENDIF #</span>
							</li>
							<li class="li-stretch">
								<span class="text-strong">${LangLoader::get_message('form.date.creation', 'common')} :</span>
								{modules_installed.CREATION_DATE}
							</li>
							<li class="li-stretch">
								<span class="text-strong">${LangLoader::get_message('last_update', 'admin')} :</span>
								{modules_installed.LAST_UPDATE}
							</li>
							<li>
								<span class="text-strong">${LangLoader::get_message('description', 'main')} :</span>
								{modules_installed.DESCRIPTION}
							</li>
							<li class="li-stretch">
								<span class="text-strong">${LangLoader::get_message('compatibility', 'admin-common')} :</span>
								<span# IF NOT modules_installed.C_COMPATIBLE # class="not-compatible"# ENDIF #>PHPBoost {modules_installed.COMPATIBILITY}</span>
							</li>
							<li class="li-stretch">
								<span class="text-strong">{@modules.php.version} :</span>
								{modules_installed.PHP_VERSION}
							</li>
						</ul>
					</div>
					# IF modules_installed.C_DOCUMENTATION #
						<div class="cell-footer center">
							<a class="basic-button smaller" href="{modules_installed.L_DOCUMENTATION}">{@module.documentation}</a>
						</div>
					# ENDIF #
				</article>
				<script>
					jQuery('#addon-menu-title-{modules_installed.MODULE_NUMBER}').opensubmenu({
						osmTarget: '.addon-menu-container'
					});
				</script>
			# END modules_installed #
		</div>
		<footer>
			<input type="hidden" name="token" value="{TOKEN}">
		</footer>
	</section>

	# IF C_MORE_THAN_ONE_MODULE_INSTALLED #
	<div class="addon-menu-container multiple-select-menu-container">
		<a href="#" class="multiple-select-menu addon-menu-title">${LangLoader::get_message('multiple.select', 'admin-common')}</a>
		<ul class="addon-menu-content">
			<li class="addon-menu-checkbox mini-checkbox select-all-checkbox">
				<label class="checkbox" for="delete-all-checkbox">
					<input type="checkbox" class="check-all" id="delete-all-checkbox" name="delete-all-checkbox" onclick="multiple_checkbox_check(this.checked, {MODULES_NUMBER});" aria-label="{@modules.select_all_modules}" />
					<span>&nbsp;</span>
				</label>
			</li>
			<li class="addon-menu-item"><button type="submit" name="activate-selected-modules" value="true" class="submit" id="activate-all-button">${LangLoader::get_message('multiple.activate_selection', 'admin-common')}</button></li>
			<li class="addon-menu-item"><button type="submit" name="deactivate-selected-modules" value="true" class="submit" id="deactivate-all-button">${LangLoader::get_message('multiple.deactivate_selection', 'admin-common')}</button></li>
			<li class="addon-menu-item"><button type="submit" name="delete-selected-modules" value="true" class="submit alt" id="delete-all-button">${LangLoader::get_message('multiple.uninstall_selection', 'admin-common')}</button></li>
		</ul>
	</div>
	# ENDIF #
</form>

<script>
	jQuery('.addon-menu-title').opensubmenu({
		osmTarget: '.addon-menu-title',
		osmCloseExcept : '.addon-menu-checkbox, .addon-menu-checkbox *'
	});

	jQuery('.addon-auth').opensubmenu({
		osmTarget: '.addon-auth-container',
		osmCloseExcept: '.addon-auth-content *',
		osmCloseButton: '.addon-auth-close i',
	});
</script>
