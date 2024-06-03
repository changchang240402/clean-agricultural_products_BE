<?php

namespace App\Repositories\User;

use App\Repositories\RepositoryInterface;

interface UserRepositoryInterface extends RepositoryInterface
{
    public function getSellersToUser();

    public function getSellerToAdmin();

    public function getUserToAdmin();

    public function sellerDetailById($id);

    public function getTraderToAdmin();

    public function totalUserByUserId($month, $year);
}
