<?php $this->append('sidebar');?>
	<?php $current_user = AuthComponent::user(); ?>
	<?php if (empty($current_user)) {?>
		<div class="short-login">
			<h4>Login</h4>
			
			<?php echo $this->Form->create('User', array('url' => array('controller' => 'users', 'action' => 'login', 'admin' => false)))?>
				<?php echo $this->Form->input('email')?>
				<?php echo $this->Form->input('password');?>
				<?php echo $this->Form->submit(__('Login'))?>
			<?php echo $this->Form->end();?>
			<?php echo $this->Html->link(__('Create an account'), array('controller' => 'users', 'action' => 'register'), array('class' => 'register'))?>
			<?php echo $this->Html->link(__('Forgot Password?'), array('controller' => 'users', 'action' => 'lost_password'), array('class' => 'register'))?>
		</div>
	<?php }  else {?>
	<?php } ?>
<?php $this->end();?>
<h3><?php echo __('Public Projects')?></h3>
<?php if (!empty($projects)) {?>
	
	<ul class="nav nav-tabs">
	  <li class="active"><a href="#tiles" data-toggle="tab">Tile View</a></li>
	  <li><a href="#table" data-toggle="tab">Table View</a></li>
	</ul>
	<div class="tab-content">
		<div class="tab-pane active projects public chips" id="tiles">
			<div class="row">
					<?php foreach ($projects as $i => $project) { ?>
						<div class="col-sm-3 col-xs-5 col-md-3">
							<a class="thumbnail" href="<?php echo Router::url(array('action'=> 'view', $project['Project']['id']));?>">
								<?php if ((!empty($project['Operation'])) && (file_exists(PDF_PATH.DS.$project['Operation'][0]['id'].'.png'))) {?>
									<?php echo $this->Html->image('/files/'.$project['Operation'][0]['id'].'.png')?>
								<?php } else { ?>
									<?php echo $this->Html->image('no-thumb.png')?>
								<?php }?>
								<p class="caption">
									<p class="title"><?php echo $project['Project']['project_name'];?></p>
									<p class="details"><?php echo __('Created %s', $this->Time->timeAgoInWords($project['Project']['created']));?>
									</p>
								</p>
							</a>
						</div>
						
					<?php }?>
			</div>
		</div>
	
		<div class="tab-pane projects index" id="table">
		
			<table class="table table-striped">
			<tr>
					<th colspan="2"><?php echo $this->Paginator->sort('project_name'); ?></th>
					<th><?php echo __('Created')?>
					<th class="actions"><?php echo __('Actions'); ?></th>
			</tr>
			<?php foreach ($projects as $project): ?>
			<tr>
				<td><?php if ((!empty($project['Operation'])) && (file_exists(PDF_PATH.DS.$project['Operation'][0]['id'].'.png'))) {?>
									<?php echo $this->Html->image('/files/'.$project['Operation'][0]['id'].'.png')?>
								<?php } else { ?>
									<?php echo $this->Html->image('no-thumb.png')?>
								<?php }?>
				<td><?php echo $this->Html->link($project['Project']['project_name'], array('action' => 'view', $project['Project']['id'])); ?>&nbsp;</td>
				<td><?php echo __('Created %s', $this->Time->timeAgoInWords($project['Project']['created']));?></td>
				<td class="actions">
					<?php echo $this->Html->link(__('View'), array('action' => 'view', $project['Project']['id']));?>
					<?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $project['Project']['id']), null, __('Are you sure you want to delete # %s?', $project['Project']['id'])); ?>
				</td>
			</tr>
		<?php endforeach; ?>
			</table>
		</div>
	</div>
	<div class="pagination">
	<?php
		echo $this->Paginator->prev('< ' . __('previous'), array(), null, array('class' => 'prev disabled'));
		echo $this->Paginator->numbers(array('separator' => ''));
		echo $this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next disabled'));
	?>
	</div>
<?php } else { ?>
	<div class="no-projects">
		<p>No projects found.</p>
	</div>
<?php } ?>