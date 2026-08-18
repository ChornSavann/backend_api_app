<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Favorite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Product;
use App\Models\User;

class ​​FavoriteController extends Controller
{
    public function toggle(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $userId = $request->user()->id; // យក ID របស់ User ដែលកំពុង Login តាមរយៈ Sanctum Auth
        $productId = $request->product_id;

        // 🔍 ស្វែងរកមើលថាតើ User នេះធ្លាប់ Favorite ផលិតផលនេះហើយឬនៅ
        $favorite = Favorite::where('user_id', $userId)
                            ->where('product_id', $productId)
                            ->first();

        if ($favorite) {
            // ❌ បើមានហើយ គឺលុបចេញ (Unfavorite / ដកចេញ)
            $favorite->delete();
            return response()->json([
                'success' => true,
                'is_favorite' => false,
                'message' => 'Removed from favorites'
            ]);
        } else {
            // ✅ បើអត់ទាន់មាន គឺបង្កើតថ្មី (Favorite / ដាក់ចូល)
            Favorite::create([
                'user_id' => $userId,
                'product_id' => $productId,
            ]);
            return response()->json([
                'success' => true,
                'is_favorite' => true,
                'message' => 'Added to favorites'
            ]);
        }
    }
}
