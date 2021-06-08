<?php

namespace App\Tests\EventSubscriber;

use App\EventSubscriber\ArchivageSubscriber;
use Monolog\Test\TestCase;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class ExceptionSubscriberTest extends TestCase {
    public function testEventSubscriber() {
        $this->assertArrayHasKey(ExceptionEvent::class, ArchivageSubscriber::class);
    }
}