<?php

namespace App\console;


use App\base\exceptions\AppException;
use App\base\ConsoleRequest;
use App\domain\ProcedureMapManager;



class ConsoleParser
{
    protected const ERROR = false;
    protected const BLOCK_NUMBERS = 'номера блоков';
    protected const PART_NUMBER = 'номер партии';
    protected const NEXT_ARG = 'next_argument';
    protected const COMMAND = 'command';
    protected const PARTIAL_PROC = 'partial_procedure';
    protected const DEFAULT_CMD = 'fullInfo';

    protected $commandMap = [
        '#^\+$#s' => [
            self::COMMAND => 'blocksAreArrived',
            self::NEXT_ARG => self::BLOCK_NUMBERS
        ],
        '#^-$#s' => [
            self::COMMAND => 'blocksAreDispatched',
            self::NEXT_ARG => self::BLOCK_NUMBERS
        ],
        '#^очистка$#isu' => [
            self::COMMAND => 'clearJournal',
            self::NEXT_ARG => null
        ],
        '#^партия$#isu' => [
            self::COMMAND => 'setPartNumber',
            self::NEXT_ARG => self::PART_NUMBER
        ],
        self::NUMBERS[self::BLOCK_NUMBERS] => [
            self::COMMAND => 'RangeInfo',
            self::NEXT_ARG => self::BLOCK_NUMBERS
        ]
    ];

    protected const NUMBERS = [
        self::BLOCK_NUMBERS => '#^(\d{3}|\d{6})(-(\d{3}|\d{6}))?(,(\d{3}|\d{6})(-(\d{3}|\d{6}))?)*$#s',
        self::PART_NUMBER => '#^\d{3}$#s'
    ];

    protected $request;
    protected $procedureMap;
    protected $cache;
    protected $numbersParser;

    protected $product;
    protected $commandOrNumbersArg;
    protected $rawNumbersArg;
    protected $command;
    protected $numbers;
    protected $partial;
    protected $partNumber;

    protected $nextArgToParse;


    public function __construct(ConsoleRequest $request, ProcedureMapManager $procedureMap, NumbersParser $numbersParser)
    {
        $this->request = $request;
        $this->procedureMap = $procedureMap;
        $this->numbersParser = $numbersParser;
    }

    public function parseAndFillRequestWithCommands()
    {
        $params = array_pad($this->request->getConsoleArgs(), 4, null);
        [, $product_name, $this->commandOrNumbersArg, $this->rawNumbersArg] = $params;
        $correct_names = $this->procedureMap->getProductNames();
        $this->product = mb_strtoupper($product_name);
        $this->checkProductName($this->product, $correct_names);
        $this->setPartialToCommandMap($this->procedureMap->getAllDoublePartialNames($this->product));
        is_null($this->commandOrNumbersArg) ? $this->command = self::DEFAULT_CMD : $this->parseCmd();
        $this->request->setProductName($this->product);
        $this->request->addCommand($this->command);
        $this->request->setBlockNumbers($this->numbers);
        $this->request->setPartNumber($this->partNumber);
        $this->request->addPartialProcName($this->partial);
    }

    protected function parseCmd()
    {
        foreach ($this->commandMap as $pattern => $command_cfg) {

            if (preg_match($pattern, $this->commandOrNumbersArg)) {
                $this->command = $command_cfg[self::COMMAND];
                $this->nextArgToParse = $command_cfg[self::NEXT_ARG] ?? null;
                $this->partial = $command_cfg[self::PARTIAL_PROC] ?? null;
                if ($pattern === self::NUMBERS[self::BLOCK_NUMBERS]) $this->rawNumbersArg = $this->commandOrNumbersArg;
                break;
            }
        }

        $this->ensure($this->command, ' не соблюдён формат ввода');

        if ($this->nextArgToParse) $this->parseNumbersOrPartNumber($this->nextArgToParse);
    }


    protected function parseNumbersOrPartNumber(string $typeNumber)
    {
        $this->ensure((bool)$this->rawNumbersArg, ' введите ' . $typeNumber);
        $right_format = preg_match(self::NUMBERS[$typeNumber], $this->rawNumbersArg);
        $this->ensure($right_format, $typeNumber . ' введен(ы) в неверном формате');
        if ($typeNumber === self::BLOCK_NUMBERS) {
            $this->numbers = $this->numbersParser->parse($this->rawNumbersArg, $this->product);
            return ;
        } elseif ($typeNumber === self::PART_NUMBER) {
            $this->partNumber = $typeNumber;
            return;
        }

        $this->ensure(self::ERROR, $typeNumber . 'введен(ы) в неверном формате');
    }

    protected function setPartialToCommandMap(array $partials): void
    {
        foreach ($partials as [$short_name, $partial]) {
            $this->commandMap["#(^$short_name$)|(^$partial$)#is"] = [
                self::COMMAND => 'blocksAreArrived',
                self::NEXT_ARG => self::BLOCK_NUMBERS,
                self::PARTIAL_PROC => $partial
            ];
        }
    }


    protected function ensure(bool $condition, ?string $msg = null)
    {
        if (!$condition) throw new AppException('неверно заданы параметры запроса: ' . $msg);
    }

    protected function checkProductName(?string $name, array $rightNames)
    {
        $this->ensure($name, ' укажите название блока');
        foreach ($rightNames as $right_name) {
            if (preg_match('/' . $right_name . '$/siu', $name)) return;
        }
        $this->ensure(self::ERROR, 'наименование блока задано неверно');
    }


}
