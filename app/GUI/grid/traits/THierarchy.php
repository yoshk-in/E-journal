<?php


namespace App\GUI\grid\traits;


use Gui\Components\VisualObjectInterface;

trait THierarchy
{
    protected ?THierarchy $child;
    protected ?object $parent = null;
    protected array $children = [];

    public function child(THierarchy $child)
    {
        $child->parent($child);
        $this->child = $child;

    }

    public function toChildren($id, THierarchy $child)
    {
        assert(!isset($this->children[$id]), 'this child already assigned');
        $this->children[$id] = $child;
        $child->parent($this);
    }

    public function parent($parent)
    {
        assert(is_null($this->parent), ' parent has already assigned');
        $this->parent = $parent;
    }

    public function getChild(): THierarchy
    {
        return $this->child;
    }


    public function getParent(): object
    {
        return $this->parent;
    }
}