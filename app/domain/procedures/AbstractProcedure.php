<?php


namespace App\domain\procedures;


use App\domain\procedures\decorators\OwnerDecorator;
use App\domain\procedures\interfaces\NameStateInterface;
use App\objectPrinter\TPrintingObject;
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
 *      "proc" = "App\domain\procedures\CasualProcedure",
 *      "composite" = "App\domain\procedures\CompositeProcedure",
 *      "proxy" = "App\domain\procedures\proxy\UpdateProcedureProxy"
 * })
 */
abstract class AbstractProcedure implements NameStateInterface
{
    use TDatabase, TPrintingObject;

    /**
     * @Id
     * @GeneratedValue(strategy="UUID")
     * @Column(type="string")
     */
    protected string $id;

    /**
     * @OneToOne(targetEntity="App\domain\procedures\decorators\OwnerDecorator", mappedBy="subject")
     */
    protected OwnerDecorator $ownerStrategy;

    public function getOwnerStrategy(): OwnerDecorator
    {
        return $this->ownerStrategy;
    }

    public function getFacade()
    {
        return $this->getOwnerStrategy();
    }

    public function getId()
    {
        return $this->id;
    }




}