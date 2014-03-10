<?php if (!isset($claim)) $claim = false;?>
<?php if (!isset($show_user)) $show_user = false;?>
<?php if (!isset($action)) $action = 'view';?>


<div class="projects chips">
	<div class="row">
		<?php foreach ($projects as $i => $project) { ?>
			<div class="chip">
				<div class="thumb">
					<?php if ((!empty($project['Operation'])) && (file_exists(PDF_PATH.DS.$project['Operation'][0]['id'].'.png'))) {?>
						<?php echo $this->Html->image('/files/'.$project['Operation'][0]['id'].'.png')?>
					<?php } else { ?>
						<?php echo $this->Html->image('no-thumb.png')?>
					<?php }?>
				</div>
				<p class="caption">
					<?php 
						if (!empty($this->request->data['Project']['project_name'])) {
							$project_name = $this->Text->highlight($project['Project']['project_name'], $this->request->data['Project']['project_name']);
						} else if (empty($project['Project']['project_name'])) {
							$project_name = __('[No Title]');
						} else {
							$project_name = $project['Project']['project_name'];
						}
					?>
					<?php echo $this->Html->link($project_name, array('action' => $action, $project['Project']['id']), array('escape' => false))?>
					<span class="details"><?php echo __('Created %s', $this->Time->timeAgoInWords($project['Project']['created']));?>
					</span>
				</p>
				<?php if ($claim) {?>
					<?php echo $this->Form->postButton(__('Claim'), array('action' => 'make_claim', $project['Project']['id']), array('class' => 'claim'), __('Are you sure you want to claim this project as your own?  It\'s not nice to claim other users projects...'));?>
				<?php } ?>
				
			</div>
		<?php }?>
	</div>
</div>		
	
<div class="pagination">
<?php
	echo $this->Paginator->prev('< ' . __('previous'), array(), null, array('class' => 'prev disabled'));
	echo $this->Paginator->numbers(array('separator' => ''));
	echo $this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next disabled'));
?>
</div>