<?php
/**
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Layouts.Email.html
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN">

<html>
<head>
	<title><?php echo $title_for_layout; ?></title>
	<?php echo $this->Html->css('email')?>	
</head>

<body>

<div class="email-header">
	
	<div class="container">
    
		<div class="navbar-header">
      		<a class="navbar-brand" href="#"><?php echo Configure::read('App.title')?></a>
      	</div>
      	<p class="navbar-text"><?php echo $title_for_layout?></p>
	</div>
</div>
<div class="body-wrap">
	<div class="innerWrap">
		<?php echo $this->fetch('content'); ?>
	</div>
</div>
</body>
</html>
