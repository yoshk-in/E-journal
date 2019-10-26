<?php

namespace App\command;

use App\base\AbstractRequest;
use App\base\AppMsg;
use App\base\exceptions\AppException;
use Psr\Container\ContainerInterface;

class CmdResolver
{
    private $defaultCmd = Info::class;
    private $request;
    private $container;

    const CMD_MAP = [
        AppMsg::RANGE_INFO => RangeInfo::class,
        AppMsg::INFO => Info::class,
        AppMsg::DISPATCH => Dispatch::class,
        AppMsg::ARRIVE => Arrive::class,
        AppMsg::PARTY => Party::class,
        AppMsg::CLEAR_JOURNAL => ClearJournal::class,
        AppMsg::FORWARD => Forward::class
    ];

    public function __construct(ContainerInterface $container, AbstractRequest $request)
    {
        $this->container = $container;
        $this->request = $request;
    }


    public function getCommand() : array
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
