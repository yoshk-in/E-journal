<?php

namespace App\domaini;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

require_once 'vendor/autoload.php';


/** @Entity @Table(name="g9s") * */
class G9 extends DomainObject
{
    protected static $PROCEDURES = [
        'nastroy',
        'technicalTraining',
        'electrikaOTK',
        'electrikaPZ'
    ];
    protected static $TECHNICAL_PROCEDURES_REGULATIONS = [
        'vibro' => 'PT30M',
        'progon' => 'PT2H',
        'moroz' => 'PT2H',
        'jara' => 'PT2H'
    ];

    protected static $RELAX_PROCEDURE = [
        'climatic_relax' => 'PT2H'
    ];

    protected static $PROCEDURES_REGULATIONS = [
        'minProcTime' => 'PT30M'
    ];

    protected $currentTTProcedure;
    protected $currentTTProcedureId;

    protected static $CLIMATIC_TESTS = [
        'moroz',
        'jara'
    ];


    public function __construct()
    {
        $this->compositeProcedures = array('technicalTraining');
        $this->ensure(array_search($this->compositeProcedures, self::$PROCEDURES) !== false,
            "{$this->compositeProcedures} must be equals 'technicalTraining'");
        $this->ensure(!is_null(self::$CLIMATIC_TESTS), 'climatics tests are required');
        foreach (self::$CLIMATIC_TESTS as $climatic)
            $this->ensure(in_array($climatic, array_keys(self::$TECHNICAL_PROCEDURES_REGULATIONS)), 'wrong name climatic');
        parent::__construct();
    }

    protected function compositeProcedureIsFinished(Collection $collection, array $arrayOfComposite)
    {
        $errMsg = '- нет отмечены частично или полностью входящие в данную процедуры испытания';
        $this->ensure($collection->count() === count($arrayOfComposite), $errMsg);
        foreach ($collection as $elem) $this->ensure(!is_null($elem->getEnd()), $errMsg);
    }

    public function startTTProcedure(string $name)
    {
        $this->checkNewTTProcedure($name);
        $nextProcedure = $this->getNewProcedureFromCollection($name, $this->TTCollection);

        if (in_array($name, self::$CLIMATIC_TESTS)) {
            $this->checkClimaticProcedure($name);
        }
        $nextProcedure->setInterval(self::$TECHNICAL_PROCEDURES_REGULATIONS[$name]);
        $nextProcedure->setStart(self::$TECHNICAL_PROCEDURES_REGULATIONS[$name]);
        $this->currentTTProcedure = $nextProcedure;
        $this->currentTTProcedureId = $this->currentTTProcedure->getIdStage();
    }

    protected function getPrevClimaticTest(string $nextTest): string
    {
        $climaticTests = self::$CLIMATIC_TESTS;
        $prevClimaticTestArray = array_filter($climaticTests, function ($climatic) use ($nextTest) {
            if ($climatic === $nextTest) return false;
            return true;
        });
        return $prevClimaticTestArray[0];
    }

    protected function getNewProcedureFromCollection(string $procedureName, Collection $procedureCollection) : Procedure
    {
        foreach ($procedureCollection as $elem) {
            if ($elem->getName() === $procedureName)
                $this->ensure(is_null($elem->getStart()), ' - данная процедура уже отмечена');
            return $elem;
        }
    }

    protected function checkNewTTProcedure(string $procedureName): void
    {
        $this->ensure($this->isCompositeProcedure($procedureName), 'it is must be compositeProcedure');
        $this->ensure(array_search($procedureName, array_keys(self::$TECHNICAL_PROCEDURES_REGULATIONS)), 'wrong name');
        $now = new \DateTime('now');
        $currentTTproc = $this->currentTTProcedure;
        if (!is_null($currentTTproc))
            $this->ensure($now < $currentTTproc->getEnd(), ' - предыдущая процедура еще не завершена');
    }

    protected function checkClimaticProcedure(Procedure $procedure)
    {
        $prevClimaticTest = $this->getPrevClimaticTest($procedure->getName());
        $relaxPeriod = new \DateInterval(self::$RELAX_PROCEDURE['climatic_relax']);
        $relaxEnd = (clone $this->TTCollection[$prevClimaticTest]->getEnd())->add($relaxPeriod);
        $now = new \DateTime('now');
        $this->ensure($now < $relaxEnd, '- не соблюдается перерыв между жарой и морозом');
    }

}

