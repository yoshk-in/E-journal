<?php


namespace App\CLI\parser;


use App\base\CLIRequest;
use App\base\exceptions\WrongInputException;
use App\CLI\parser\buffer\ParserBuffer;
use App\controller\TChainOfResponsibility;
use Psr\Container\ContainerInterface;

class CommandMapParser extends Parser implements CommandParseMap
{
    use TChainOfResponsibility;

    private ContainerInterface $appContainer;
    const WRONG_INPUT = ' не соблюдён формат ввода';
    private Parser $endParse;
    private Parser $startParse;
    private \Closure $creatParserCall;
    protected array $cmdMap = [
        '(\d{3}|\d{6})(-(\d{3}|\d{6}))?(,(\d{3}|\d{6})(-(\d{3}|\d{6}))?)*'
        => CommandParseMap::BY_PRODUCT_NUMB_CMD,
        '+|-' => CommandMapParser::MOVE_PRODUCT,
        'очистка' => CommandParseMap::CLEAR_JOURNAL,
        'партия' => CommandParseMap::PARTY,
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
                if (preg_match('#^' . $pattern . '$#su', $cmdArg)) {
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
            $restParsers[array_key_last($restParsers) - 1]->setNextHandler($next);
        }
        $next->setNextHandler($this->endParse);
        --self::$argN;
    }

    public function parseByChain()
    {
        $this->startParse->setNextHandler($this);
        $this->startParse->parse();
    }


    protected function doParse()
    {
        $this->cmdMap = array_merge($this->parserBuffer->additionToMap, $this->cmdMap);
        $this->setRestChainParsers($this->getCurrentCLIArg());
    }
}