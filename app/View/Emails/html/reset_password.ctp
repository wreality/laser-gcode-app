<h1>Reset Password</h1>

<p>Click the link below to reset your user account password on <?php echo Configure::read('App.title');?>.</p>

<p><?php echo $this->Html->link(__('Reset Password'), Router::url(array('controller' => 'users', 'action' => 'reset', $user['User']['validate_key']), true));?>

