<?php

namespace App\Repositories\Item;

use App\Repositories\RepositoryInterface;

interface ItemRepositoryInterface extends RepositoryInterface
{
    public function getItemsToUser();

    public function itemDetail($id);

    public function getItem();

    public function totalItemByUserId($month, $year);
}
