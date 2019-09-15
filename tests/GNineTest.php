<?php

namespace tests;

use App\base\exceptions\WrongInputException;
use App\base\exceptions\WrongModelException;
use App\domain\GNine;
use PHPUnit\Framework\TestCase;

class GNineTest extends TestCase
{
    protected $gnine;
    protected $snapshot;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->shortTest = 0;
    }

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

    public function testEarlyStartTTProgon()
    {
        $gnine = $this->snapshot['after']['start']['technicalTraining'];
        $gnine->startTTProcedure('vibro');
        $this->expectException(WrongInputException::class);
        $gnine->startTTProcedure('progon');
    }

    public function ttAfterStartProvider()
    {
        $this->prepareProductLife();
        $index = 0;
        $stageNames = [];
        foreach ($this->snapshot['tt']['after']['start'] as $stage => $shot) {
            $stageNames[] = $stage;
            foreach ($stageNames as $key => $stageName) {
                $result[$index][] = $shot;
                $result[$index][] = $stageName;
                ++$index;
            }
        }
    }

    public function ttAfterEndsWithCompletedProcNamesProvider()
    {
        $this->prepareProductLife();
        $index = 0;
        $stageNames = [];
        foreach ($this->snapshot['tt']['after']['end'] as $stage => $shot) {
            $stageNames[] = $stage;
            foreach ($stageNames as $key => $stageName) {
                $result[$index][] = $shot;
                $result[$index][] = $stageName;
                ++$index;
            }
        }
        return $result;
    }

    public function ttAfterEndsProvider()
    {
        $this->prepareProductLife();
        foreach ($this->snapshot['tt']['after']['ends'] as $shot) {
            $result[][] = $shot;
        }
        return $result;
    }

    /** @dataProvider ttAfterStartProvider */
    /** @dataProvider ttAfterEndsWithCompletedProcNamesProvider */
    public function testDoublingStartTT(GNine $gnine, $stageName)
    {
        $this->expectException(WrongInputException::class);
        $gnine->startTTProcedure($stageName);
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
        $this->expectException(WrongInputException::class);
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
        $this->expectException(WrongInputException::class);
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
        $this->expectException(WrongInputException::class);
        $gnine->startProcedure();
    }

    /** @dataProvider afterEndsProvider */
    public function testEarlyEndException($miss, GNine $gnine)
    {
        $gnine->startProcedure();
        $this->expectException(WrongInputException::class);
        $gnine->endProcedure();
    }

    public function testEarlyTTEndException()
    {
        $this->prepareProductLife();
        $gnineAfterTT = $this->snapshot['after']['start']['technicalTraining'];
        $this->sleep();
        $this->expectException(WrongInputException::class);
        $gnineAfterTT->endProcedure();

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


    private function assertAllNullExceptNames(array $names, \App\domain\G9Procedure $procedure)
    {
        if (array_key_exists($procedure->getName(), $names)) {
            $this->assertNullExceptStartOrEnd($procedure, $names[$procedure->getName()]);
        } else {
            $this->assertNullExceptStartOrEnd($procedure, 'all');
        }

    }

    private function assertNullExceptStartOrEnd(\App\domain\G9Procedure $procedure, string $exceptStartOrEnd)
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

    private function snapShot(string $name1, string $name2 = null, ?string $name3 = null, ?string $name4 = null, ?string $name5 = null, ?string $name6 = null)
    {
        $clone = clone $this->gnine;
        if (!is_null($name6)) $this->snapshot[$name1][$name2][$name3][$name4][$name5][$name6];
        else if (!is_null($name5)) $this->snapshot[$name1][$name2][$name3][$name4][$name5];
        else if (!is_null($name4)) $this->snapshot[$name1][$name2][$name3][$name4] = $clone;
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
        $this->sleep();
        $this->snapShot('before', 'end', 'nastroy');  //2
        $this->gnine->endProcedure();
        $this->snapShot('after', 'end', 'nastroy');  //3  end nastroy
        $this->gnine->startProcedure();
        $this->snapShot('after', 'start', 'technicalTraining');  //4   start TT
        $this->gnine->startTTProcedure('vibro');
        $this->snapShot('tt', 'after', 'start', 'vibro');  //5
        $this->sleep();
        $this->snapShot('tt', 'after', 'end', 'vibro');  //6
        $this->gnine->startTTProcedure('progon');
        $this->snapShot('tt', 'after', 'start', 'progon');  //7
        $this->sleep();
        $this->snapShot('tt', 'after', 'end', 'progon');  //8
        $this->gnine->startTTProcedure('moroz');
        $this->snapShot('tt', 'after', 'start', 'moroz');  //9
        $this->sleep(2);
        $this->snapShot('tt', 'after', 'end', 'moroz');  //10
        $this->gnine->startTTProcedure('jara');
        $this->snapShot('tt', 'after', 'start', 'jara');  //11
        $this->sleep();
        $this->snapShot('tt', 'after', 'end', 'jara');
        $this->snapShot('before', 'end', 'technicalTraining');
        $this->gnine->endProcedure();
        $this->snapShot('after', 'end', 'technicalTraining');  //12  end TT
        $this->snapShot('after', 'end', 'doubling');  //13
        $this->gnine->startProcedure();
        $this->snapShot('after', 'start', 'electrikaOTK');  //14  start OTK
        $this->sleep();
        $this->snapShot('before', 'end', 'electrikaOTK');  //15
        $this->gnine->endProcedure();
        $this->snapShot('after', 'end', 'electrikaOTK');  //16  endOTK
        $this->gnine->startProcedure();
        $this->snapShot('after', 'start', 'electrikaPZ'); //17  start PZ
        $this->sleep();
        $this->snapShot('before', 'end', 'electrikaPZ'); //18
        $this->gnine->endProcedure();
        $this->snapShot('after', 'end', 'electrikaPZ');  //19 endPz
    }

    private function sleep(int $time = 1)
    {
        if ($this->shortTest) $time = 0;
        sleep($time);
    }

}