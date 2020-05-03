<?php


namespace App\controller;


use App\base\AbstractRequest;
use App\base\exceptions\WrongInputException;
use App\cache\Cache;
use App\command\CmdResolver;
use App\domain\data\AbstractProductData;
use App\domain\procedures\data\CompositeProcedureData;
use App\domain\procedures\decorators\ProductOwnerDecorator;
use App\domain\procedures\factories\PartialProcedureFactory;
use App\domain\procedures\factories\ProcedureFactory;
use App\domain\procedures\ProcedureMap;
use App\domain\procedures\proxy\UpdateProcedureProxy;
use App\domain\productManager\ProductClassManager;
use App\domain\ProductMap;
use App\events\Event;
use App\helpers\TContainerGet;
use App\repository\AfterRequestCallBuffer;
use App\repository\ProductRepository;
use Doctrine\DBAL\Driver\PDOException;
use Psr\Container\ContainerInterface;

class Controller extends AbstractController
{
    use TContainerGet;

    protected CmdResolver $commandResolver;
    protected AbstractRequest $request;


    public function __construct(CmdResolver $commandResolver, ContainerInterface $container)
    {
        $this->commandResolver = $commandResolver;
        $this->container = $container;
    }

    public function run()
    {
        $commands = $this->commandResolver->getCommand();
        try {
            foreach ($commands as $command) {
                $command->execute();
            }
        } catch (PDOException $exception) {
            throw new WrongInputException($exception->getMessage());
        }

    }

    /**
     * @param string |AbstractProductData $productData
     */
    public function productNameHasBeenSet(string $productData)
    {
        $name = $productData::getName();

        /** @var ProductClassManager $productClassMng */
        $productClassMng = $this->containerGet(ProductClassManager::class);
        $productClass = $productClassMng->getServicedProductClass($name);


        /** @var ProductMap $productMap */
        $productMap = $this->containerGet(ProductMap::class);
        $productMap->setServicedProduct($name);

        /** @var ProcedureMap $procedureMap */
        $procedureMap = $this->containerGet(ProcedureMap::class);
        $procedureMap->setServicedProduct($name);


        $productData::setNumberLengths(...$productClassMng->getProductNumberProperties());
        $productData::setNumberStrategy($productClassMng->getNumberStrategy());
        $productData::setPartNumberSource($this->containerGet(Cache::class));
        $productData::setProcedureFactory($this->containerGet(ProcedureFactory::class));
        $productData::setProductClass($productClass);

        CompositeProcedureData::setPartialFactory($this->containerGet(PartialProcedureFactory::class));
    }

    public function preDBSave(UpdateProcedureProxy $updateProcedureProxy)
    {
        AfterRequestCallBuffer::set(fn () => $updateProcedureProxy->setProxy());
    }

    public function productProcedureStart()
    {

    }

    public function productProcedureEnd(ProductOwnerDecorator $procedureDecor)
    {
        $product = $procedureDecor->getProduct();
        /** @var ProcedureMap $procedureMap */
        $procedureMap = $this->containerGet(ProcedureMap::class);
        $product->setStateName($procedureMap->getNextProductState($procedureDecor->getName()));
    }


}