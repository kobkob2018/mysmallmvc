<div id="tags_edit_wrap">
	<h3>סטטיסטיקת באנרים</h3>
	<table colspan='3' border="1" style="border-collapse:collapse">
		<tr>
			<th>שם הבאנר</th>
			<th>צפיות</th>
			<th>הקלקות</th>
			<th>הקלקות באחוזים</th>
		</tr>
		<?php foreach($user_banners_data as $user_banner_data): ?>
			<tr>
				<td><?php utpr($user_banner_data['banner_name']); ?></td>
				<td><?php echo $user_banner_data['views']; ?></td>
				<td><?php echo $user_banner_data['clicks']; ?></td>
				<td><?php echo sprintf('%0.2f', $user_banner_data['clicks']*100/$user_banner_data['views']); ?></td>
			</tr>
		<?php endforeach; ?>
	</table>
</div>