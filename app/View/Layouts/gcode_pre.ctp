<!DOCTYPE html>
<html lang="en">
  <!--
    GCode Viewer
    https://github.com/jherrm/gcode-viewer
  -->
  <head>
    <meta http-equiv="Access-Control-Allow-Origin" content="*">
    <meta charset="utf-8">
    <title>GCode Viewer</title>
    <?php
    	if (!function_exists('adjustPath')) {
	    	function adjustPath($dest) {
	        	return '/vendor/gcode-viewer-master/web/'.$dest;
	    	}
    	}
    ?>
    <?php echo $this->Html->css(adjustPath('lib/bootstrap.min.css'))?>
    <style>
      #renderArea {
        position: fixed;
        left: 0;
        right: 0;
        bottom: 0;
        top: 40px;
        background-color: #000000;
      }
      .dg.main {
        margin-top:40px;
      }
    </style>
    <script type="text/javascript">
    var config = {
        	lastImportedKey: 'last-imported',
    		notFirstVisitKey: 'not-first-visit',
    		defaultFilePath: '<?php echo Router::url('/files/'.$operation_id.'.gcode')?>'
    };


    </script>

    <!-- 3rd party libs -->
    <?php echo $this->Html->script(adjustPath("lib/modernizr.custom.93389.js"))?>
    <?php echo $this->Html->script(adjustPath("lib/jquery-1.7.1.min.js"))?>
    <?php echo $this->Html->script(adjustPath("lib/bootstrap-modal.js"))?>
    <?php echo $this->Html->script(adjustPath("lib/sugar-1.2.4.min.js"))?>
    <?php echo $this->Html->script(adjustPath("lib/three.js"))?>
    <?php echo $this->Html->script(adjustPath("lib/TrackballControls.js"))?>

    <?php echo $this->Html->script(adjustPath("js/ShaderExtras.js"))?>
    <?php echo $this->Html->script(adjustPath("js/postprocessing/EffectComposer.js"))?>
    <?php echo $this->Html->script(adjustPath("js/postprocessing/MaskPass.js"))?>
    <?php echo $this->Html->script(adjustPath("js/postprocessing/RenderPass.js"))?>
    <?php echo $this->Html->script(adjustPath("js/postprocessing/ShaderPass.js"))?>
    <?php echo $this->Html->script(adjustPath("js/postprocessing/BloomPass.js"))?>


    <?php echo $this->Html->script(adjustPath("js/Stats.js"))?>
    <?php echo $this->Html->script(adjustPath("js/dat.gui.min.js"))?>
    <?php echo $this->Html->script(adjustPath("gcode_model.js"))?>
    <?php echo $this->Html->script(adjustPath("gcode_parser.js"))?>
    <?php echo $this->Html->script(adjustPath("gcode_interpreter.js"))?>
    <?php echo $this->Html->script(adjustPath("gcode_importer.js"))?>
    <?php echo $this->Html->script(adjustPath("gcode_renderer.js"))?>
    <?php echo $this->Html->script(adjustPath("renderer.js"))?>
    <?php echo $this->Html->script(adjustPath("ui.js"))?>



  </head>
  <body>

    <!-- Top bar -->
    <div class="navbar navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <span class="brand" href="#">GCode Viewer</span>
          <ul class="nav">
            <li><a href="javascript:openDialog()">Load Model</a></li>
            <li><a href="javascript:about()">About</a></li>
          </ul>
          <ul class="nav pull-right">
            <li><a href="https://github.com/jherrm/gcode-viewer" target="_blank">Code on GitHub</a></li>
            <li><a href="http://twitter.com/jherrm" target="_blank">@jherrm</a></li>
          </ul>
        </div>
      </div>
    </div>

    <!-- WebGL rendering area -->
    <div id="renderArea"></div>

    <div class="modal" id="openModal" style="display: none">
      <div class="modal-header">
        <a class="close" data-dismiss="modal">&times;</a>
        <h3>Open GCode</h3>
      </div>
      <div class="modal-body">
        <h4>Examples</h4>
        <ul class="gcode_examples">
          <li><a href="examples/15mm_cube.gcode">15mm_cube.gcode</a></li>
          <li><a href="examples/octocat.gcode">octocat.gcode</a></li>
          <li><a href="examples/part.gcode">part.gcode</a></li>
        </ul>
        <p>To view your own model, drag a gcode file from your desktop and drop it in this window.</p>
      </div>
      <div class="modal-footer">
        <a class="btn" data-dismiss="modal">Cancel</a>
      </div>
    </div>

    <!-- 'About' dialog'-->
    <div class="modal fade" id="aboutModal" style="display: none">
      <div class="modal-header">
        <a class="close" data-dismiss="modal">&times;</a>
        <h3>About GCode Viewer</h3>
      </div>
      <div class="modal-body">
        <p>This is a viewer for <a href="http://en.wikipedia.org/wiki/G-code" target="_new">GCode</a>
        files, which contain commands sent to a CNC machine such as a
        <a href="http://reprap.org/" target="_blank">RepRap</a> or
        <a href="http://www.makerbot.com/" target="_blank">MakerBot</a> 3D printer.</p>

        <p>This viewer shows the operations the machine will take.</p>

        <p>Drag the mouse to rotate the model. Scroll with the mouse wheel to zoom. Right click and drag to pan.</p>

        <p>To view your own model, drag a gcode file from your desktop and drop it in this window.</p>

        <p>To learn more, read the <a href="http://jherrman.com/2012/10/gcode-viewer/">blog</a> <a href="http://joewalnes.com/2012/04/01/a-3d-webgl-gcode-viewer-for-understanding-3d-printers/" target="_new">posts</a>.</p>

        <p>This code is based off of the <a href="http://gcode.joewalnes.com">original</a> GCode Viewer by <a href="http://joewalnes.com">Joe Walnes</a>.
      </div>
      <div class="modal-footer">
        <a class="btn btn-primary" data-dismiss="modal">OK</a>
      </div>
    </div>

  </body>
</html>
