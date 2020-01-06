<?php


namespace App\CLI\parser\buffer;


class ParserBuffer
{
    public array $additionToMap = [];
    public array $partialAliases = [];
    public ?string $cmdArg = null;
}