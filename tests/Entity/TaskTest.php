<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class TaskTest extends TestCase
{
    private $task;

    public function setUp(): void
    {
        $this->task = new Task();
    }

    public function testId(): void
    {
        $this->assertNull($this->task->getId());
    }

    public function testTitle()
    {
        $this->task->setTitle('my title');
        $this->assertSame('my title', $this->task->getTitle());
    }

    public function testContent()
    {
        $this->task->setContent('task long content');
        $this->assertSame('task long content', $this->task->getContent());
    }

    public function testIsDone()
    {
        $this->task->toggle(true);
        $this->assertSame(true, $this->task->isDone());
    }

    public function testCreatedAt()
    {
        $date = new \DateTime();
        $this->task->setCreatedAt($date);
        $this->assertSame($date, $this->task->getCreatedAt());
    }

    public function testAuthor()
    {
        $this->task->setAuthor(new User());
        $this->assertInstanceOf(User::class, $this->task->getAuthor());
    }
}
