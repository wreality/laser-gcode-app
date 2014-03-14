<?php $this->append('sidebar');?>
	<?php echo $this->element('Project/project_nav', array('current' => 'public'))?>
<?php $this->end();?>
<h3><?php echo __('Public Projects')?></h3>
<?php if (empty($projects)):  ?>
	<div class="no-projects">
		<p>No public projects found.</p>
	</div>
<?php else: ?>	
	<?php echo $this->element('Project/projects_index', array('show_user' => true))?>
<?php endif; // empty(projects)?>