<h3><?php echo ucwords($user['User']['username'])?>'s Public Projects</h3>
<?php echo $this->element('Project/projects_index', array('projects' => $projects));;?>


<?php $this->start('sidebar');?>
<table class="table table-striped">
	<tr>
		<th><?php echo $this->Html->gravatar($user['User']['email'], array('size' => 80))?></th>
		<th><?php echo $user['User']['username'];?></th>
	</tr>
	<tr>
		<th><?php echo __("Member Since");?></th>
		<td><?php echo $this->Time->format('M, Y', $user['User']['created']);?>	</td>
	</tr>
	<tr>
		<th colspan="2"><?php echo __n('%s public project', '%s public projects', $user['User']['public_count'], array($user['User']['public_count']));?></th>
	</tr>
	
</table>

<?php $this->end();?>