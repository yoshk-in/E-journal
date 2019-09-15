<?php

namespace App\console;

use App\base\AbstractRequest;
use \App\base\AppHelper;
use App\base\exceptions\AppException;
use App\base\ConsoleRequest;
use App\cache\Cache;
use App\domain\ProcedureMapManager;
use App\domain\Product;


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

    public function __construct(ConsoleRequest $request, ProcedureMapManager $procedureMap, Cache $cache)
    {
        $this->request = $request;
        $this->procedureMap = $procedureMap;
        $this->cache = $cache;
    }

    public function parseAndFillRequestWithCommands()
    {
        $params = array_pad($this->request->getConsoleArgs(), 4, null);
        [, $product_name, $command_or_numbers, $raw_numbers] = $params;
        $correct_names = $this->procedureMap->getProductNames();
        $this->validateProductName($product_name = mb_strtoupper($product_name), $correct_names);
        $this->setPartialToCommandMap($this->procedureMap->getAllDoublePartialNames($product_name));
        [$command, $numbers, $part_number, $partial_proc_command] =
            $this->parse(
                $this->commandMap,
                $this->cache->getPartNumber($product_name),
                $command_or_numbers,
                $raw_numbers
            );
        $this->request->setProductName($product_name);
        $this->request->addCommand($command);
        $this->request->setBlockNumbers($numbers);
        $this->request->setPartNumber($part_number);
        $this->request->addPartialProcName($partial_proc_command);
    }

    protected function parse(
        array $commandMap,
        ?int $cachePartNumber,
        ?string $commandOrNumbersArg,
        ?string $rawNumbers
    ): array {

        if (is_null($commandOrNumbersArg)) $command = self::DEFAULT_CMD;
        else {
            foreach ($commandMap as $pattern => $command_cfg) {

                if (preg_match($pattern, $commandOrNumbersArg)) {
                    $command = $command_cfg[self::COMMAND];
                    $next_arg_type = $command_cfg[self::NEXT_ARG] ?? null;
                    $partial = $command_cfg[self::PARTIAL_PROC] ?? null;
                    if ($pattern === self::NUMBERS[self::BLOCK_NUMBERS]) $rawNumbers = $commandOrNumbersArg;
                    break;
                }
            }
        }
        $this->ensure(isset($command), ' не соблюдён формат ввода');

        if ($next_arg_type ?? null) {
            [$numbers, $block_number] = $this->parseNumbersOrPartNumber($next_arg_type, $cachePartNumber, $rawNumbers);
        }
        return [$command, $numbers ?? null, $block_number ?? null, $partial ?? null];
    }



    protected function parseNumbersOrPartNumber(string $typeNumber, ?int $cachePartNumber, ?string $rawNumbers): array
    {
        $this->ensure((bool)$rawNumbers, ' введите ' . $typeNumber);
        $right_format = preg_match(self::NUMBERS[$typeNumber], $rawNumbers);
        $this->ensure($right_format,$typeNumber . ' введен(ы) в неверном формате');
        if ($typeNumber === self::BLOCK_NUMBERS) {
            return [$numbers = $this->getValidatedNumbers($rawNumbers, $cachePartNumber), null];
        } elseif ($typeNumber === self::PART_NUMBER) {
            return [null, $rawNumbers];
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

    private function getValidatedNumbers(?string $numbers_string, ?int $cachePartNumber = null)
    {
        $short_numbers = [];
        $number_range = [];
        $array_exploded_by_comma = explode(',', $numbers_string);
        foreach ($array_exploded_by_comma as $elem) {
            $array_exploded_by_hyphen = explode('-', $elem);

            if (count($array_exploded_by_hyphen) == 2) {
                [$first, $last] = $this->getFullNumbers($array_exploded_by_hyphen, $cachePartNumber);
                var_dump($first, $last);
                $this->ensure($first < $last, 'диапазон номеров должен задаваться по возврастающей');
                $number_range[] = range($first, $last);
            } else {
                $short_numbers = array_merge($short_numbers, $array_exploded_by_hyphen);
            }
        }
        $numbers = array_merge($this->getFullNumbers($short_numbers, $cachePartNumber), ...$number_range);
        $all_number_are_unique = count($numbers) == count(array_unique($numbers));
        $this->ensure($all_number_are_unique, 'переданы повторяющиеся номера');
        sort($numbers, SORT_NUMERIC);
        return $numbers;
    }

    protected function getFullNumbers(array $numbers, ?int $cachePartNumber): array
    {
        foreach ($numbers as $number) {
            $full_numbers[] = (strlen($number) === 6) ?  $number :
                ($cachePartNumber ?? $this->ensure(
                    self::ERROR,
        'не задан номер партии - его можно сохранить единожды, ' .
        'чтобы не вводить каждый раз, командой вида "партия \'120\'""'
                    )) . $number;
        }

        return $full_numbers ?? [];
    }

    protected function ensure(bool $condition, ?string $msg = null)
    {
        if (!$condition) throw new AppException('неверно заданы параметры запроса: ' . $msg);
    }

    protected function validateProductName(?string $name, array $rightNames)
    {
        $this->ensure($name, ' укажите название блока');
        foreach ($rightNames as $right_name) {
            if (preg_match('/' . $right_name . '$/siu', $name)) return;
        }
        $this->ensure(self::ERROR, 'наименование блока задано неверно');
    }


}
