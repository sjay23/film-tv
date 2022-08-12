<?php

declare(strict_types=1);

namespace App\Service\Parsers;

use App\Entity\CommandTask;
use App\Entity\Provider;
use App\Repository\ProviderRepository;
use App\Service\TaskService;
use Exception;

class ExecParserService
{
    public function __construct(
        $parser,
        TaskService $taskService,
        ProviderRepository $providerRepository,
    )
    {
        $this->parser = $parser;
        $this->taskService = $taskService;
        $this->providerRepository = $providerRepository;
        $this->task = $this->getTask();
    }

    public function exec(): void
    {
        $this->taskService->updateCountTask($this->task);
        if ($this->task->getStatus() == 1) {
            throw new Exception('Task is running.');
        }
        $this->taskService->setWorkStatus($this->task);
        $this->parser->parserPages();
        $this->taskService->setNotWorkStatus($this->task);
    }

    /**
     * @param $name
     * @return Provider|null
     */
    protected function getProvider($name): ?Provider
    {
        return $this->providerRepository->findOneBy(['name' => $name]);
    }

    /**
     * @return CommandTask|null
     */
    protected function getTask(): ?CommandTask
    {
        return $this->taskService->getTask($this->getProvider($this->parser->getParserName()));
    }
}
