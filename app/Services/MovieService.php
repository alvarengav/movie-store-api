<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MovieService
{
    /**
     * Upload image in store/public/movies_images/  path
     * @param \Illuminate\Http\UploadedFile|\Illuminate\Http\UploadedFile[]|array $image
     */
    public static function uploadImage($image)
    {
        $originalName = $image->getClientOriginalName();
        $path = $image->storeAs('movies_images', $originalName, 'public');
        return $path;
    }

    /**
     * Delete image in store/public/movies_images/  path
     * @param string path
     */
    public static function deleteImage($path)
    {
        Storage::disk('public')->delete($path);
    }
}
