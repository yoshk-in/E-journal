<?php


namespace App\console\parser;


use App\base\ConsoleRequest;

abstract class CommandParser implements NextArgIndex
{
    protected $command;
    protected $partial;
    protected $request;
    protected $partNumber;
    protected $numbers;
    protected $nextArg;


    public function __construct(ConsoleRequest $request)
    {
        $this->request = $request;    
    }


    public function getCommand()
    {
        $class_name = static::class;
        $concrete_command = str_replace(__NAMESPACE__ . '\\', '', $class_name);
        $concrete_command = strstr($concrete_command, Arg::REMOVE_PARSER, $before_remove = true);
        return $concrete_command;
    }

    abstract public function parse();


    public function getBlockNumbers(): ?array
    {
        return $this->numbers;
    }

    public function getPartNumber(): ?string
    {
        return $this->partNumber;
    }

    public function getPartial(): ?string
    {
        return $this->partial;
    }




}