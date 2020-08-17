<?php
use app\base\Form;
/** @var array $view */
?>
<h2 class="text-center">Login</h2>

<form action="/user/login" method="post">
    <input name="csrf_token" type="hidden" value="<?= Form::getCsrfToken() ?>">
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
        <button type="submit" class="btn btn-success">Login</button>
        <a href="/user/registration" class="btn btn-link">Registration</a>
    </div>
</form>
