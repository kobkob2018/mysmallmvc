<div id="page_wrap" class="page-wrap">
	<div class="header">
		<div id="logo_wrap" class="">
			<img src="style/image/logo.png" alt="מערכת הלידים של איי-אל-ביז" />
		</div>
		<div id="filter_door" class="header-item">
			<a href="javascript://" onClick="open_close_filter(this)" class="closed">
				<img src="style/image/Search-icon.png" alt="מערכת הלידים של איי-אל-ביז" style="width:30px; border: 5px solid transparent;"/>
			</a>
		</div>		
		<?php if($this->user): ?>
			<div id="header_links" class="header-right-menu">
				<a id="header_lead_list_link" class="header-link header-item" href='leads/all/'><span class="bg"></span><span class="header-link-title">לידים</span></a>
			</div>
			<div id="left_menu" class="header-left-menu">
				<div id="user_menu" class="header-item">
					<div id="header_usermenu_wrap">
					  <a id="header_usermenu_door" href="javascript://" rel="closed" onClick="open_close_usermenu()">
						<span class="bg"></span><span class="user-name"><?php utpr($this->user['full_name']); ?></span>
					  </a>


					  <div id="usermenu_wrap" style="display:none;">
						<div id="usermenu_content">
							<h4><?php utpr($this->user['full_name']); ?></h4>
							<h5><?php utpr($this->user['name']); ?></h5>
							<ul>
								<li><a href="credits/buyLeads/">רכישת לידים</a></li>
								<li><a href="notifications/payment_list/">תשלומים אחרונים</a></li>
								<li><a href="user/details/">עדכון פרטים</a></li>
								<?php if($this->user['have_net_banners']): ?>
									<li><a href="reports/banners/">הבאנרים שלי</a></li>
								<?php endif; ?>
								<li><a href="user/logout/">יציאה</a></li>
							</ul>
						</div>
					  </div>
					</div>						
				</div>
				<div id="notifications_menu" class="header-item">
					<div id="header_notifications_wrap">
						  <a id="header_notifications_door" href="javascript://" data-touched='0' rel="closed" onClick="open_close_notifications()">
							
						  </a>

						  <div id="notifications_wrap" style="display:none;">
							<div id="notifications_content">
								<div id="notifications_recived_content">
								
								</div>
							</div>
						  </div>
						  <div id="notifications_bugger_wrap" style="display:none;">
							<div id="notifications_bugger_content">
								קיימות הודעות מערכת.
								<br/>
								 <a href="javascript://" onClick="hide_notifications_bugger()">
									לחץ כאן לצפייה 
								</a>
							 
							</div>
						  </div>						  
					</div>						
				</div>					
			</div>
			<script type="text/javascript">
				check_notifications_interval();
			</script>
		<?php else: ?>
		  <a href = 'userLogin/login/'>כניסה למערכת</a>
		  <a href = 'userLogin/register/'>הרשמה</a>
		<?php endif; ?>
		
		<?php include('views/messeges/all.php'); ?>
		<div class="clear"></div>	
	</div>
	<div class="header-space-keeper"></div>
	<div id="content_wrap">
		<?php $this->print_action_output(); ?>
	</div>
	<div id="footer" class="footer">
	<? /*
	 © כל הזכויות שומורות <a href="http://www.ilbiz.co.il" class="copyrightBottom" title="פורטל עסקים ישראל">פורטל עסקים ישראל</a>&nbsp;&nbsp;&nbsp; <a href="http://www.il-biz.com" class="copyrightBottom" target="_blank" title="IL-BIZ קידום עסקים באינטרנט">IL-BIZ קידום עסקים באינטרנט</a>&nbsp;&nbsp;&nbsp; <a href="http://kidum.ilbiz.co.il/" class="copyrightBottom" target="_blank" title="קידום באינטרנט">קידום באינטרנט</a> - אילן שוורץ&nbsp;&nbsp;&nbsp; <a href="http://www.il-biz.co.il/" class="copyrightBottom" target="_blank" title="בניית אתרים">בניית אתרים</a>
	*/ ?>
	</div>
</div>