<?php


namespace App\domain\procedures\decorators;


use App\domain\procedures\CasualProcedure;
use App\domain\procedures\data\AbstractProcedureData;
use App\domain\procedures\interfaces\ProcedureInterface;
use App\domain\procedures\traits\IProcedureOwner;
use App\domain\procedures\traits\TProcedureProxy;
use App\events\Event;
use App\events\traits\TObservable;
use App\repository\DB;
use App\repository\traits\TDatabase;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\DiscriminatorColumn;
use Doctrine\ORM\Mapping\DiscriminatorMap;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\InheritanceType;
use Doctrine\ORM\Mapping\OneToOne;

/**
 * @Entity()
 * @InheritanceType("SINGLE_TABLE")
 * @DiscriminatorColumn(name="class", type="string")
 * @DiscriminatorMap({
 *     "proc" = "App\domain\procedures\decorators\ProcedureOwnerDecorator",
 *     "product" = "App\domain\procedures\decorators\ProductOwnerDecorator",
 * })
 */
abstract class OwnerDecorator implements ProcedureInterface
{
    use TDatabase, TProcedureProxy, TObservable;

    protected IProcedureOwner $owner;
    protected ?IProcedureOwner $futureOwner;
    protected ?IProcedureOwner $pastOwner = null;

    /**
     * @OneToOne(targetEntity="App\domain\procedures\AbstractProcedure", inversedBy="ownerStrategy")
     */
    protected ProcedureInterface $subject;

    /** @Column(type="string", nullable=false) */
    protected string $name;

    /**
     * @Column(type="integer", nullable=false)
     */
    protected int $ownerOrder;

    /**
     * @Id()
     * @GeneratedValue()
     * @Column(type="integer")
     */
    protected int $id;


    /** @var callable[] */
    const HANDLING_MAP = [
        CasualProcedure::READY_TO_START => 'analyze',
        CasualProcedure::READY_TO_START_INNER => 'startHandle',
        CasualProcedure::READY_TO_END_INNER => 'analyze',
        CasualProcedure::READY_TO_END => 'startHandle',
        CasualProcedure::ENDED => 'endHandle'
    ];


    public function __construct(IProcedureOwner $owner, AbstractProcedureData $data)
    {
        $this->owner = $owner;
        $this->futureOwner = $owner;
        [$this->subject, $this->name, $this->ownerOrder] = $data->getOwnerStrategyData();
        $this->persist();
    }


    public function __call($name, $arguments)
    {
        return $this->subject->$name(...$arguments);
    }

    public function getOwnerStrategy(): OwnerDecorator
    {
        return $this;
    }

    public function ownerHandling()
    {
        $handling_method = self::HANDLING_MAP[$this->subject->getState()];
        $this->$handling_method();
        $this->owner->procedureOwnerHandling($this);
    }



    public function getName(): string
    {
        return $this->name;
    }

    public function getOwnerOrder(): int
    {
        return $this->ownerOrder;
    }

    public static function create(IProcedureOwner $owner, AbstractProcedureData $data)
    {
        return new static($owner, $data);
    }



    public function setNewSubject(ProcedureInterface $procedure)
    {
        $this->subject = $procedure;
        $this->persist();
    }

    public function getOwner(): IProcedureOwner
    {
        return $this->owner;
    }



    /**
     * @callable from HANDLING_MAP
     */
    protected function startHandle()
    {
        $this->futureOwner = null;
        $this->event(Event::START);

    }

    /**
     * @callable from HANDLING_MAP
     */
    protected function endHandle()
    {
        $this->pastOwner = $this->owner;
        $this->event(Event::END);
    }


}