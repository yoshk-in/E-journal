<?php


namespace App\domain;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use App\events\{Event};


/**
 * @MappedSuperClass
 */
abstract class Procedure extends AbstractProcedure
{
    /**
     * @ManyToOne(targetEntity="Product")
     **/
    protected $owner;


    public function __construct(string $name, int $idState, Product $product, ?array $innerProcs = null)
    {
        parent::__construct($name, $idState, $product);
        if ($innerProcs) $this->innerProcs = new ArrayCollection(ProcedureFactory::createPartials($innerProcs, $this));
        else $this->innerProcs = new ArrayCollection();
    }

    public function setEnd()
    {
        $this->checkInput((bool)$this->getStart(), ' событие еще не начато');
        $this->checkInput(!$end = $this->getEnd(), 'coбытие уже отмечено');
        $this->end = new DateTimeImmutable('now');
    }



}