<?php

namespace App\command;

use App\base\AbstractRequest;
use App\base\AppMsg;
use App\command\DBFindNumbers\MoveProductByRequest;
use App\command\casual\{ClearJournal, Party};
use Psr\Container\ContainerInterface;
use App\command\DBFindNumbers\{RangeInfo, CurrentProcInfo};

class CmdResolver
{
    private string $defaultCmd = Info::class;
    private AbstractRequest $request;
    private ContainerInterface $container;

    const CMD_MAP = [
        AppMsg::RANGE_INFO => RangeInfo::class,
        AppMsg::PRODUCT_INFO => Info::class,
        AppMsg::MOVE_PRODUCT => MoveProductByRequest::class,
        AppMsg::PARTY => Party::class,
        AppMsg::CLEAR_JOURNAL => ClearJournal::class,
        AppMsg::FORWARD => Forward::class,
        AppMsg::GUI_INFO => GUIInfo::class,
        AppMsg::CREATE_PRODUCTS => CreateProducts::class,
        AppMsg::CURRENT_PROCEDURE_INFO => CurrentProcInfo::class,
        AppMsg::CREATE_PRODUCT_OR_GENERATE => CreateProductOrGenerate::class,
        AppMsg::STAT_INFO => StartedAndUnfinishedInfoProducts::class,
        AppMsg::CHANGE_PRODUCT_MAIN_NUMBER => ChangeProductNumber::class,
    ];

    public function __construct(ContainerInterface $container, AbstractRequest $request)
    {
        $this->container = $container;
        $this->request = $request;
    }


    public function getCommand(): array
    {
        $result_cmd_array = [];
        $commands = $this->request->getCmd();

        if (!empty($commands)) {
            foreach ($commands as $command) {
                $result_cmd_array[] = $this->container->get(self::CMD_MAP[$command]);
            }
            return $result_cmd_array;

        } else {
            return $this->container->get($this->defaultCmd);
        }
    }
}
