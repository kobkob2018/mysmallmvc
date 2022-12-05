<h3>שליחת ליד חדש</h3>
<hr/>
<div class="well estimate-form" id="estimate_form_wrap">
	<script src="https://ilbiz.co.il/affiliate/style/js/estimate_form.js?v=2.0"></script>
	<div class="estimate-form-content">
	
		<script type="text/javascript">						
			function get_estimateSiteForm(cat , subCat , cat_spec , aff_id){
				var ef_url = "https://ilbiz.co.il/ClientSite/ajax.php?main=estimateSiteHeight&version=version_1"
				+"&aff_id=" + aff_id 
				+"&cat=" + cat 
				+ "&subCat=" + subCat 
				+ "&cat_spec=" + cat_spec;
				console.log(ef_url);

				jQuery.ajax({
					url: ef_url,
					type: 'GET',
					success: function (transport) {
						document.getElementById("estimateSiteHeightDiv").innerHTML = transport;
						etsimate_form_handler();
					},
					error: function () {
						console.log("err:c_c_f.p(1250)");
					}
				});
			}
		</script>
		
		<div id='estimateSiteHeightDiv'>

		</div>
		<script type='text/javascript'>
			//window.onload = function() {
				get_estimateSiteForm("0" , "0" , "0" , "<?php echo $this->user['id']; ?>" );
			//};
		</script>
	</div>
</div>