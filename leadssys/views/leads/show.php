<?php $data_sent=$lead->users_send_summary;  $data=$lead->estimate_form_data;  ?>
<div class="beck-link">
	<a href='/<?php echo $this->base_url_dir; ?>/leads/all/'>חזרה לרשימת הלידים</a>
</div>
<h3>צפייה בליד <?php utpr($data['name']); ?> <?php echo $data['phone']; ?> [<?php echo hebdt($data['insert_date']); ?>]</h3>
<hr/>
<div class="lead-details">
	
	<div id="vertical-table">
		<table class="col-md-12 table-bordered table-striped table-condensed cf">
			<tbody>
				<tr>
					<th>שם</th>
					<td data-title="שליחה ללקוחות"><?php utpr($data['name']); ?></td>
				</tr>
				<tr>
					<th>טלפון</th>
					<td data-title="שליחה ללקוחות"><?php echo $data['phone']; ?></td>
				</tr>
				<tr>
					<th>זמן שליחה</th>
					<td data-title="שליחה ללקוחות" style="direction:ltr;"><?php echo hebdt($data['insert_date']); ?></td>
				</tr>				
				<tr>
					<th>שליחה ללקוחות</th>
					<td data-title="שליחה ללקוחות"><?php echo $data_sent['total_send']; ?></td>
				</tr>
				<tr>
					<th>פתוח</th>
					<td data-title="פתוח"><?php echo $data_sent['payByPassword_1']; ?></td>
				</tr>
				<tr>
					<th class="numeric">סגור</th>
					<td data-title="סגור" class="numeric"><?php echo $data_sent['payByPassword_0']; ?></td>
				</tr>
				<tr>
					<th class="numeric">חוייבו</th>
					<td data-title="חוייבו" class="numeric"><?php echo $data_sent['billed']; ?></td>
				</tr>
				<tr>
					<th class="numeric">כפולים</th>
					<td data-title="כפולים %" class="numeric"><?php echo $data_sent['doubled']; ?></td>
				</tr>
				<tr>
					<th class="numeric">הוחזרו</th>
					<td data-title="הוחזרו" class="numeric"><?php echo $data_sent['refunded']; ?></td>
				</tr>
				<tr>
					<th class="numeric">סה"כ לתשלום</th>
					<td data-title="סה&quot;כ לתשלום" class="numeric"><?php echo $data_sent['total_pay']; ?></td>
				</tr>				
			
			</tbody>
		</table>
	</div>
</div>