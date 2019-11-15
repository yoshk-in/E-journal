<?php


namespace App\GUI\handlers;


use App\CLI\render\ProductStat;
use App\GUI\components\LabelWrapper;
use App\GUI\domainBridge\Store;
use App\GUI\factories\LabelFactory;

class GuiStat
{
    private $store;
    private $stat;
    private $output;
    private $lFactory;
    private $title = "текущая статистика: \n";

    public function __construct(Store $store, ProductStat $stat, LabelFactory $lFactory)
    {
        $this->store = $store;
        $this->stat = $stat;
        $this->lFactory = $lFactory;
    }

    public function attachOutput(LabelWrapper $object)
    {
        $this->output = $object;
        $this->updateStat();
    }

    public function updateStat()
    {
        $this->output->setText($this->title . $this->stat->doStat($this->store->getStartedProducts()));
    }
}