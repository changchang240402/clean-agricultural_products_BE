<?php

namespace App\Services;

use App\Repositories\Item\ItemRepository;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ItemService
{
    private const PAGINATE_PER_PAGE = 20;

    private const PAGINATE_PER_PAGE_COUNT = 10;
    protected $itemRepository;

    public function __construct(
        ItemRepository $itemRepository,
    ) {
        $this->itemRepository = $itemRepository;
    }

    public function getItem(
        int $page,
        string $name = null,
        int $status = null,
    ) {
        $items = $this->itemRepository->getItem();
        if ($items->count() > 0) {
            if ($name) {
                $items = $this->filterByName($items, $name);
            }
            if (isset($status)) {
                $items = $this->filterByStatus($items, $status);
            }
        }
        if ($items->isEmpty()) {
            throw new Exception('Item not found');
        }
        $perPage = self::PAGINATE_PER_PAGE_COUNT;
        $itemsPerPage = $items->forPage($page, $perPage);
        $paginatedItems = new LengthAwarePaginator(
            $itemsPerPage->values()->all(),
            $items->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return $paginatedItems;
    }

    public function getItemsToUser(
        int $page,
        string $name = null,
        int $product = null,
        string $sort = null,
    ) {
        $items = $this->itemRepository->getItemsToUser();
        if ($items->count() > 0) {
            if ($name) {
                $items = $this->filterByName($items, $name);
            }
            if ($product) {
                $items = $this->filterByProduct($items, $product);
            }
            if ($sort) {
                $items = $this->filterBySort($items, $sort);
            }
        }
        if ($items->isEmpty()) {
            throw new Exception('Item not found');
        }
        $perPage = self::PAGINATE_PER_PAGE;
        $itemsPerPage = $items->forPage($page, $perPage);
        $paginatedItems = new LengthAwarePaginator(
            $itemsPerPage->values()->all(),
            $items->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return $paginatedItems;
    }

    private function filterBySort($items, $sort)
    {
        if ($sort === 'asc') {
            return $items->sortBy('price');
        } elseif ($sort === 'desc') {
            return $items->sortByDesc('price');
        }
    }

    private function filterByStatus($items, $status)
    {
        return isset($status) ? $items->filter(function ($item) use ($status) {
            return $item['status'] === $status;
        }) : $items;
    }

    private function filterByName($items, $name)
    {
        return $name ? $items->filter(function ($item) use ($name) {
            $itemName = strtolower($item['item_name']);
            $searchKeyword = strtolower($name);
            return Str::contains($itemName, $searchKeyword);
        }) : $items;
    }

    private function filterByProduct($items, $product)
    {
        return $product ? $items->filter(function ($item) use ($product) {
            if ($item['product_id'] === $product) {
                return true;
            }
            return false;
        }) : $items;
    }

    public function filterItem(int $page, array $filter)
    {
        $name = null;
        $product = null;
        $sort = null;
        if (isset($filter['name'])) {
            $name = $filter['name'];
        }
        if (isset($filter['product_id'])) {
            $product = $filter['product_id'];
        }
        if (isset($filter['sort'])) {
            $sort = $filter['sort'];
        }
        return $this->getItemsToUser($page, $name, $product, $sort);
    }

    public function filterItems(int $page, array $filter)
    {
        $name = null;
        $status = null;
        if (isset($filter['name'])) {
            $name = $filter['name'];
        }
        if (isset($filter['status'])) {
            $status = $filter['status'];
        }
        return $this->getItem($page, $name, $status);
    }
    public function getTopItemSale()
    {
        $item = $this->itemRepository->getItemsToUser();
        return $item->sortByDesc('total_orders')->take(10)->values()->toArray();
    }

    public function getNewItemSale()
    {
        $items = $this->itemRepository->getItemsToUser();
        return $items->sortBy('total_orders')->take(25)->values()->toArray();
    }

    public function itemDetail($id)
    {
        return $this->itemRepository->itemDetail($id);
    }

    public function getItemByShop($id)
    {
        $items = $this->itemRepository->getItemsToUser();
        $itemShop = $items->filter(function ($item) use ($id) {
            if ($item['seller_id'] == $id) {
                return true;
            }
            return false;
        });
        return $itemShop->values()->toArray();
    }

    public function getItemWarning()
    {
        return $this->itemRepository->getItemWarning();
    }

    public function getItemUnban()
    {
        return $this->itemRepository->getItemUnban();
    }

    public function getItemBan()
    {
        $items = $this->itemRepository->getItem();
        return $items->filter(function ($item) {
            if ($item['status'] == config('constants.STATUS_ITEM')['in use'] && $item['notifications'] >= 3) {
                return true;
            }
            return false;
        })->pluck('id');
    }

    public function createItem($item)
    {
        $item['seller_id'] = auth()->id();
        $item['status'] = 2;
        try {
            $data = $this->itemRepository->create($item);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
        return $data;
    }

    public function updateItem($id, $item)
    {
        $userId = auth()->id();
        $itemOld = $this->itemRepository->getItemBySellerId($userId, $id);
        if (!$itemOld) {
            throw new Exception('This item does not exist');
        }
        try {
            $data = $this->itemRepository->update($id, $item);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
        return $data;
    }
}
