<?php

namespace org\schema;

class ProductModel extends Product
{
    /**
     * A pointer from a previous, often discontinued variant of the product to its newer variant.
     * @var ProductModel|null
     */
    public ?ProductModel $predecessorOf ;

    /**
     * A pointer from a newer variant of a product to its previous, often discontinued predecessor.
     * @var ProductModel|null
     */
    public ?ProductModel $successorOf ;

}