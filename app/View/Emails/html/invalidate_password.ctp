<h1>Password Invalidated</h1>

<p>Your password on <?php echo Configure::read('App.title');?> has been invalidated by an admin user.  You will need to click the link below to reset your password.</p>
<p>You may also visit <?php echo $this->Html->link(Configure::read('App.title'), '/', true); ?> and select the Forgot Password link.</p>

<p><?php echo $this->Html->link(__('Reset Password'), Router::url(array('controller' => 'users', 'action' => 'reset', $user['User']['validate_key']), true));?>

