<?php

namespace App\Repositories\Item;

use App\Repositories\RepositoryInterface;

interface ItemRepositoryInterface extends RepositoryInterface
{
    public function getItemsToUser();
}