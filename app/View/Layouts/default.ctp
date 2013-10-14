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
    	'/vendor/bootstrap-3.0.0/js/collapse.js'
    ));?>
 
  </head>
  <body>
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