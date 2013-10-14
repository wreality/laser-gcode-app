<div class="projects form">
	<h2>Operations</h2>
		<?php if (empty($project['Operation'])) { ?>
			<p>No operations exist yet.</p>
		<?php } else {?>
			<?php foreach ($project['Operation'] as $oi => $operation) {?>
				<h2>Operation <?php echo $oi; ?>Paths</h2>
				<?php if (empty($operation['Path'])) {?>
					<p>No paths exist yet.</p>
				<?php } else { ?>
					<?php foreach($operation['Path'] as $path) {?>
						<?php echo $path['file_name']?>
					<?php } ?>
				<?php } ?>
			<?php } ?>
		<?php } ?>
		
</div>