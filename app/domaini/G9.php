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

    protected static $CLIMATIC_RELAX = [
        'relax' => 'PT2H'
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
        // TODO: Implement compositeProcedureIsFinished() method.
        $errMsg = '- нет отмечаны частично или полностью входящие в данную процедуры испытания';
        $this->ensure($collection->count() === count($arrayOfComposite), $errMsg);
        foreach ($collection as $elem) $this->ensure(!is_null($elem->getEnd()), $errMsg);

    }

    public function startTTProcedure(string $name)
    {
        $this->ensure(
            $this->isCompositeProcedure($this->currentProcedure->getName()),
            'it is must be compositeProcedure'
        );
        $this->ensure(array_search($name, array_keys(self::$TECHNICAL_PROCEDURES_REGULATIONS)), 'wrong name');
        $now = new \DateTime('now');
        foreach ($this->TTCollection as $elem) {
            if ($elem->getName() === $name)
                $this->ensure(is_null($elem->getStart()), ' - данная процедура уже отмечена');
        }
        $currentTTproc = $this->currentTTProcedure;
        if (!is_null($currentTTproc))
            $this->ensure($now < $currentTTproc->getEnd(), ' - предыдущая процедура еще не завершена');
        if (in_array($name, self::$CLIMATIC_TESTS)) {
            $climatics = self::$CLIMATIC_TESTS;
            $prevTest = array_filter($climatics, function ($climatic) use ($name) {
                if ($climatic === $name) return false;
                return true;
            });
            $now = new \DateTime('now');
            $relaxPeriod = new \DateInterval(self::$CLIMATIC_RELAX[0]);
            $requiredEnd = $now->add($relaxPeriod);
            $end = $this->TTCollection[$prevTest[0]]->getEnd();
            if (!is_null($end))
                $this->ensure($end < $requiredEnd, '- не соблюдается перерыв между жарой и морозом');
        }
        $elem->setInterval(self::$TECHNICAL_PROCEDURES_REGULATIONS[$name]);
        $elem->setStart(self::$TECHNICAL_PROCEDURES_REGULATIONS[$name]);
    }





}




