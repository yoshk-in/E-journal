<?php


namespace App\domain;

/**
 * @Entity
 */
class ProductNumberManager
{
    //min finished product, product numbers which less than that are all finished
    /**
     * @Column(type="integer", nullable=true)
     */
    private $horizonNumber;

    /** @Id
     * @Column(type="string")
     */
    private $productName;

    /**
     * @return mixed
     */
    public function getHorizonNumber()
    {
        return $this->horizonNumber;
    }

    /**
     * @param mixed $horizonNumber
     * @return ProductNumberManager
     */
    public function setHorizonNumber($horizonNumber)
    {
        $this->horizonNumber = $horizonNumber;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getProductName()
    {
        return $this->productName;
    }

    /**
     * @param mixed $productName
     * @return ProductNumberManager
     */
    public function setProductName($productName)
    {
        $this->productName = $productName;
        return $this;
    }
}