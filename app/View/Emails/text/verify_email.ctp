Your account on <?php echo Configure::read('App.title'); ?> has been created.

Before you can use your account you need to verify your email address by visiting the link below.

<?php echo Router::url(array('controller' => 'users', 'action' => 'verify', $user['User']['validate_key']), true);?>

