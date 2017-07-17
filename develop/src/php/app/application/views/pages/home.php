<table>
	<tr>
		<th>ID</th>
		<th>name</th>
	</tr>

<?php foreach ($sensors as $sensor_item): ?>
	<tr>
		<td><?php echo $sensor_item['id'] ?></td>
		<td><?php echo $sensor_item['name'] ?></td>
	</tr>
<?php endforeach; ?>

</table>
