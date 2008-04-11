		# IF C_DISPLAY_ARTICLE #
		<div class="module_position">					
			<div class="module_top_l"></div>		
			<div class="module_top_r"></div>
			<div class="module_top">
				<div style="float:left">
					<strong>{NAME}</strong>
				</div>
				<div style="float:right">
					{COM} {EDIT} {DEL}
				</div>
			</div>
			<div class="module_contents">
				# IF PAGINATION_ARTICLES #
				<div style="float:right;margin-right:35px;width:250px;">
					<form action="" method="post">
						<p class="row2 text_strong" style="padding:2px;text-indent:4px;">{L_SUMMARY}:</p>
						<p class="row1" style="padding:2px;padding-bottom:15px">
							<select name="page_list" style="display:block;width:100%;margin:auto;font-size:12px;" onchange="document.location = {U_ONCHANGE_ARTICLE}">
								{PAGES_LIST}
							</select>
							<input type="submit" name="valid" id="articles_page_list" value="{L_SUBMIT}" class="submit" />
							<script type="text/javascript">
							<!--				
							document.getElementById('articles_page_list').style.display = 'none';
							-->
							</script>
						</p>
					</form>
				</div>
				<div class="spacer"></div>
				# ENDIF #
				
				# IF PAGE_NAME #
				<p class="text_strong" style="text-indent:35px;">{PAGE_NAME}</p>
				# ENDIF #
				
				{CONTENTS}
				
				<div class="spacer" style="margin-top:25px;">&nbsp;</div>
				# IF PAGINATION_ARTICLES #
				<div style="float:left;width:33%;text-align:right">&nbsp;{PAGE_PREVIOUS_ARTICLES}</div>
				<div style="float:left;width:33%" class="text_center">{PAGINATION_ARTICLES}</div>
				<div style="float:left;width:33%;">{PAGE_NEXT_ARTICLES}&nbsp;</div>
				# ENDIF #
				<div class="spacer"></div>
			</div>
			<div class="module_bottom_l"></div>		
			<div class="module_bottom_r"></div>
			<div class="module_bottom">
				<span style="float:left" class="text_small">
					# INCLUDE handle_note #
				</span>
				<span style="float:right" class="text_small">
					{L_WRITTEN}: <a class="small_link" href="../member/member{U_MEMBER_ID}">{PSEUDO}</a>, {L_ON}: {DATE}
				</span>
				<div class="spacer"></div>
			</div>
		</div>
		<br /><br />
		# INCLUDE handle_com #
		
		# ENDIF #
		