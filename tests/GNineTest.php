<?php

namespace tests;

use App\base\exceptions\IncorrectInputException;
use App\base\exceptions\WrongModelException;
use App\domaini\GNine;
use PHPUnit\Framework\TestCase;

class GNineTest extends TestCase
{
    protected $gnine;
    protected $snapshot;

    public function setUp(): void
    {

        $this->gnine = new MockGNine();
        $this->snapshot = [];

    }

    public function testExceptionWithoutInit()
    {
        $this->expectException(WrongModelException::class);
        $this->gnine->startProcedure();
    }

    private function snapShot()
    {
        $this->snapshot[] = clone $this->gnine;
    }

    public function testProductLife()
    {
        $this->gnine->initByNumber(120051);
        $this->snapShot();  //0
        $this->gnine->startProcedure();
        $this->snapShot();  //1 start nastroy
        sleep(1);
        $this->snapShot();  //2
        $this->gnine->endProcedure();
        $this->snapShot();  //3  end nastroy
        $this->gnine->startProcedure();
        $this->snapShot();  //4   start TT
        $this->gnine->startTTProcedure('vibro');
        $this->snapShot();  //5
        sleep(1);
        $this->snapShot();  //6
        $this->gnine->startTTProcedure('progon');
        $this->snapShot();  //7
        sleep(2);
        $this->snapShot();  //8
        $this->gnine->startTTProcedure('moroz');
        $this->snapShot();  //9
        sleep(2);
        $this->snapShot();  //10
        $this->gnine->startTTProcedure('jara');
        $this->snapShot();  //11
        sleep(2);
        $this->gnine->endProcedure();
        $this->snapShot();  //12  end TT
        sleep(1);
        $this->snapShot();  //13
        $this->gnine->startProcedure();
        $this->snapShot();  //14  start OTK
        sleep(1);
        $this->snapShot();  //15
        $this->gnine->endProcedure();
        $this->snapShot();  //16  endOTK
        $this->gnine->startProcedure();
        $this->snapShot(); //17  start PZ
        sleep(1);
        $this->snapShot(); //18
        $this->gnine->endProcedure();
        $this->snapShot();  //19 endPz
        $this->assertSame(true, $this->gnine instanceof GNine);
        return [
            'snapshot' => $this->snapshot,
            'gnine' => $this->gnine
        ];
    }

    /** @depends  testProductLife */
    public function testProcedureSnapshots(array $stack)
    {
        $procs = $stack['gnine']->getProcCollection();
        $nastroy = $procs[0];
        $tt = $procs[1];
        $OTK = $procs[2];
        $PZ = $procs[3];
        $snapshot = $stack['snapshot'];
        $this->assertSame($nastroy->getName(), 'nastroy');
        $this->assertSame($tt->getName(), 'technicalTraining');
        $this->assertSame($OTK->getName(), 'electrikaOTK');
        $this->assertSame($PZ->getName(), 'electrikaPZ');
        $this->goAssertNullThroughSnapShot($snapshot[0]);
        $this->assertSame($nastroy->getStart(), $snapshot[1]->getProcCollection()[0]->getStart());
        $this->goAssertNullThroughSnapShot($snapshot[1], array('nastroy' => 'exceptStart'));
        $this->goAssertNullThroughSnapShot($snapshot[2], array('nastroy' => 'exceptStart'));
        $this->goAssertNullThroughSnapShot($snapshot[3], array('nastroy' => 'exceptAll'));
        $this->assertSame($tt->getStart(), $snapshot[4]->getProcCollection()[1]->getStart());
        $this->goAssertNullThroughSnapShot($snapshot[4], array('nastroy' => 'exceptAll', 'technicalTraining' => 'exceptStart'));
        $this->goAssertNullThroughSnapShot($snapshot[5], array('nastroy' => 'exceptAll', 'technicalTraining' => 'exceptStart'));
        $this->goAssertNullThroughSnapShot($snapshot[6], array('nastroy' => 'exceptAll', 'technicalTraining' => 'exceptStart'));
        $this->goAssertNullThroughSnapShot($snapshot[7], array('nastroy' => 'exceptAll', 'technicalTraining' => 'exceptStart'));
        $this->goAssertNullThroughSnapShot($snapshot[8], array('nastroy' => 'exceptAll', 'technicalTraining' => 'exceptStart'));
        $this->goAssertNullThroughSnapShot($snapshot[9], array('nastroy' => 'exceptAll', 'technicalTraining' => 'exceptStart'));
        $this->goAssertNullThroughSnapShot($snapshot[10], array('nastroy' => 'exceptAll', 'technicalTraining' => 'exceptStart'));
        $this->goAssertNullThroughSnapShot($snapshot[11], array('nastroy' => 'exceptAll', 'technicalTraining' => 'exceptStart'));
        $this->goAssertNullThroughSnapShot($snapshot[12], array('nastroy' => 'exceptAll', 'technicalTraining' => 'exceptAll'));
        $this->goAssertNullThroughSnapShot($snapshot[13], array('nastroy' => 'exceptAll', 'technicalTraining' => 'exceptAll'));
        $this->goAssertNullThroughSnapShot($snapshot[14], array('nastroy' => 'exceptAll', 'technicalTraining' => 'exceptAll', 'electrikaOTK' => 'exceptStart'));
        $this->goAssertNullThroughSnapShot($snapshot[15], array('nastroy' => 'exceptAll', 'technicalTraining' => 'exceptAll', 'electrikaOTK' => 'exceptStart'));
        $this->goAssertNullThroughSnapShot($snapshot[16], array('nastroy' => 'exceptAll', 'technicalTraining' => 'exceptAll', 'electrikaOTK' => 'exceptAll'));
        $this->goAssertNullThroughSnapShot($snapshot[17], array('nastroy' => 'exceptAll', 'technicalTraining' => 'exceptAll', 'electrikaOTK' => 'exceptAll', 'electrikaPZ' => 'exceptStart'));
        $this->goAssertNullThroughSnapShot($snapshot[18], array('nastroy' => 'exceptAll', 'technicalTraining' => 'exceptAll', 'electrikaOTK' => 'exceptAll', 'electrikaPZ' => 'exceptStart'));
        $this->goAssertNullThroughSnapShot($snapshot[17], array('nastroy' => 'exceptAll', 'technicalTraining' => 'exceptAll', 'electrikaOTK' => 'exceptAll', 'electrikaPZ' => 'exceptAll'));
        $this->assertSame($OTK->getStart(), $snapshot[14]->getProcCollection()[2]->getStart());
        $this->assertSame($PZ->getStart(), $snapshot[17]->getProcCollection()[3]->getStart());
        $this->assertSame($nastroy->getEnd(), $snapshot[3]->getProcCollection()[0]->getEnd());
        $this->assertSame($tt->getEnd(), $snapshot[12]->getProcCollection()[1]->getEnd());
        $this->assertSame($OTK->getEnd(), $snapshot[16]->getProcCollection()[2]->getEnd());
        $this->assertSame($PZ->getEnd(), $snapshot[19]->getProcCollection()[3]->getEnd());
    }

    private function goAssertNullThroughSnapShot($snapshot, ?array $exceptProcName = null)
    {
        foreach ($snapshot->getProcCollection() as $procedure) {
            if (is_null($exceptProcName)) {
                $this->assertNullExceptStartOrEnd($procedure, 'all');
            } else {
                $this->assertAllNullExceptNames($exceptProcName, $procedure);
            }
        }
    }

    private function assertAllNullExceptNames(array $names, \App\domaini\Procedure $procedure)
    {
        if (array_key_exists($procedure->getName(), $names)) {
            $this->assertNullExceptStartOrEnd($procedure, $names[$procedure->getName()]);
        } else {
            $this->assertNullExceptStartOrEnd($procedure, 'all');
        }

    }

    private function assertNullExceptStartOrEnd(\App\domaini\Procedure $procedure, string $exceptStartOrEnd)
    {
        switch ($exceptStartOrEnd) {
            case  'exceptStart' :
                $this->assertSame(null, $procedure->getEnd());
                break;
            case 'all':
                $this->assertSame(null, $procedure->getStart());
                $this->assertSame(null, $procedure->getEnd());
                break;
            case 'exceptAll' :
                break;

        }

    }

}