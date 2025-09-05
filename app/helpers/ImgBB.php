<?php

use Illuminate\Support\Facades\Http;

function uploadToImgBB($imageFile)
{
    try {
        $apiKey = env('IMGBB_API_KEY');

        if (!$apiKey) {
            throw new Exception('ImgBB API key not configured');
        }

        $response = Http::timeout(30)->asForm()->post('https://api.imgbb.com/1/upload', [
            'key' => $apiKey,
            'image' => base64_encode(file_get_contents($imageFile->getRealPath())),
            'name' => 'shop_' . time() . '_' . uniqid()
        ]);

        if ($response->successful()) {
            return $response->json()['data']['url'];
        }

        \Log::error('ImgBB upload failed: ' . $response->body());
        return null;

    } catch (Exception $e) {
        \Log::error('ImgBB upload error: ' . $e->getMessage());
        return null;
    }
}
