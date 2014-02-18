<h1>Your account on <?php echo Configure::read('App.title'); ?> has been created.</h1>

<p>Before you can use your account you need to verify your email address by clicking on the link below.</p>

<p><?php echo $this->Html->link(Router::url(array('controller' => 'users', 'action' => 'verify', $user['User']['validate_key']), true));?>

