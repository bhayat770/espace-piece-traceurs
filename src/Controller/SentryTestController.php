<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SentryTestController extends AbstractController
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    #[Route('/_sentry-test', name: 'app_sentry_test')]
    public function testLog(): Response
    {
        $this->logger->error('My custom logged error.');

        // the following code will test if an uncaught exception logs to sentry
        throw new RuntimeException('Example exception.');
    }
}
