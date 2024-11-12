<?php

namespace App\Services;

use Intervention\Image\ImageManager;
use Illuminate\Http\Request;
use Intervention\Image\Drivers\Gd\Driver;

class FileUpload
{
    /**
     * Handle image upload and thumbnail generation.
     */
    public function handleFileUpload(Request $request, $user)
    {
        $imageName = $user->image; 

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $ext = $image->getClientOriginalExtension();
            $imageName = $request->name . '-' . time() . '.' . $ext;
            $image->move(public_path("/profile_pic/"), $imageName);

            $sourcePath = public_path("/profile_pic/" . $imageName);
            $manager = new ImageManager(Driver::class);
            $image = $manager->read($sourcePath);
            $image->cover(150, 150);
            $image->save(public_path('/profile_pic/thumb/' . $imageName));
            $imageName = asset('profile_pic/thumb/' . $imageName);
        }

        return $imageName;
    }
}