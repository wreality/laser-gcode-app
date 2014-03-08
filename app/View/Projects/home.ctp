<?php $this->append('sidebar');?>
	<?php echo $this->element('Project/project_nav', array('current' => 'own'))?>
<?php $this->end();?>
<h3><?php echo __('My Projects')?></h3>
<?php if (empty($projects)):  ?>
	<div class="no-projects">
		<p>You currently have no projects.</p>
	</div>
<?php else: ?>	
	<?php echo $this->element('Project/projects_index', array('action' => 'edit'));?>
<?php endif; // empty(projects)?>