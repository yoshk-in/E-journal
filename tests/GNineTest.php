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
        $this->prepareProductLife();
    }


    public function testProcedureSnapshots()
    {
        $procs = $this->gnine->getProcCollection();
        $nastroy = $procs[0];
        $tt = $procs[1];
        $OTK = $procs[2];
        $PZ = $procs[3];
        $this->assertSame($nastroy->getName(), 'nastroy');
        $this->assertSame($tt->getName(), 'technicalTraining');
        $this->assertSame($OTK->getName(), 'electrikaOTK');
        $this->assertSame($PZ->getName(), 'electrikaPZ');
        $snapshot = $this->snapshot;
        foreach ($procs as $index => $proc) {
            $this->assertSame($proc->getStart(), $snapshot['after']['start'][$proc->getName()]->getProcCollection()[$index]->getStart());
            $this->assertSame($proc->getEnd(), $snapshot['after']['end'][$proc->getName()]->getProcCollection()[$index]->getEnd());
        }
        $this->goAssertNullThroughSnapShot($snapshot['after']['init']);
        $this->goAssertNullThroughSnapShot($snapshot['after']['start']['nastroy'], array('nastroy' => 'exceptStart'));
        $this->goAssertNullThroughSnapShot($snapshot['before']['end']['nastroy'], array('nastroy' => 'exceptStart'));
        $this->goAssertNullThroughSnapShot($snapshot['after']['end']['nastroy'], array('nastroy' => 'exceptAll'));
        $this->goAssertNullThroughSnapShot($snapshot['after']['start']['technicalTraining'], array('nastroy' => 'exceptAll', 'technicalTraining' => 'exceptStart'));
        $this->goAssertNullThroughSnapShot($snapshot['before']['end']['technicalTraining'], array('nastroy' => 'exceptAll', 'technicalTraining' => 'exceptStart'));
        $this->goAssertNullThroughSnapShot($snapshot['after']['end']['technicalTraining'], array('nastroy' => 'exceptAll', 'technicalTraining' => 'exceptAll'));
        $this->goAssertNullThroughSnapShot($snapshot['after']['start']['electrikaOTK'], array('nastroy' => 'exceptAll', 'technicalTraining' => 'exceptAll', 'electrikaOTK' => 'exceptStart'));
        $this->goAssertNullThroughSnapShot($snapshot['before']['end']['electrikaOTK'], array('nastroy' => 'exceptAll', 'technicalTraining' => 'exceptAll', 'electrikaOTK' => 'exceptStart'));
        $this->goAssertNullThroughSnapShot($snapshot['after']['end']['electrikaOTK'], array('nastroy' => 'exceptAll', 'technicalTraining' => 'exceptAll', 'electrikaOTK' => 'exceptAll'));
        $this->goAssertNullThroughSnapShot($snapshot['after']['start']['electrikaPZ'], array('nastroy' => 'exceptAll', 'technicalTraining' => 'exceptAll', 'electrikaOTK' => 'exceptAll', 'electrikaPZ' => 'exceptStart'));
        $this->goAssertNullThroughSnapShot($snapshot['before']['end']['electrikaPZ'], array('nastroy' => 'exceptAll', 'technicalTraining' => 'exceptAll', 'electrikaOTK' => 'exceptAll', 'electrikaPZ' => 'exceptStart'));
        $this->goAssertNullThroughSnapShot($snapshot['after']['end']['electrikaPZ'], array('nastroy' => 'exceptAll', 'technicalTraining' => 'exceptAll', 'electrikaOTK' => 'exceptAll', 'electrikaPZ' => 'exceptAll'));
    }

    public function afterStartsProvider()
    {
        $this->prepareProductLife();
        $result = [];
        foreach ($this->snapshot['after']['start'] as $shot) {
            $result[][] = $shot;
        }
        return $result;
    }

    /** @dataProvider afterStartsProvider */
    public function testExceptionDoubleStart(GNine $gnine)
    {
        $this->expectException(IncorrectInputException::class);
        $gnine->startProcedure();
    }

    public function afterEndsProvider()
    {
        $this->prepareProductLife();
        $result = [];
        $index = 0;
        foreach ($this->snapshot['after']['end'] as $stage => $shot) {
            $result[$index]['for_double_end_test'] = $shot;
            if ($stage !== 'electrikaPZ') {
                $result[$index]['for_early_end_test'] = $shot;
            } else {
                $result[$index]['for_early_end_test'] = $this->snapshot['after']['init'];
            }
            ++$index;
        }

        return $result;
    }

    /** @dataProvider afterEndsProvider */
    public function testExceptionDoubleEnd(GNine $gnine)
    {
        $this->expectException(IncorrectInputException::class);
        $gnine->endProcedure();
    }

    public function beforeEndProvider()
    {
        $this->prepareProductLife();
        $result = [];
        foreach ($this->snapshot['before']['end'] as $shot) {
            $result[][] = $shot;
        }
        return $result;
    }

    /** @dataProvider beforeEndProvider */
    public function testDoubleStartAfterPauseProduct($gnine)
    {
        $this->expectException(IncorrectInputException::class);
        $gnine->startProcedure();
    }

    /** @dataProvider afterEndsProvider */
    public function testEarlyEndException($miss, GNine $gnine)
    {
        $gnine->startProcedure();
        $this->expectException(IncorrectInputException::class);
        $gnine->endProcedure();
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

    private function snapShot(string $name1, string $name2 = null, ?string $name3 = null, ?string $name4 = null)
    {
        $clone = clone $this->gnine;
        if (!is_null($name4)) $this->snapshot[$name1][$name2][$name3][$name4] = $clone;
        else if (!is_null($name3)) $this->snapshot[$name1][$name2][$name3] = $clone;
        else if (!is_null($name2)) $this->snapshot[$name1][$name2] = $clone;
    }

    private function prepareProductLife()
    {
        $this->gnine = new MockGNine();
        $this->snapshot = [];
        $this->gnine->initByNumber(120051);
        $this->snapShot('after', 'init');  //0
        $this->gnine->startProcedure();
        $this->snapShot('after', 'start', 'nastroy');  //1 start nastroy
        sleep(1);
        $this->snapShot('before', 'end', 'nastroy');  //2
        $this->gnine->endProcedure();
        $this->snapShot('after', 'end', 'nastroy');  //3  end nastroy
        $this->gnine->startProcedure();
        $this->snapShot('after', 'start', 'technicalTraining');  //4   start TT
        $this->gnine->startTTProcedure('vibro');
        $this->snapShot('after', 'start', 'technicalTraining');  //5
        sleep(1);
        $this->snapShot('after', 'start', 'technicalTraining');  //6
        $this->gnine->startTTProcedure('progon');
        $this->snapShot('after', 'start', 'technicalTraining');  //7
        sleep(1);
        $this->snapShot('after', 'start', 'technicalTraining');  //8
        $this->gnine->startTTProcedure('moroz');
        $this->snapShot('after', 'start', 'technicalTraining');  //9
        sleep(2);
        $this->snapShot('after', 'start', 'technicalTraining');  //10
        $this->gnine->startTTProcedure('jara');
        $this->snapShot('after', 'start', 'technicalTraining');  //11
        sleep(1);
        $this->snapShot('before', 'end', 'technicalTraining');
        $this->gnine->endProcedure();
        $this->snapShot('after', 'end', 'technicalTraining');  //12  end TT
        $this->snapShot('after', 'end', 'doubling');  //13
        $this->gnine->startProcedure();
        $this->snapShot('after', 'start', 'electrikaOTK');  //14  start OTK
        sleep(1);
        $this->snapShot('before', 'end', 'electrikaOTK');  //15
        $this->gnine->endProcedure();
        $this->snapShot('after', 'end', 'electrikaOTK');  //16  endOTK
        $this->gnine->startProcedure();
        $this->snapShot('after', 'start', 'electrikaPZ'); //17  start PZ
        sleep(1);
        $this->snapShot('before', 'end', 'electrikaPZ'); //18
        $this->gnine->endProcedure();
        $this->snapShot('after', 'end', 'electrikaPZ');  //19 endPz
    }

}