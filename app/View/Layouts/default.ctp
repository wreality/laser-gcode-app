<!DOCTYPE html>
<html>
  <head>
    <title><?php echo $title_for_layout?></title>
  
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap -->
    <?php echo $this->Html->script(array('/vendor/jquery/jquery-1.9.0.min.js', 'app.js'));?>
    <?php echo $this->Html->less('/less/default.less');?>
    <?php echo $this->Html->script(array(
    	'/vendor/less/less-1.3.3.min.js', 
    	'/vendor/bootstrap-3.0.0/js/transition.js',
    	'/vendor/bootstrap-3.0.0/js/collapse.js',
    	'/vendor/bootstrap-3.0.0/js/dropdown.js',
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
    <a class="navbar-brand" href="#">GCode Generator</a>
  </div>

  <!-- Collect the nav links, forms, and other content for toggling -->
  <div class="collapse navbar-collapse navbar-ex1-collapse">
    <ul class="nav navbar-nav navbar-right">
      
      <li class="dropdown">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown">System Wide <b class="caret"></b></a>
        <ul class="dropdown-menu">
          <li><?php echo $this->Html->link(__('Settings'), array('controller' => 'settings', 'action' => 'index'));?></li>
          <li><?php echo $this->Html->link(__('Presets'), array('controller' => 'presets', 'action' => 'index'));?></li>
         
        </ul>
      </li>
    </ul>
   
  </div><!-- /.navbar-collapse -->
</nav>
  		<div class="container">
  			<div class="row">
  				<?php echo $this->Session->flash();?>
  			</div>
  			<div class="row">
	  			<div class="col-md-3">
		    		<?php echo $this->fetch('sidebar');?>
		    	</div>
		    	<div class="col-md-9">
		    		<?php echo $this->fetch('content');?>
		    	</div>
		    </div>
	    </div>
    </body>
</html>