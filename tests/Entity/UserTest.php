<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    private $user;
    private $task;

    public function setUp(): void
    {
        $this->user = new User();
        $this->task = new Task();
    }

    public function testUserId()
    {
        $this->assertNull($this->user->getId());
    }

    public function testUsername()
    {
        $this->user->setUsername('test');
        $this->assertSame('test', $this->user->getUsername());
    }

    public function testUserPassword()
    {
        $this->user->setPassword('password');
        $this->assertSame('password', $this->user->getPassword());
    }

    public function testUserRoles()
    {
        $this->user->setRole('ROLE_TEST');
        $this->assertSame(['ROLE_TEST'], $this->user->getRoles());
    }

    public function testUserEmail()
    {
        $this->user->setEmail('test@gmail');
        $this->assertSame('test@gmail', $this->user->getEmail());
    }

    public function testSalt(): void
    {
        $this->assertNull($this->user->getSalt());
    }

    public function testEraseCredentials(): void
    {
        $this->assertNull($this->user->eraseCredentials());
    }

    public function testTask()
    {
        $this->user->addTask($this->task);
        $this->assertCount(1, $this->user->getTasks());

        $tasks = $this->user->getTasks();
        $this->assertSame($this->user->getTasks(), $tasks);

        $this->user->removeTask($this->task);
        $this->assertCount(0, $this->user->getTasks());
    }
}
