<?php


namespace App\console\parser;


use App\base\ConsoleRequest;

abstract class CommandMapParser implements NextArgIndexMap
{
    protected $command;
    protected $partial;
    protected $request;
    protected $partNumber;
    protected $numbers;
    protected $nextArg;
    const REMOVE_PARSER_WORD = 'Parser';

    public function __construct(ConsoleRequest $request)
    {
        $this->request = $request;    
    }


    public function getCommand()
    {
        $class_name = static::class;
        $concrete_command = str_replace(__NAMESPACE__ . '\\', '', $class_name);
        $concrete_command = strstr($concrete_command, self::REMOVE_PARSER_WORD, $before_remove = true);
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