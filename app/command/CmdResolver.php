<?php

namespace App\command;

use App\base\AbstractRequest;
use App\base\AppCmd;
use App\CLI\parser\ProcessingProductInfo;
use App\helpers\Gen;
use App\helpers\TContainerGet;
use App\command\RepositoryLessCmd\{ClearJournal, SetPartNumber};
use App\command\RepositoryCmd\foundHandler\{ChangeNumberHandler,
    EndProcHandler,
    ForwardHandler,
    ReportProcInfoHandler,
    ReportInfoHandler,
    StartProcHandler};
use App\command\RepositoryCmd\notFoundHandler\{CommonNotFoundHandler,RequestDependNotFoundHandler};
use App\command\RepositoryCmd\repositoryRequest\{CreateRequest,
    UnfinishedRequest,
    GenerateRequest,
    ByIdRequest};
use App\command\RepositoryCmd\{PersistRepositoryCmd, RepositoryCmd};
use Psr\Container\ContainerInterface;

class CmdResolver
{
    use TContainerGet;

    const DEFAULT_CMD = ProcessingProductInfo::class;
    private AbstractRequest $request;

    const CMD_MAP = [
        AppCmd::CONCRETE_PRODUCT_INFO => [RepositoryCmd::class, [ByIdRequest::class, ReportInfoHandler::class, CommonNotFoundHandler::class]],
        AppCmd::START_PROCEDURE => [PersistRepositoryCmd::class, [ByIdRequest::class, StartProcHandler::class, RequestDependNotFoundHandler::class]],
        AppCmd::END_PROCEDURE => [PersistRepositoryCmd::class, [ByIdRequest::class, EndProcHandler::class, CommonNotFoundHandler::class]],
        AppCmd::SET_PART_NUMBER => [SetPartNumber::class, []],
        AppCmd::CLEAR_JOURNAL => [ClearJournal::class, []],
        AppCmd::FORWARD => [PersistRepositoryCmd::class, [ByIdRequest::class, ForwardHandler::class, CommonNotFoundHandler::class]],
        AppCmd::CREATE_PRODUCTS => [PersistRepositoryCmd::class, [CreateRequest::class]],
        AppCmd::CURRENT_PROCEDURE_INFO => [RepositoryCmd::class, [ByIdRequest::class, ReportProcInfoHandler::class, CommonNotFoundHandler::class]],
        AppCmd::FIND_UNFINISHED => [RepositoryCmd::class, [UnfinishedRequest::class, ReportInfoHandler::class, CommonNotFoundHandler::class]],
        AppCmd::CREATE_PRODUCT_OR_GENERATE => [PersistRepositoryCmd::class, [GenerateRequest::class, ReportInfoHandler::class]],
        AppCmd::CHANGE_PRODUCT_MAIN_NUMBER => [PersistRepositoryCmd::class, [UnfinishedRequest::class, ChangeNumberHandler::class]],
    ];

    public function __construct(ContainerInterface $container, AbstractRequest $request)
    {
        $this->container = $container;
        $this->request = $request;
    }


    public function getCommand(): iterable
    {
        $commands = $this->request->getCmd();
        empty($commands) ? yield $this->containerGet(self::DEFAULT_CMD) : yield from $this->generateCmd($commands);
    }

    /**
     * @param Command[] | string[] $commands
     * @return \Generator
     */
    protected function generateCmd(array $commands): \Generator
    {
        foreach ($commands as $command) {
            $cmd_parts = self::CMD_MAP[$command];
            yield $this->setCmdHandlers($cmd_parts[0], $cmd_parts[1]);
        }
    }


    protected function setCmdHandlers(string $cmd, array $handlers): Command
    {
        $cmd = $this->containerGet($cmd);
        Gen::settle($cmd->setHandlers(), $this->containerGets($handlers));
        return $cmd;
    }


}
