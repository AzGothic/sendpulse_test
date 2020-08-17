<?php
/** @var array $view */
?>

<h2 class="text-center"><?= htmlspecialchars($view['task']['title']) ?></h2>

<div class="alert alert-info" role="alert">
    <span class="todo-task-date">[<?= (new \DateTime($view['task']['date']))->format('Y-m-d H:i') ?>]</span>
    <span class="todo-task-status">[<?= ($view['task']['status'] == 1 ? '<b class="success">DONE</b>' : 'OPEN') ?>]</span>
    <div><?= nl2br(htmlspecialchars($view['task']['body'])) ?></div>
</div>

<div class="container text-center">
<?php if ($view['task']['status'] != 1) : ?>
    <?php if ($view['task']['parent_id'] == 0) : ?>
        <a class="btn btn-success" href="/todo/add?parent_id=<?= $view['task']['id'] ?>">+ Sub</a>
    <?php endif; ?>
    <a class="btn btn-info" href="/todo/edit?id=<?= $view['task']['id'] ?>">Edit</a>
    <a class="btn btn-warning" href="/todo/done?id=<?= $view['task']['id'] ?>">Close</a>
<?php endif; ?>
<a class="btn btn-danger" href="/todo/remove?id=<?= $view['task']['id'] ?>">Delete</a>
<?php if ($view['task']['parent_id'] != 0) : ?>
    <a class="btn btn-primary" href="/todo/view?id=<?= $view['task']['parent_id'] ?>">Back</a>
<?php else : ?>
    <a class="btn btn-primary" href="/todo">Back</a>
<?php endif; ?>
</div>

<?php if ($view['task']['parent_id'] == 0) : ?>
    <h3 class="text-center">Sub Tasks</h3>
    <?php if (!$view['subs']) : ?>
        <div class="alert alert-warning" role="alert">
            Nothing found...
        </div>
    <?php else : ?>
        <?php foreach ($view['subs'] as $task) : ?>
            <div class="alert alert-info" role="alert">
                <span class="todo-task-date">[<?= (new \DateTime($task['date']))->format('Y-m-d H:i') ?>]</span>
                <span class="todo-task-status">[<?= ($task['status'] == 1 ? '<b class="success">DONE</b>' : 'OPEN') ?>]</span>
                <a class="btn btn-sm btn-link" href="/todo/view?id=<?= $task['id'] ?>"><h5><?= htmlspecialchars($task['title']) ?></h5></a>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
<?php endif; ?>
