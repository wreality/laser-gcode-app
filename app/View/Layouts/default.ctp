<!DOCTYPE html>
<html>
  <head>
    <title><?php echo Configure::read('App.title')?>: <?php echo $title_for_layout?></title>
  
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap -->
    <?php echo $this->Html->script(array('/vendor/jquery/jquery-1.9.0.min.js', 'app.js'));?>
    <?php if (Configure::read('debug') > 0) {?>
    	<?php echo $this->Html->less('/less/default.less');?>
    	<?php echo $this->Html->script('/vendor/less/less-1.6.3.min.js')?>
    	<?php echo $this->Html->script('debug.js')?>
    <?php } else {?>
    	<?php echo $this->Html->css('/css/default.css');?>
    <?php }?>
    
    <?php echo $this->Html->css('/css/fonts.css');?>
    <?php echo $this->Html->script(array(
    	'/vendor/bootstrap-3.1.1/js/transition.js',
    	'/vendor/bootstrap-3.1.1/js/collapse.js',
    	'/vendor/bootstrap-3.1.1/js/dropdown.js',
    	'/vendor/bootstrap-3.1.1/js/modal.js',
    	'/vendor/bootstrap-3.1.1/js/alert.js',
    	'/vendor/bootstrap-3.1.1/js/tab.js',
    ));?>

 
  </head>
  <body>
  
  <nav class="navbar navbar-default" role="navigation">
  <!-- Brand and toggle get grouped for better mobile display -->
  <div class="navbar-header">
    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
      <span class="sr-only">Toggle navigation</span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
    </button>
    <?php echo $this->Html->link(__('GCode Generator'), '/', array('class' => 'navbar-brand'))?>
    
  </div>
<?php $current_user = AuthComponent::user(); $this->set('current_user', $current_user); ?>
  <!-- Collect the nav links, forms, and other content for toggling -->
  <div class="collapse navbar-collapse navbar-ex1-collapse">
  	<ul class="nav navbar-nav">
  		<?php if (!empty($current_user)) {?>
  			<li><?php echo $this->Html->link(__('My Projects'), array('controller' => 'projects', 'action' => 'home', 'admin' => false));?></li>
  			<li><?php echo $this->Html->link(__('Claim Projects'), array('controller' => 'projects', 'action' => 'claim', 'admin' => false));?></li>
  		<?php }?>
  		<li><?php echo $this->Html->link(__('Browse Public Projects'), array('controller' => 'projects', 'action' => 'index', 'admin' => false));?></li>
  	</ul>
    <ul class="nav navbar-nav navbar-right">
      <li><a data-toggle="modal" href="#aboutModal">About</a></li>
        <?php if (!empty($current_user)) {?>
			<?php if ($current_user['admin']) {?>
		      <li class="dropdown">
				<?php echo $this->Html->dropdownStart(__('System Wide'))?>
					<?php echo $this->Html->dropdownItem(__('Manage Users'), array('controller' => 'users', 'action' => 'index', 'admin' => true))?>
					<?php echo $this->Html->dropdownItem(__('Manage Projects'), array('controller' => 'projects', 'action' => 'index', 'admin' => true))?>
						<?php echo $this->Html->dropdownDivider()?>
					<?php echo $this->Html->dropdownItem(__('Settings'), array('controller' => 'settings', 'action' => 'index', 'admin' => true));?>
					<?php echo $this->Html->dropdownItem(__('Presets'), array('controller' => 'presets', 'action' => 'index', 'admin' => false));?>
					<?php echo $this->Html->dropdownDivider()?>
					<?php echo $this->Html->dropdownItem(__('Utility GCode'), array('controller' => 'g_codes', 'action' => 'index', 'admin' => false));?>
				<?php echo $this->Html->dropdownEnd();?>
			  </li>
			<?php } ?>
			<li class="dropdown">
			<?php echo $this->Html->dropdownStart(__('%s (%s)', $current_user['username'], $current_user['email']))?>
				<?php echo $this->Html->dropdownItem(__('Account Details'), array('controller' => 'users', 'action' => 'account', 'admin' => false))?>
				<?php echo $this->Html->dropdownDivider()?>
				<?php echo $this->Html->dropdownItem(__('Logout'), array('controller' => 'users', 'action' => 'logout', 'admin' => false))?>
			<?php echo $this->Html->dropdownEnd()?>
			</li>
        <?php } else {?>
        	<li><?php echo $this->Html->link(__('Login/Create Account'), array('controller' => 'users', 'action' => 'login', 'admin' => false))?>
        <?php }?>		
    </ul>
   
  </div><!-- /.navbar-collapse -->
</nav>
  		<div class="container">
  			<div class="row">
  				<?php echo $this->Session->flash();?>
  				<?php echo $this->fetch('top_content')?>
  			</div>
  			<?php 
  				if ($this->fetch('sidebar')) {
					$class = 'main-pane';
				}
				elseif ($this->fetch('narrow')) {
					$class = 'narrow-pane';
				} else {
					$class = 'full-pane';
				}  			
  			?>
  			<div class="row <?php echo $class?>">
  			
  				<?php if ($class == 'main-pane'): ?>
		  			<div class="sidebar">
			    		<?php echo $this->fetch('sidebar');?>
			    	</div>
			    	<div class="content">
			    		<?php echo $this->fetch('content');?>
			    	</div>
			    <?php elseif ($class == 'narrow-pane'):?>
			    	<div class="pad">&nbsp;</div>
			    	<div class="content">
			    		<?php echo$this->fetch('narrow');?>
			    	</div>
			    <?php else: ?>
			    	<div class="pad">&nbsp;</div>
			    	<div class="content">
			    		<?php Echo $this->fetch('content');?>
			    	</div>
			    	<div class="oad">&nbsp;</div>
			    <?php endif; ?>
		    </div>
	    </div>
	     <div class="modal fade" id="aboutModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	  <div class="modal-dialog">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	        <h4 class="modal-title">About GCode Generator</h4>
	      </div>
	      <div class="modal-body">
	        <p>GCode Generator creates gcode files from PDFs for use on the <?php echo $this->Html->link('Lansing Makers Network\'s', 'http://lansingmakersnetwork.org/')?>
	           buildlog.net laser cutter.</p>
	        <p>Under the hood, GCode Generator relies on several OpenSource libraries and projects:
	        	<ul>
	        		<li>GCode Generation: <?php echo $this->Html->link('https://github.com/timschmidt/pstoedit-lmn-laser')?></li>
	        		<li>GCode Preview: <?php echo $this->Html->link('https://github.com/jherrm/gcode-viewer');?></li>
	        		<li>CakePHP Framework: <?php echo $this->Html->link('https://github.com/cakephp/cakephp')?></li>
	        	</ul>
	        </p>
	        <p>GCode Generator is open source under the GPL.  Source code is available at <?php echo $this->Html->link('https://github.com/wreality/laser-gcode-app')?></p>
	      	<p>Version: <?php echo Configure::read('App.version');?></p>
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	     
	      </div>
	    </div><!-- /.modal-content -->
	  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<?php echo $this->element('sql_dump')?>
    </body>
</html>