<?php

namespace App\Tests\Controller;

use App\Entity\Task;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class TaskControllerTest extends WebTestCase
{
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        parent::setUp();
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        // Очистка базы данных после каждого теста
        $this->entityManager->createQuery('DELETE FROM App\Entity\Task')->execute();
        $this->entityManager->close();
    }

    public function testCreateTask(): void
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/tasks',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'title' => 'Test Task',
                'description' => 'Test Description',
                'status' => 'pending'
            ])
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $response);
        $this->assertEquals('Test Task', $response['title']);
        $this->assertEquals('Test Description', $response['description']);
        $this->assertEquals('pending', $response['status']);
    }

    public function testCreateTaskWithEmptyTitle(): void
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/tasks',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'title' => '',
                'description' => 'Test Description',
                'status' => 'pending'
            ])
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('errors', $response);
        $this->assertArrayHasKey('title', $response['errors']);
    }

    public function testCreateTaskWithInvalidJson(): void
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/tasks',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            'invalid json'
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('error', $response);
    }

    public function testListTasks(): void
    {
        // Создаем тестовые задачи
        $task1 = new Task();
        $task1->setTitle('Task 1');
        $task1->setDescription('Description 1');
        $task1->setStatus('pending');
        $task1->setCreatedAt(new \DateTimeImmutable());
        $task1->setUpdatedAt(new \DateTimeImmutable());

        $task2 = new Task();
        $task2->setTitle('Task 2');
        $task2->setDescription('Description 2');
        $task2->setStatus('completed');
        $task2->setCreatedAt(new \DateTimeImmutable());
        $task2->setUpdatedAt(new \DateTimeImmutable());

        $this->entityManager->persist($task1);
        $this->entityManager->persist($task2);
        $this->entityManager->flush();

        $client = static::createClient();
        $client->request('GET', '/tasks');

        $this->assertResponseIsSuccessful();
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($response);
        $this->assertCount(2, $response);
        $this->assertEquals('Task 1', $response[0]['title']);
        $this->assertEquals('Task 2', $response[1]['title']);
    }

    public function testShowTask(): void
    {
        // Создаем тестовую задачу
        $task = new Task();
        $task->setTitle('Test Task');
        $task->setDescription('Test Description');
        $task->setStatus('pending');
        $task->setCreatedAt(new \DateTimeImmutable());
        $task->setUpdatedAt(new \DateTimeImmutable());

        $this->entityManager->persist($task);
        $this->entityManager->flush();
        $taskId = $task->getId();

        $client = static::createClient();
        $client->request('GET', '/tasks/' . $taskId);

        $this->assertResponseIsSuccessful();
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals($taskId, $response['id']);
        $this->assertEquals('Test Task', $response['title']);
        $this->assertEquals('Test Description', $response['description']);
        $this->assertEquals('pending', $response['status']);
    }

    public function testShowNonExistentTask(): void
    {
        $client = static::createClient();
        $client->request('GET', '/tasks/99999');

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('error', $response);
        $this->assertEquals('Task not found', $response['error']);
    }

    public function testUpdateTask(): void
    {
        // Создаем тестовую задачу
        $task = new Task();
        $task->setTitle('Original Title');
        $task->setDescription('Original Description');
        $task->setStatus('pending');
        $task->setCreatedAt(new \DateTimeImmutable());
        $task->setUpdatedAt(new \DateTimeImmutable());

        $this->entityManager->persist($task);
        $this->entityManager->flush();
        $taskId = $task->getId();

        $client = static::createClient();
        $client->request(
            'PUT',
            '/tasks/' . $taskId,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'title' => 'Updated Title',
                'description' => 'Updated Description',
                'status' => 'completed'
            ])
        );

        $this->assertResponseIsSuccessful();
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals($taskId, $response['id']);
        $this->assertEquals('Updated Title', $response['title']);
        $this->assertEquals('Updated Description', $response['description']);
        $this->assertEquals('completed', $response['status']);
    }

    public function testUpdateTaskWithEmptyTitle(): void
    {
        // Создаем тестовую задачу
        $task = new Task();
        $task->setTitle('Original Title');
        $task->setDescription('Original Description');
        $task->setStatus('pending');
        $task->setCreatedAt(new \DateTimeImmutable());
        $task->setUpdatedAt(new \DateTimeImmutable());

        $this->entityManager->persist($task);
        $this->entityManager->flush();
        $taskId = $task->getId();

        $client = static::createClient();
        $client->request(
            'PUT',
            '/tasks/' . $taskId,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'title' => '',
                'description' => 'Updated Description',
                'status' => 'completed'
            ])
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('errors', $response);
        $this->assertArrayHasKey('title', $response['errors']);
    }

    public function testUpdateNonExistentTask(): void
    {
        $client = static::createClient();
        $client->request(
            'PUT',
            '/tasks/99999',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'title' => 'Updated Title',
                'description' => 'Updated Description',
                'status' => 'completed'
            ])
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('error', $response);
        $this->assertEquals('Task not found', $response['error']);
    }

    public function testDeleteTask(): void
    {
        // Создаем тестовую задачу
        $task = new Task();
        $task->setTitle('Task to Delete');
        $task->setDescription('Description');
        $task->setStatus('pending');
        $task->setCreatedAt(new \DateTimeImmutable());
        $task->setUpdatedAt(new \DateTimeImmutable());

        $this->entityManager->persist($task);
        $this->entityManager->flush();
        $taskId = $task->getId();

        $client = static::createClient();
        $client->request('DELETE', '/tasks/' . $taskId);

        $this->assertResponseIsSuccessful();
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('message', $response);
        $this->assertEquals('Task deleted successfully', $response['message']);

        // Проверяем, что задача действительно удалена
        $deletedTask = $this->entityManager->getRepository(Task::class)->find($taskId);
        $this->assertNull($deletedTask);
    }

    public function testDeleteNonExistentTask(): void
    {
        $client = static::createClient();
        $client->request('DELETE', '/tasks/99999');

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('error', $response);
        $this->assertEquals('Task not found', $response['error']);
    }
}

