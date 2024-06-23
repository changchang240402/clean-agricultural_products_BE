<?php

namespace App\Repositories\Item;

use App\Models\Item;
use App\Repositories\BaseRepository;
use Exception;
use Illuminate\Contracts\Database\Eloquent\Builder;

class ItemRepository extends BaseRepository implements ItemRepositoryInterface
{
    public function getModel()
    {
        return Item::class;
    }

    public function getItemsToUser()
    {
        return $this->model
            ->where('status', '=', config('constants.STATUS_ITEM')['in use'])
            ->with([
                'product' => function ($query) {
                    $query->select('id', 'product_name');
                }
            ])
            ->withCount([
                'orderDetails as total_orders' => function ($query) {
                    $query->whereHas('order', function ($query) {
                        $query->where('status', '=', 4);
                    });
                }
            ])
            ->withCount([
                'attachments as total_reviews' => function ($query) {
                    $query->where('review_type', '=', config('constants.REVIEW')['item']);
                }
            ])
            ->with([
                'attachments' => function ($query) {
                    $query->where('review_type', '=', config('constants.REVIEW')['item']);
                }
            ])
            ->get()
            ->map(function ($item) {
                $totalReviews = $item->total_reviews;
                $totalScore =  $item->attachments->sum(function ($attachment) {
                    return $attachment->review_type == config('constants.REVIEW')['item'] ? $attachment->number_star : 0;
                });
                $averageScore = $totalReviews > 0 ? round($totalScore / $totalReviews, 1) : 0;
                $item->average_review_score = $averageScore;
                return [
                    'id' => $item->id,
                    'item_name' => $item->item_name,
                    'seller_id' => $item->seller_id,
                    'product_id' => $item->product_id,
                    'price' => $item->price,
                    'price_type' => $item->price_type,
                    'image' => $item->image,
                    'total_orders' => $item->total_orders,
                    'total_reviews' => $item->total_reviews,
                    'average_review_score' => $item->average_review_score,
                    'product_name' => $item->product->product_name,
                    'created_at' => $item->created_at,
                ];
            });
    }

    public function itemDetail($id)
    {
        $item = $this->model->where('id', $id)
            ->with([
                'product' => function ($query) {
                    $query->select('id', 'product_name');
                }
            ])
            ->with([
                'seller' => function ($query) {
                    $query->select('id', 'name', 'avatar');
                }
            ])
            ->withCount([
                'orderDetails as total_orders' => function ($query) {
                    $query->whereHas('order', function ($query) {
                        $query->where('status', '=', 4);
                    });
                }
            ])
            ->withCount([
                'attachments as total_reviews' => function ($query) {
                    $query->where('review_type', '=', config('constants.REVIEW')['item']);
                }
            ])
            ->with([
                'attachments' => function ($query) {
                    $query->where('review_type', '=', config('constants.REVIEW')['item']);
                }
            ])
            ->first();
        if (!$item) {
            throw new Exception('Item not found');
        }
        $totalReviews = $item->total_reviews;
        $totalScore =  $item->attachments->sum(function ($attachment) {
            return $attachment->review_type == config('constants.REVIEW')['item'] ? $attachment->number_star : 0;
        });
        $averageScore = $totalReviews > 0 ? round($totalScore / $totalReviews, 1) : 0;
        return [
            'id' => $item->id,
            'item_name' => $item->item_name,
            'seller_id' => $item->seller_id,
            'product_id' => $item->product_id,
            'describe' => $item->describe,
            'total' => $item->total,
            'price' => $item->price,
            'type' => $item->type,
            'price_type' => $item->price_type,
            'image' => $item->image,
            'total_orders' => $item->total_orders,
            'total_reviews' => $item->total_reviews,
            'average_review_score' => $averageScore,
            'status' => $item->status,
            'product_name' => $item->product->product_name,
            'seller_name' => $item->seller->name,
            'seller_avatar' => $item->seller->avatar,
            'created_at' => $item->created_at,
        ];
    }

    public function getItemWarning()
    {
        return $this->model
            ->where('status', '=', config('constants.STATUS_ITEM')['in use'])
            ->with([
                'product' => function ($query) {
                    $query->select('id', 'product_name', 'price_max', 'price_min');
                }
            ])
            ->get()
            ->filter(function ($item) {
                $price = $item->price;
                $priceMax = $item->product->price_max;
                $priceMin = $item->product->price_min;
                return $price < $priceMin || $price > $priceMax;
            })
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'seller_id' => $item->seller_id,
                    'product_name' => $item->product->product_name,
                ];
            })
            ->values();
    }

    public function getItemUnban()
    {
        return $this->model
            ->where('status', '=', config('constants.STATUS_ITEM')['archived'])
            ->with([
                'product' => function ($query) {
                    $query->select('id', 'product_name', 'price_max', 'price_min');
                }
            ])
            ->get()
            ->filter(function ($item) {
                $price = $item->price;
                $priceMax = $item->product->price_max;
                $priceMin = $item->product->price_min;
                return $price > $priceMin && $price < $priceMax;
            })
            ->pluck('id');
    }

    public function getItem()
    {
        $userId = auth()->id();
        $user = auth()->user();
        $items = [];
        switch ($user->role) {
            case 0:
                $items = $this->model
                    ->with([
                        'product' => function ($query) {
                            $query->select('id', 'product_name', 'price_max', 'price_min');
                        }
                    ])
                    ->withCount([
                        'notifications as notifications' => function ($query) {
                            $query->where('notification_type_id', '=', 2);
                        }
                    ])
                    ->orderByDesc('notifications')
                    ->orderBy('updated_at', 'desc')->get();
                break;
            case 2:
                $items = $this->model
                    ->where('seller_id', '=', $userId)
                    ->with([
                        'product' => function ($query) {
                            $query->select('id', 'product_name', 'price_max', 'price_min');
                        }
                    ])
                    ->withCount([
                        'notifications as notifications' => function ($query) {
                            $query->where('notification_type_id', '=', 2);
                        }
                    ])
                    ->orderByDesc('notifications')->get();
                break;
            default:
                return response()->json(['message' => 'Unknown Role'], 400);
        }
        return $items;
    }
    public function totalItemByUserId($month, $year)
    {
        $item = $this->model->count();
        $item_now = $this->model
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->count();
        return [
            'item' => $item,
            'item_now' => $item_now
        ];
    }

    public function getItemBySellerId($userId, $itemId)
    {
        return $this->model->where('id', '=', $itemId)->where('seller_id', '=', $userId)->first();
    }

    // public function update($id, $attributes = [])
    // {
    //     $item = $this->model::find($id);
    //     if (!$item) {
    //         throw new Exception('Issue not found');
    //     }
    //     $item->update($attributes);
    //     if (!$item->wasChanged()) {
    //         throw new Exception('No changes have been made');
    //     }
    //     $changes = ['new' => $item->getChanges()];

    //     return $changes;
    // }
}
