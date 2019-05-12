<?php


namespace tests;

use PHPUnit\Framework\TestCase;
use App\domain\G9;

class G9Test extends TestCase
{
    public $g9;

    public function setUp(): void
    {
        $this->g9 = new G9('120051');
    }

    public function testProcedure()
    {
        $g9 = $this->g9->nextProcedure();
        if ($g9->getProcedureStart('nastroy') instanceof \DateTime) return true;
    }
}