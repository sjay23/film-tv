<?php

declare(strict_types=1);

namespace App\Service\Parsers;

use App\Entity\CommandTask;
use App\Entity\Provider;
use App\Repository\ProviderRepository;
use App\Service\TaskService;
use Exception;

final class ExecParserService
{
    private TaskService $taskService;
    private ProviderRepository $providerRepository;
    private ?CommandTask $task;

    /**
     * @throws Exception
     */
    public function __construct(
        TaskService $taskService,
        ProviderRepository $providerRepository,
    ) {
        $this->taskService = $taskService;
        $this->providerRepository = $providerRepository;
    }

    /**
     * @param MainParserService $parser
     * @return void
     * @throws Exception
     */
    public function exec(MainParserService $parser): void
    {
        $this->setTask($parser);
        $this->taskService->updateCountTask($this->task);
        if ($this->task->getStatus() == 1) {
            throw new Exception('Task is running.');
        }
        $this->taskService->setWorkStatus($this->task);
        $parser->parserPages();
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
     * @param MainParserService $parser
     * @return void
     * @throws Exception
     */
    protected function setTask(MainParserService $parser): void
    {
        $this->task = $this->taskService->getTask($this->getProvider($parser->getParserName()));
    }
}
