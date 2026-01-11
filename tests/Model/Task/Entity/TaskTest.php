<?php

declare(strict_types=1);

namespace App\Tests\Model\Task\Entity;

use App\Model\Task\Entity\Task;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Task::class)]
final class TaskTest extends TestCase
{
    public function testCreateTaskWithAllProperties(): void
    {
        $title = 'Test Task';
        $description = 'Test Description';
        $status = 'new';

        $task = Task::create($title, $description, $status);

        $this->assertSame($title, $task->title);
        $this->assertSame($description, $task->description);
        $this->assertSame($status, $task->status);
        $this->assertNull($task->id);
        $this->assertNull($task->updatedAt);
    }

    public function testCreateTaskWithNullDescription(): void
    {
        $title = 'Test Task';
        $description = '';
        $status = 'pending';

        $task = Task::create($title, $description, $status);

        $this->assertSame($title, $task->title);
        $this->assertSame($description, $task->description);
        $this->assertSame($status, $task->status);
    }

    public function testCreateTaskWithDifferentStatuses(): void
    {
        $statuses = ['new', 'pending', 'active', 'inactive', 'deleted'];

        foreach ($statuses as $status) {
            $task = Task::create('Task', 'Description', $status);
            $this->assertSame($status, $task->status);
        }
    }

    public function testUpdateTaskProperties(): void
    {
        $task = Task::create('Original Title', 'Original Description', 'new');

        $newTitle = 'Updated Title';
        $newDescription = 'Updated Description';
        $newStatus = 'active';

        $task->update($newTitle, $newDescription, $newStatus);

        $this->assertSame($newTitle, $task->title);
        $this->assertSame($newDescription, $task->description);
        $this->assertSame($newStatus, $task->status);
    }

    public function testUpdateTaskWithEmptyDescription(): void
    {
        $task = Task::create('Title', 'Description', 'new');

        $task->update('New Title', '', 'active');

        $this->assertSame('New Title', $task->title);
        $this->assertSame('', $task->description);
        $this->assertSame('active', $task->status);
    }

    public function testSetCreatedAtValue(): void
    {
        $task = Task::create('Test Task', 'Test Description', 'new');
        $beforeCall = new \DateTimeImmutable('now', new \DateTimeZone('Europe/Moscow'));

        $task->setCreatedAtValue();

        $afterCall = new \DateTimeImmutable('now', new \DateTimeZone('Europe/Moscow'));

        $this->assertNotNull($task->createdAt);
        $this->assertInstanceOf(\DateTimeImmutable::class, $task->createdAt);
        $this->assertGreaterThanOrEqual($beforeCall->getTimestamp(), $task->createdAt->getTimestamp());
        $this->assertLessThanOrEqual($afterCall->getTimestamp(), $task->createdAt->getTimestamp());
        $this->assertSame('Europe/Moscow', $task->createdAt->getTimezone()->getName());
    }

    public function testSetUpdatedAtValue(): void
    {
        $task = Task::create('Test Task', 'Test Description', 'new');
        $beforeCall = new \DateTimeImmutable('now', new \DateTimeZone('Europe/Moscow'));

        $task->setUpdatedAtValue();

        $afterCall = new \DateTimeImmutable('now', new \DateTimeZone('Europe/Moscow'));

        $this->assertNotNull($task->updatedAt);
        $this->assertInstanceOf(\DateTimeImmutable::class, $task->updatedAt);
        $this->assertGreaterThanOrEqual($beforeCall->getTimestamp(), $task->updatedAt->getTimestamp());
        $this->assertLessThanOrEqual($afterCall->getTimestamp(), $task->updatedAt->getTimestamp());
        $this->assertSame('Europe/Moscow', $task->updatedAt->getTimezone()->getName());
    }

    public function testSetUpdatedAtValueCanBeCalledMultipleTimes(): void
    {
        $task = Task::create('Test Task', 'Test Description', 'new');

        $task->setUpdatedAtValue();
        $firstUpdatedAt = $task->updatedAt;

        usleep(10000);

        $task->setUpdatedAtValue();
        $secondUpdatedAt = $task->updatedAt;

        $this->assertNotNull($firstUpdatedAt);
        $this->assertNotNull($secondUpdatedAt);
        $this->assertGreaterThanOrEqual($firstUpdatedAt->getTimestamp(), $secondUpdatedAt->getTimestamp());
    }

    public function testCreateMultipleTasksAreIndependent(): void
    {
        $task1 = Task::create('Task 1', 'Description 1', 'new');
        $task2 = Task::create('Task 2', 'Description 2', 'pending');

        $this->assertSame('Task 1', $task1->title);
        $this->assertSame('Task 2', $task2->title);
        $this->assertSame('new', $task1->status);
        $this->assertSame('pending', $task2->status);
        $this->assertNotSame($task1, $task2);
    }

    public function testUpdateDoesNotChangeIdOrCreatedAt(): void
    {
        $task = Task::create('Original Title', 'Original Description', 'new');
        $task->setCreatedAtValue();
        $originalCreatedAt = $task->createdAt;

        $task->update('New Title', 'New Description', 'active');

        $this->assertNull($task->id);
        $this->assertSame($originalCreatedAt, $task->createdAt);
        $this->assertNull($task->updatedAt);
    }

    public function testUpdateAndThenSetUpdatedAtValue(): void
    {
        $task = Task::create('Original Title', 'Original Description', 'new');
        $task->setCreatedAtValue();

        $task->update('New Title', 'New Description', 'active');
        $task->setUpdatedAtValue();

        $this->assertSame('New Title', $task->title);
        $this->assertSame('New Description', $task->description);
        $this->assertSame('active', $task->status);
        $this->assertNotNull($task->createdAt);
        $this->assertNotNull($task->updatedAt);
        $this->assertGreaterThanOrEqual($task->createdAt->getTimestamp(), $task->updatedAt->getTimestamp());
    }
}

