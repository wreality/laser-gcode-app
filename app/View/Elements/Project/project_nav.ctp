<?php echo $this->Form->postButton(__('Create new Project'), array('action' => 'add'), array('class' => 'create-proj'))?>
	<?php if (!AuthComponent::user('id')): ?>
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
	<?php else: //Logged In?>
		<div class="projects search">
			<?php 
				switch ($current) {
					case 'own':
						 $search = __('Search My Projects');
						 break;
					case 'claim':
						$search = __('Search Unclaimed Projects');
						break;
					case 'public':
						$search = __('Search Public Projects');
						break;
				}
			?>
			<h3><?php echo $search?></h3>
			<?php echo $this->Form->create('Project', array('novalidate' => 'novalidate'));?>
				<?php echo $this->Form->input('project_name', array('label' => false))?>
				<?php echo $this->Form->submit(__('Find Projects'));?>
			<?php echo $this->Form->end();?>
			<?php if (!empty($this->request->data)) {?>
				<?php echo $this->Form->postButton(__('Clear Search'));?>
			<?php } ?>
		</div>
		<?php /*?>
		<ul class="projects-nav">
			<li <?php echo $current=='own'?'class="active"':''?>><?php echo $this->Html->link(__('My Projects'), array('action' => 'home'))?></li>
			<li <?php echo $current=='claim'?'class="active"':''?>><?php echo $this->Html->link(__('Claim Projects'), array('action' => 'claim'));?></li>
			<li <?php echo $current=='public'?'class="active"':''?>><?php echo $this->Html->link(__('Browse Public Projects'), array('action' => 'index'));?></li>

		</ul>
		*/ ?>
		
	<?php endif; //Logged In ? ?>