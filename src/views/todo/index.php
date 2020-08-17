<?php
/** @var array $view */
?>

<div class="container text-center">
    <a class="btn btn-success" href="/todo/add">+ Task</a>
</div>

<h2 class="text-center">Tasks</h2>

<?php if (!$view['tasks']) : ?>
    <div class="alert alert-warning" role="alert">
        Nothing found...
    </div>
<?php else : ?>
    <?php foreach ($view['tasks'] as $task) : ?>
        <div class="alert alert-info" role="alert">
            <span class="todo-task-date">[<?= (new \DateTime($task['date']))->format('Y-m-d H:i') ?>]</span>
            <span class="todo-task-status">[<?= ($task['status'] == 1 ? '<b class="success">DONE</b>' : 'OPEN') ?>]</span>
            <a class="btn btn-sm btn-link" href="/todo/view?id=<?= $task['id'] ?>"><h4><?= htmlspecialchars($task['title']) ?></h4></a>
        </div>
    <?php endforeach; ?>
<?php endif; ?>