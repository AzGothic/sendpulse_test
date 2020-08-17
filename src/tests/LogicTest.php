<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use app\models\User;
use app\models\Task;
use app\base\Application;

class LogicTest extends TestCase
{
    protected $user;
    protected $task;

    protected function setUp(): void
    {
        require_once __DIR__ . '/bootstrap.php';

        $this->user = new User();
        $this->task = new Task();
    }

    public function testUserRegistration(): void
    {
        $this->user->remove('tests@tests.tests');

        $this->assertTrue(
            $this->user->registration([
                'name' => 'Tests',
                'email' => 'tests@tests.tests',
                'password' => 'tests',
            ], false)
        );
    }

    public function testUserLoginByEmail(): array
    {
        $this->assertIsArray(
            $this->user->login('tests@tests.tests', 'tests')
        );

        return $this->user->getUserByEmail('tests@tests.tests');
    }

    /**
     * @depends testUserLoginByEmail
     */
    public function testUserLoginByToken(array $user): void
    {
        $this->assertIsArray(
            $this->user->login(null, null, $user['token'])
        );
    }


    /**
     * @depends testUserLoginByEmail
     */
    public function testCreateTask(array $user): void
    {
        $this->assertTrue(
            $this->task->create([
                'user_id' => $user['id'],
                'date'    => date('Y-m-d H:i:s'),
                'title'   => 'Tests title',
                'body'    => 'Tests body',
            ])
        );
    }

    /**
     * @depends testUserLoginByEmail
     */
    public function testGetTasksList(array $user): array
    {
        $tasks = $this->task->getTasks(0, $user['id']);
        $this->assertIsArray(
            $tasks
        );

        return $tasks;
    }

    /**
     * @depends testGetTasksList
     */
    public function testGetTask(array $tasks): array
    {
        $task = $this->task->getTask($tasks[0]['id'], $tasks[0]['user_id']);
        $this->assertIsArray(
            $task
        );

        return $task;
    }

    /**
     * @depends testGetTask
     */
    public function testEditTask(array $task): void
    {
        $this->assertTrue(
            $this->task->update([
                'id'      => $task['id'],
                'user_id' => $task['user_id'],
                'date'    => date('Y-m-d H:i:s'),
                'title'   => 'Tests title new',
                'body'    => 'Tests body new',
            ])
        );
    }

    /**
     * @depends testGetTask
     */
    public function testCreateSubTask(array $task): void
    {
        $this->assertTrue(
            $this->task->create([
                'parent_id' => $task['id'],
                'user_id' => $task['user_id'],
                'date'    => date('Y-m-d H:i:s'),
                'title'   => 'Tests sub title',
                'body'    => 'Tests sub body',
            ])
        );
    }

    /**
     * @depends testGetTask
     */
    public function testGetSubTasks(array $task): array
    {
        $subs = $this->task->getTasks($task['id'], $task['user_id']);
        $this->assertIsArray(
            $subs
        );

        return ($subs ? $subs[0] : null);
    }

    /**
     * @depends testGetSubTasks
     */
    public function testEditSub(array $sub): void
    {
        $this->assertTrue(
            $this->task->update([
                'id'      => $sub['id'],
                'user_id' => $sub['user_id'],
                'date'    => date('Y-m-d H:i:s'),
                'title'   => 'Tests sub title new',
                'body'    => 'Tests sub body new',
            ])
        );
    }

    /**
     * @depends testGetTask
     */
    public function testDeleteSub(array $task): void
    {
        $this->assertTrue(
            $this->task->create([
                'parent_id' => $task['id'],
                'user_id' => $task['user_id'],
                'date'    => date('Y-m-d H:i:s'),
                'title'   => 'Tests sub 2 title',
                'body'    => 'Tests sub 2 body',
            ])
        );

        $sub = $this->task->getTasks($task['id'], $task['user_id'])[0];
        $this->assertIsArray(
            $sub
        );

        $this->assertTrue(
            $this->task->remove($sub['id'], $sub['user_id'])
        );

        $this->assertIsArray($this->task->getTask($task['id'], $sub['user_id']));
        $this->assertSame(1, count($this->task->getTasks($task['id'], $sub['user_id'])));
    }

    /**
     * @depends testGetSubTasks
     */
    public function testCloseParentTask(array $sub): void
    {
        $this->assertTrue(
            $this->task->markAsDone($sub['parent_id'], $sub['user_id'])
        );

        $sub = $this->task->getTask($sub['id'], $sub['user_id']);
        $this->assertSame(1, (int) $sub['status']);
    }

    /**
     * @depends testGetSubTasks
     */
    public function testDeleteParentTask(array $sub): void
    {
        $this->assertTrue(
            $this->task->remove($sub['parent_id'], $sub['user_id'])
        );

        $this->assertSame(false, $this->task->getTask($sub['id'], $sub['user_id']));
        $this->assertSame(false, $this->task->getTask($sub['parent_id'], $sub['user_id']));
    }

    /**
     * @depends testUserLoginByEmail
     */
    public function testCloseAllSubs(array $user): void
    {
        $this->assertTrue(
            $this->task->create([
                'user_id' => $user['id'],
                'date'    => date('Y-m-d H:i:s'),
                'title'   => 'Tests title',
                'body'    => 'Tests body',
            ])
        );

        $task = $this->task->getTasks(0, $user['id'])[0];
        $this->assertIsArray(
            $task
        );

        $this->assertTrue(
            $this->task->create([
                'parent_id' => $task['id'],
                'user_id' => $task['user_id'],
                'date'    => date('Y-m-d H:i:s'),
                'title'   => 'Tests sub title',
                'body'    => 'Tests sub body',
            ])
        );

        $sub = $this->task->getTasks($task['id'], $user['id'])[0];
        $this->assertIsArray(
            $sub
        );

        $this->assertTrue(
            $this->task->markAsDone($sub['id'], $sub['user_id'])
        );

        $task = $this->task->getTask($sub['parent_id'], $sub['user_id']);
        $this->assertSame(1, (int) $task['status']);
    }

    /**
     * @depends testUserLoginByEmail
     */
    public function testUserLogout(array $user): void
    {
        $this->user->login(null, null, $user['token']);
        $this->assertTrue(
            $this->user->logout()
        );
    }

    /**
     * @depends testUserLoginByEmail
     */
    public function testUserDelete(array $user): void
    {
        $this->assertTrue(
            $this->user->remove($user['email'])
        );
    }
}
