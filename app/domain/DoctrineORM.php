<?php


namespace App\domain;


use App\base\exceptions\AppException;
use data\DatabaseConf;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\Setup;

trait DoctrineORM
{
    private $doctrineCriteria;
    private $entityManager;
    private $doctrineRepository;

    public function __construct(string $domainClass, bool $devMode)
    {
        $this->entityManager = $this->_getEntityManager($devMode);
        $this->doctrineRepository = $this->entityManager->getRepository($domainClass);
        $this->doctrineCriteria = Criteria::class;
    }

    protected function findConcreteProducts(Criteria $prevCriteria, string $idFieldName, array $numbers): array
    {
        foreach ($numbers as $number) {
            $prevCriteria->andWhere(Criteria::create()->expr()->eq($idFieldName, $number));
        }
        $collection = $this->getDoctrineRepository()->matching($prevCriteria);
        $not_found = array_filter($numbers, function ($number) use ($collection) {
            return !$collection->exists(function ($key) use ($number, $collection) {
                return $collection[$key]->getNumber() == $number;
            });
        });
        return [$collection, $not_found];
    }

    protected function findNotFinishedProducts(
        Criteria $fistCriteria,
        string $fieldName,
        string $fieldValue
    ): Collection
    {
        $criteria = $fistCriteria->orWhere(Criteria::create()->expr()->lt($fieldName, $fieldValue));
        return $this->getDoctrineRepository()->matching($criteria);
    }

    public function save()
    {
        $this->getEntityManager()->flush();
    }

    protected function persist(object $object)
    {
        $this->getEntityManager()->persist($object);
    }

    protected function ormAndCriteria(string $nameField, string $productName)
    {
        return $this->doctrineAndCriteria($nameField, $productName);
    }

    protected function ormFindConcreteProducts($product_name_criteria, $id_field, $numbers)
    {
        return $this->findConcreteProducts($product_name_criteria, $id_field, $numbers);
    }

    protected function ormFindNotFinishedProducts($product_name_criteria, $current_proc_id_field, $max_proc_count)
    {
        return $this->findNotFinishedProducts($product_name_criteria, $current_proc_id_field, $max_proc_count);
    }

    protected function getEntityManager() : EntityManager
    {
        return $this->entityManager;
    }

    protected function getDoctrineCriteria(): string
    {
        return $this->doctrineCriteria;
    }

    protected function getDoctrineRepository() :EntityRepository
    {
        return $this->doctrineRepository;
    }


    protected function doctrineAndCriteria(string $fieldName, string $fieldValue): Criteria
    {
        return $this->getDoctrineCriteria()::create()->andWhere(Criteria::create()->expr()
            ->eq($fieldName, $fieldValue));
    }

    public static function _getEntityManager($devMode = true) : EntityManager
    {
        $config_exists = !(file_exists('data/DatabaseConf.php')
            && (class_exists('\data\DatabaseConf')));
        if ($config_exists) {
            throw new AppException(
                'configuration class does not exists in /data dir ' .
                '"DatabaseConf::getConf()" method required ' .
                "by Doctrine ORM"
            );
        }
        $config = DatabaseConf::getConf();
        $doctrine_conf = Setup::createAnnotationMetadataConfiguration(
            array('app/domain'), $devMode
        );
        return $manager = EntityManager::create($config, $doctrine_conf);
    }
}