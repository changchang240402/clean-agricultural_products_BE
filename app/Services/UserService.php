<?php

namespace App\Services;

use App\Repositories\User\UserRepository;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class UserService
{
    protected $userRepository;

    public function __construct(
        UserRepository $userRepository,
    ) {
        $this->userRepository = $userRepository;
    }

    public function getSellerToAdmin()
    {
        return $this->userRepository->getSellerToAdmin();
    }


    public function getShopsToUse(
        string $name = null,
        string $province = null,
    ) {
        $shops = $this->userRepository->getSellersToUser();
        if ($shops->count() > 0) {
            if ($name) {
                $col = 'name';
                $shops = $this->filterByName($shops, $name, $col);
            }
            if ($province) {
                $col = 'address';
                $shops = $this->filterByName($shops, $province, $col);
            }
        }
        if ($shops->isEmpty()) {
            throw new Exception('Shop not found');
        }

        return [
            'shops' => $shops->values()->toArray(),
            'count' => $shops->count(),
        ];
    }

    private function filterByName($shops, $name, $col)
    {
        return $name ? $shops->filter(function ($shop) use ($name, $col) {
            $shopName = strtolower($shop[$col]);
            $searchKeyword = strtolower($name);
            return Str::contains($shopName, $searchKeyword);
        }) : $shops;
    }

    public function filterShop(array $filter)
    {
        $name = null;
        $province = null;
        if (isset($filter['name'])) {
            $name = $filter['name'];
        }
        if (isset($filter['address'])) {
            $province = $filter['address'];
        }
        return $this->getShopsToUse($name, $province);
    }
}
