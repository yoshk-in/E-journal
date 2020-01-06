<?php


namespace App\domain;

use App\base\AppMsg;
use App\controller\TChainOfResponsibility;
use App\domain\traits\IProcedureOwner;
use App\domain\traits\TChangeNameAfterEndProcedure;
use App\domain\traits\TManualEndingProcedure;
use DateTimeImmutable;

/**
 * @Entity
 *
 * @InheritanceType("SINGLE_TABLE")
 * @DiscriminatorColumn(name="class", type="string")
 * @DiscriminatorMap({"casual" = "CasualProcedure", "composite" = "CompositeProcedure"})
 *
 */
class CasualProcedure extends AbstractProcedure
{
    use TManualEndingProcedure, TChangeNameAfterEndProcedure {TManualEndingProcedure::end as manual_end;}
    /**
     * @ManyToOne(targetEntity="Product")
     **/
    protected IProcedureOwner $owner;


    public function end(): AbstractProcedure
    {
        $this->manual_end();
        $this->name = $this->nameAfterEnd;
        return $this;
    }

    protected function concreteProcStart(?string $partial = null)
    {
        return $this;
    }
}