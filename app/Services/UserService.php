<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\User\UserRepository;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UserService
{
    private const PAGINATE_PER_PAGE = 10;

    protected $userRepository;

    protected $mapboxService;

    public function __construct(
        UserRepository $userRepository,
        MapboxService $mapboxService,
    ) {
        $this->userRepository = $userRepository;
        $this->mapboxService = $mapboxService;
    }

    public function getSellerToAdmin(
        int $page,
        string $name = null,
        int $status = null,
        string $sort = null,
    ) {
        $sellers =  $this->userRepository->getSellerToAdmin();
        if ($sellers->count() > 0) {
            if ($name) {
                $col = 'name';
                $sellers = $this->filterByName($sellers, $name, $col);
            }
            if (isset($status)) {
                $sellers = $this->filterByStatus($sellers, $status);
            }
            if ($sort) {
                $col1 = 'items_count';
                $sellers = $this->filterBySort($sellers, $sort, $col1);
            }
        }
        if ($sellers->isEmpty()) {
            throw new Exception('Seller not found');
        }
        $perPage = self::PAGINATE_PER_PAGE;
        $sellersPerPage = $sellers->forPage($page, $perPage);
        $paginatedSellers = new LengthAwarePaginator(
            $sellersPerPage->values()->all(),
            $sellers->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return $paginatedSellers;
    }

    public function getUserToAdmin(
        int $page,
        string $name = null,
        int $status = null,
        string $sort = null,
    ) {
        $users =  $this->userRepository->getUserToAdmin();
        if ($users->count() > 0) {
            if ($name) {
                $col = 'name';
                $users = $this->filterByName($users, $name, $col);
            }
            if (isset($status)) {
                $users = $this->filterByStatus($users, $status);
            }
            if ($sort) {
                $col1 = 'total_order_remove';
                $users = $this->filterBySort($users, $sort, $col1);
            }
        }
        if ($users->isEmpty()) {
            throw new Exception('User not found');
        }
        $perPage = self::PAGINATE_PER_PAGE;
        $usersPerPage = $users->forPage($page, $perPage);
        $paginatedUsers = new LengthAwarePaginator(
            $usersPerPage->values()->all(),
            $users->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return $paginatedUsers;
    }

    public function getTraderToAdmin(
        int $page,
        string $name = null,
        int $status = null,
        string $sort = null,
    ) {
        $traders = $this->userRepository->getTraderToAdmin();
        if ($traders->count() > 0) {
            if ($name) {
                $col = 'name';
                $traders = $this->filterByName($traders, $name, $col);
            }
            if (isset($status)) {
                $traders = $this->filterByStatus($traders, $status);
            }
            if ($sort) {
                $col1 = 'average_review_score';
                $traders = $this->filterBySort($traders, $sort, $col1);
            }
        }
        if ($traders->isEmpty()) {
            throw new Exception('Trader not found');
        }
        $perPage = self::PAGINATE_PER_PAGE;
        $tradersPerPage = $traders->forPage($page, $perPage);
        $paginatedTraders = new LengthAwarePaginator(
            $tradersPerPage->values()->all(),
            $traders->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return $paginatedTraders;
    }

    private function filterBySort($users, $sort, $name)
    {
        if ($sort === 'asc') {
            return $users->sortBy($name);
        } elseif ($sort === 'desc') {
            return $users->sortByDesc($name);
        }
    }

    private function filterByStatus($users, $status)
    {
        return isset($status) ? $users->filter(function ($user) use ($status) {
            return $user['status'] === $status;
        }) : $users;
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

    public function filterTrader(int $page, array $filter)
    {
        $name = null;
        $status = null;
        $sort = null;
        if (isset($filter['name'])) {
            $name = $filter['name'];
        }
        if (isset($filter['status'])) {
            $status = $filter['status'];
        }
        if (isset($filter['sort'])) {
            $sort = $filter['sort'];
        }
        return $this->getTraderToAdmin($page, $name, $status, $sort);
    }

    public function filterSeller(int $page, array $filter)
    {
        $name = null;
        $status = null;
        $sort = null;
        if (isset($filter['name'])) {
            $name = $filter['name'];
        }
        if (isset($filter['status'])) {
            $status = $filter['status'];
        }
        if (isset($filter['sort'])) {
            $sort = $filter['sort'];
        }
        return $this->getSellerToAdmin($page, $name, $status, $sort);
    }

    public function filterUser(int $page, array $filter)
    {
        $name = null;
        $status = null;
        $sort = null;
        if (isset($filter['name'])) {
            $name = $filter['name'];
        }
        if (isset($filter['status'])) {
            $status = $filter['status'];
        }
        if (isset($filter['sort'])) {
            $sort = $filter['sort'];
        }
        return $this->getUserToAdmin($page, $name, $status, $sort);
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
    public function findTraderShip($address, $total_quantity)
    {
        $seller = $this->mapboxService->getCoordinates($address);
        if (!$seller) {
            throw new Exception('This address user does not exist');
        }
        $traders = User::where('role', '=', config('constants.ROLE')['trader'])
                        ->where('status', '=', config('constants.STATUS_USER')['in use'])
                        ->where('payload', '>=', $total_quantity)
                        ->get();
        $nearestTrader = null;
        $nearestDistance = PHP_INT_MAX;
        foreach ($traders as $trader) {
            $address2 = $this->mapboxService->getCoordinates($trader->address);

            $distance = $this->mapboxService->getDirections($address2, $seller);

            if ($distance < $nearestDistance) {
                $nearestTrader = $trader;
                $nearestDistance = $distance;
            }
        }

        return $nearestTrader;
    }

    public function uploadS3($path, $address)
    {
        $filePath = $address . time() . '_' . $path->getClientOriginalName();
        Storage::put($filePath, file_get_contents($path), 's3');
        $fileUrl = Storage::disk('s3')->url($filePath);
        return $fileUrl;
    }
}