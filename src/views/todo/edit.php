<?php
use app\base\Form;
/** @var array $view */
?>

<h2 class="text-center">Edit</h2>

<form action="/todo/edit?id=<?= $view['id'] ?>" method="post">
    <input name="csrf_token" type="hidden" value="<?= Form::getCsrfToken() ?>">
    <div class="form-group">
        <label for="date" class="required">Date/Time</label>
        <div class="input-group date datetimepicker-handler" id="datetimepicker1" data-target-input="nearest">
            <input name="date" type="text" class="form-control datetimepicker-input" id="date" placeholder="Choose..." data-target="#datetimepicker1"
                   value="<?= (isset($view['date']) ? $view['date'] : '') ?>" />
            <div class="input-group-append" data-target="#datetimepicker1" data-toggle="datetimepicker">
                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label for="title" class="required">Title</label>
        <input name="title" type="text" class="form-control" id="title" placeholder="Title"
               value="<?= (isset($view['title']) ? $view['title'] : '') ?>">
    </div>
    <div class="form-group">
        <label for="body" class="required">Body</label>
        <textarea name="body" class="form-control" id="body" placeholder="Body"><?= (isset($view['body']) ? $view['body'] : '') ?></textarea>
    </div>
    <div class="text-center">
        <button type="submit" class="btn btn-success">Save</button>
        <a href="/todo/view?id=<?= $view['id'] ?>" class="btn btn-primary">Back</a>
    </div>
</form>