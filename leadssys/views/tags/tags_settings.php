<div id="tags_edit_wrap">
	<h3>תיוגים</h3>
	<table colspan='3' border="1" style="border-collapse:collapse">
		<tr>
			<th>#</th>
			<th>תיוג</th>
			<th></th>
		</tr>
		<tr>
			<td>הוספת תיוג</td>
			<td>
				<form action="" method="POST">
					<input type="hidden" name="add_tag" value="1" />
					<input type="text" name="tag_data[tag_name]"  />
				
			</td>
			<td>
					<input type="submit" value="שלח" />
				</form>
			</td>
		</tr>
		<?php foreach($tag_list as $tag_id=>$tag_name): if($tag_id!='0'): ?>
			<tr>
				<td><?php echo $tag_id; ?></td>
				<td><?php echo $tag_name; ?></td>
				<td>
					<form action="" method="POST">
						<input type="hidden" name="delete_tag" value="1" />
						<input type="hidden" name="tag_data[tag_id]" value="<?php echo $tag_id; ?>" />
						<input type="submit" value="מחק" />
					</form>				
				</td>
			</tr>
		<?php endif; endforeach; ?>
	</table>
</div>