<?php


namespace App\repository;


use App\domain\data\AbstractProductData;
use App\domain\data\ProductData;
use App\domain\AbstractProduct;
use App\events\Event;
use Generator;

class ProductRepository
{

    private DBLayer $orm;
    protected RequestBuffer $requestBuffer;

    /** @var string | AfterRequestCallBuffer */
    const PRE_SAVE_OPERATOR = AfterRequestCallBuffer::class;


    const NUMBER_FIELD = 'number';
    const FINISHED_FIELD = 'ended';
    const STARTED_FIELD = 'started';
    const AD_NUMBER_FIELD = 'advancedNumber';
    const ID_FIELD = 'id';


    /** @var string | AbstractProductData */
    const REQUESTING_SUBJECT_DATA = AbstractProductData::class;


    public function __construct(DBLayer $orm, RequestBuffer $requestBuffer)
    {
        $this->orm = $orm;
        $this->requestBuffer = $requestBuffer;
        DB::setRepository($this);
    }


    public function remove($entity)
    {
        $this->orm->remove($entity);
    }


    public function createProducts(array $productData): Generator
    {
        /** @var ProductData $data */
        foreach ($productData as $data)  yield $data->createProduct();

    }

    public function findById(array $numbers): array
    {
        return $this->byId($numbers)->find();
    }

    public function byId(array $ids): self
    {
        $this->requestBuffer->where([self::ID_FIELD => $ids]);
        return $this;
    }

    public function findByMainNumbers(array $numbers): array
    {
        return $this->byMainNumber($numbers)->find();
    }

    public function findByAllNumbers(array $mains, array $advanced, ?bool $state): array
    {
        return $this->byMainNumber($mains)->byAdvancedNumber($advanced)->find();
    }

    public function byMainNumber(array $numbers): self
    {
        $this->productByNumbers(self::NUMBER_FIELD, $numbers);
        return $this;
    }

    protected function productByNumbers(string $field, array $numbers): self
    {
        $this->requestBuffer->where([$field => $numbers]);
        return $this;
    }

    public function byAdvancedNumber(array $numbers): self
    {
        return $this->productByNumbers(self::AD_NUMBER_FIELD, $numbers);
    }

    public function findByUniqueProperties(array $data)
    {
        $this->orm->setServicedEntity((self::REQUESTING_SUBJECT_DATA)::getServicedClass());
        $this->setCommonRequestingSubjectProps();
        $this->requestBuffer->where([(self::REQUESTING_SUBJECT_DATA)::findBy() => $data]);
        $this->requestBuffer->asc((self::REQUESTING_SUBJECT_DATA)::orderBy());
        $found = $this->orm->findWhere(...$this->requestBuffer->getBuffer());
        (self::REQUESTING_SUBJECT_DATA)::resetFinding();
        $this->requestBuffer->reset();
        return $found;
    }


    public function find(): array
    {
        $this->orm->setServicedEntity((self::REQUESTING_SUBJECT_DATA)::getServicedClass());
        $this->setCommonRequestingSubjectProps();
        $this->requestBuffer->asc((self::REQUESTING_SUBJECT_DATA)::orderBy());
        $found = $this->orm->findWhere(...$this->requestBuffer->getBuffer());
        (self::REQUESTING_SUBJECT_DATA)::resetFinding();
        $this->requestBuffer->reset();
        return $found;
    }


    public function setCommonRequestingSubjectProps(): self
    {
        foreach ((self::REQUESTING_SUBJECT_DATA)::getCommonRequestingProductProps() as $fieldName => $value) {
            $this->requestBuffer->where([$fieldName => $value]);
        }
        return $this;
    }


    public function findNotEndedByAdvancedNumber($advancedNumber): array
    {
        return $this->byAdvancedNumber($advancedNumber)->setCommonRequestingSubjectProps(false)->find();
    }


    public function findLast(): ?AbstractProduct
    {
        [$criteria, $order] = $this->requestBuffer->getBuffer();
        return $this->orm->findWhere($criteria, $order, $limit = 1)[0];
    }




    public function save()
    {
        (self::PRE_SAVE_OPERATOR)::drop();
        $this->orm->save();
    }


    public function persist($entity)
    {

        $this->orm->persist($entity);
    }

    public function findAllByMainNumber(): array
    {
        return $this->find();
    }




}