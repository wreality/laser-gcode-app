<?php $this->append('sidebar');?>
	<?php echo $this->element('Project/project_nav', array('current' => 'claim'))?>
<?php $this->end();?>
<h3><?php echo __('Unclaimed Projects')?></h3>
<?php if (empty($projects)):  ?>
	<div class="no-projects">
		<p>No unclaimed projects found.</p>
	</div>
<?php else: ?>	
	<?php echo $this->element('Project/projects_index', array('claim' => true));?>
<?php endif; // empty(projects)?>