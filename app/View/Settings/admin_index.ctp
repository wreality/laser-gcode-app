<?php echo $this->set('Application Settings')?>
<table class="table table-condensed table-bordered settings">
	<tr>
			<th><?php echo __('Setting');?></th>
			<th><?php echo __('Current Value')?></th>
			
	</tr>
	<?php $group = false?>
	<?php foreach ($settings as $setting) { ?>
		<?php 
			
			if (stripos($setting['Setting']['key'], 'App.') === 0) {
				$key = substr($setting['Setting']['key'], 4);
				
			} else {
				$key = $setting['Setting']['key'];
			}
			if (preg_match('/^[a-zA-Z]+/', $key, $matches)) {
				if ($matches[0] != $group) {
					$class = true;
					$group = $matches[0];
				} else {
					$class = false;
				}
			}
		?>
		<tr <?php echo $class?'class="divider"':''?>>
			<td><?php echo $this->Html->link('<h6>'.$setting['Setting']['title'].'</h6>', array('action' => 'edit', $setting['Setting']['key']), array('escape' => false)); ?><span class="aside">(<?php echo $setting['Setting']['key']?>)</span></td>
			<td><?php 
					if ($setting['Setting']['value'] == null) {
						$ini = true;
						$setting['Setting']['value'] = Configure::read($setting['Setting']['key']);
					} else {
						$ini = false;
					}
					if ($setting['Setting']['type'] == 'enum') {
						$options = unserialize($setting['Setting']['enum_data']);
						echo $options[$setting['Setting']['value']].
							' <span class="aside">('.$setting['Setting']['value'].')</span>';	
					} else if ($setting['Setting']['type'] == 'longtext') {
						echo $this->Text->truncate($setting['Setting']['value'], 50);
					} else if ($setting['Setting']['type'] == 'html') {
						echo h('<HTML DATA>');
					} else if ($setting['Setting']['type'] == 'bool') {
						echo $setting['Setting']['value']?'Yes':'No';
					} else {
						echo h($setting['Setting']['value']);
					}
					if ($ini) {
						echo ' <h5 class="pull-right">(From Config/core.php)</h5>';
					}
				?> <?php if (!empty($setting['Setting']['units'])) echo $setting['Setting']['units'] ?>
			</td>
			<?php if (Configure::read('debug') == 3) {?>
			<td class="actions">
				<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $setting['Setting']['key'])); ?>
				
				<?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $setting['Setting']['key']), null, __('Are you sure you want to delete # %s?', $setting['Setting']['key'])); ?>
			</td>
			<?php }?>
		</tr>
	<?php } ?>
</table>
<?php if (Configure::read('debug') == 3) {?>
	<ul class="nav nav-pills">
		<li><?php echo $this->Html->link(__('New Setting'), array('action' => 'add')); ?></li>
	</ul>
	<?php echo $this->Form->create(null, array('action' => 'import', 'class' => 'form-search'));?>
	<div class="input-append">
		<?php echo $this->Form->text('key', array('div' => false, 'type' => 'text', 'placeholder'=> 'Import Key', 'label' => false, 'class' => 'search-query'));?>
		<?php echo $this->Form->button(__('Import Key'), array('type' => 'submit', 'div' => false, 'class' => 'btn btn-primary'))?>
	</div>
	<?php echo $this->Form->end()?>
<?php }?>
