<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class LogoController extends Controller
{
    public function index()
    {
        $logo = Setting::get('site_logo', null);
        $siteName = Setting::get('site_name', 'RanKage');
        $siteTagline = Setting::get('site_tagline', 'Game Shop');
        
        return view('admin.logo.index', compact('logo', 'siteName', 'siteTagline'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
            'remove_logo' => 'nullable|boolean',
            'site_name' => 'required|string|max:100',
            'site_tagline' => 'nullable|string|max:100',
        ]);

        // Handle logo removal
        if ($request->remove_logo) {
            $currentLogo = Setting::get('site_logo');
            if ($currentLogo && file_exists(public_path('storage/' . $currentLogo))) {
                unlink(public_path('storage/' . $currentLogo));
            }
            Setting::set('site_logo', null, 'string', 'Site logo image path');
        }
        // Handle logo upload
        elseif ($request->hasFile('logo')) {
            // Delete old logo if exists
            $currentLogo = Setting::get('site_logo');
            if ($currentLogo && file_exists(public_path('storage/' . $currentLogo))) {
                unlink(public_path('storage/' . $currentLogo));
            }

            $logo = $request->file('logo');
            $logoName = 'logo_' . time() . '.' . $logo->getClientOriginalExtension();
            $logo->move(public_path('storage/logos'), $logoName);
            
            Setting::set('site_logo', 'logos/' . $logoName, 'string', 'Site logo image path');
        }

        // Update site name and tagline
        Setting::set('site_name', $request->site_name, 'string', 'Site name');
        Setting::set('site_tagline', $request->site_tagline ?? '', 'string', 'Site tagline');

        return response()->json([
            'success' => true,
            'message' => 'Logo settings updated successfully',
        ]);
    }
}
