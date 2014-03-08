<h1>Please verify your updated email address for  <?php echo Configure::read('App.title'); ?>.</h1>

<p>Before your email address can be updated you need to verify your email address by clicking on the link below.</p>

<p><?php echo $this->Html->link(__('Click to verify your email address'), Router::url(array('controller' => 'users', 'action' => 'update_email_verify', $user['User']['validate_key']), true));?>

