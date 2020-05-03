<?php


namespace App\CLI\parser;


use App\base\CLIRequest;
use App\base\exceptions\WrongInputException;
use App\CLI\parser\buffer\ParserBuffer;
use App\controller\TChainOfResponsibility;
use Closure;
use Psr\Container\ContainerInterface;

class CommandMapParser extends Parser implements CommandParseMap
{
    private ContainerInterface $appContainer;
    private Parser $endParse;
    private Parser $startParse;
    private Closure $creatParserCall;
    const WRONG_INPUT = ' не соблюдён формат ввода';

    /** REGEX PATTERNS */
    const FORMAT_PATTERN = '{^%s$}isu';
    const NUMBER = '\d{3}|\d{6}';
    const NUMBER_RANGE = '(-' . self::NUMBER . ')?';
    const SEQUENCE_NUMBER_RANGE = '(,' . self::NUMBER . self::NUMBER_RANGE . ')*';
    /* "(\d{3}|\d{6})(-(\d{3}|\d{6}))?(,(\d{3}|\d{6})(-(\d{3}|\d{6}))?)*" */
    protected array $cmdMap = [
        self::NUMBER . self::NUMBER_RANGE . self::SEQUENCE_NUMBER_RANGE => CommandParseMap::CONCRETE_PRODUCT_INFO,
        '\+|-' => CommandMapParser::MOVE_PRODUCT,
        'очистка' => CommandParseMap::CLEAR_JOURNAL,
        'партия' => CommandParseMap::SET_PART_NUMBER,
    ];


    public function __construct(ContainerInterface $appContainer, CLIRequest $request, ParserBuffer $parserBuffer)
    {
        $this->appContainer = $appContainer;
        $this->creatParserCall = fn(string $parser) => $this->appContainer->get($parser);
        $this->startParse = ($this->creatParserCall)(ProductNameParser::class);
        $this->endParse = ($this->creatParserCall)(EndParse::class);
        parent::__construct($request, $parserBuffer);
    }


    public function setRestChainParsers($cmdArg)
    {
        if (is_null($cmdArg)) {
            $foundChain = CommandParseMap::DEFAULT;
            $this->parserBuffer->cmdArg =  null;
        } else {
            foreach ($this->cmdMap as $pattern => $cmdChain) {
                $pattern_str = sprintf(self::FORMAT_PATTERN, $pattern);
                if ($x = preg_match($pattern_str, $cmdArg)) {
                    $foundChain = $cmdChain;
                    $this->parserBuffer->cmdArg = $cmdArg;
                    break;
                }
            }
        }
        if (!isset($foundChain)) throw new WrongInputException(self::WRONG_INPUT);

        $restParsers[] = $this;
        foreach ($foundChain as $key => $decorator) {
            $next = $restParsers[] = ($this->creatParserCall)($decorator);
            $restParsers[array_key_last($restParsers) - 1]->setNext($next);
        }
        $next->setNext($this->endParse);
        --self::$argN;
    }

    public function parseByChain()
    {
        $this->startParse->setNext($this);
        $this->startParse->parse();
    }


    protected function doParse()
    {
        $this->cmdMap = array_merge($this->parserBuffer->additionToMap, $this->cmdMap);
        $this->setRestChainParsers($this->getCurrentCLIArg());
    }
}