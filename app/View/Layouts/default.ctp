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
    	'/vendor/bootstrap-3.0.0/js/modal.js',
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

  <!-- Collect the nav links, forms, and other content for toggling -->
  <div class="collapse navbar-collapse navbar-ex1-collapse">
    <ul class="nav navbar-nav navbar-right">
      <li><a data-toggle="modal" href="#aboutModal">About</a>
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
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	        <button type="button" class="btn btn-primary">Save changes</button>
	      </div>
	    </div><!-- /.modal-content -->
	  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
    </body>
</html>