<?php
use app\base\Form;
/** @var array $view */
?>
<h2 class="text-center">Registration</h2>

<form action="/user/registration" method="post">
    <input name="csrf_token" type="hidden" value="<?= Form::getCsrfToken() ?>">
    <div class="form-group">
        <label for="name">Name</label>
        <input name="name" type="text" class="form-control" id="name" placeholder="Name"
               value="<?= (isset($view['name']) ? $view['name'] : '') ?>">
    </div>
    <div class="form-group">
        <label for="email" class="required">Email</label>
        <input name="email" type="email" class="form-control" id="email" placeholder="Email"
               value="<?= (isset($view['email']) ? $view['email'] : '') ?>">
    </div>
    <div class="form-group">
        <label for="password" class="required">Password</label>
        <input name="password" type="password" class="form-control" id="password" placeholder="Password">
    </div>
    <div class="text-center">
        <button type="submit" class="btn btn-success">Register</button>
        <a href="/user/login" class="btn btn-link">Login</a>
    </div>
</form>
