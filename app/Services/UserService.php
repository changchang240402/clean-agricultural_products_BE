<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\User\UserRepository;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class UserService
{
    protected $userRepository;

    protected $mapboxService;

    public function __construct(
        UserRepository $userRepository,
        MapboxService $mapboxService,
    ) {
        $this->userRepository = $userRepository;
        $this->mapboxService = $mapboxService;
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

    public function sellerDetailById($id)
    {
        return $this->userRepository->sellerDetailById($id);
    }

    public function getCostShip($userId, $sellerId)
    {
        $address1 = User::where('id', $userId)->first();
        if (!$address1) {
            throw new Exception('This address1 does not exist');
        }
        $address2 = User::where('id', $sellerId)->first();
        if (!$address2) {
            throw new Exception('This address2 does not exist');
        }
        $user = $this->mapboxService->getCoordinates($address1->address);
        if (!$user) {
            throw new Exception('This address user does not exist');
        }
        $seller = $this->mapboxService->getCoordinates($address2->address);
        if (!$seller) {
            throw new Exception('This address seller does not exist');
        }
        return $this->mapboxService->getDirections($user, $seller);
    }
}
