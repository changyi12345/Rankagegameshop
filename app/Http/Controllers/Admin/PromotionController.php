<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class PromotionController extends Controller
{
    public function index()
    {
        $promotion = [
            'enabled' => Setting::get('promotion_enabled', true),
            'icon' => Setting::get('promotion_icon', '🎉'),
            'title' => Setting::get('promotion_title', 'Special Promotion!'),
            'description' => Setting::get('promotion_description', 'Get 10% extra diamonds on Mobile Legends!'),
            'button_text' => Setting::get('promotion_button_text', 'Shop Now'),
            'button_link' => Setting::get('promotion_button_link', '/games'),
        ];

        return view('admin.promotions.index', compact('promotion'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'enabled' => 'boolean',
            'icon' => 'nullable|string|max:10',
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:500',
            'button_text' => 'required|string|max:50',
            'button_link' => 'required|string|max:255',
        ]);

        Setting::set('promotion_enabled', $request->enabled ?? false, 'boolean', 'Enable/disable promotion banner');
        Setting::set('promotion_icon', $request->icon ?? '🎉', 'string', 'Promotion icon emoji');
        Setting::set('promotion_title', $request->title, 'string', 'Promotion title');
        Setting::set('promotion_description', $request->description, 'string', 'Promotion description');
        Setting::set('promotion_button_text', $request->button_text, 'string', 'Promotion button text');
        Setting::set('promotion_button_link', $request->button_link, 'string', 'Promotion button link');

        return response()->json([
            'success' => true,
            'message' => 'Promotion updated successfully',
        ]);
    }
}
