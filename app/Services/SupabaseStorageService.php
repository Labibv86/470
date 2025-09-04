<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class SupabaseStorageService
{
    public function uploadImage($file, $bucket = 'item-images')
    {
        try {
            // Generate unique filename
            $filename = 'item_' . time() . '_' . uniqid() . '.' . $file->extension();

            // Get Supabase credentials
            $projectUrl = env('SUPABASE_PROJECT_URL');
            $apiKey = env('SUPABASE_API_KEY');

            if (!$projectUrl || !$apiKey) {
                throw new \Exception('Supabase credentials not configured');
            }

            // Upload to Supabase using simple HTTP client
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => $file->getMimeType(),
            ])->withBody(
                file_get_contents($file->getRealPath()),
                $file->getMimeType()
            )->post("{$projectUrl}/storage/v1/object/{$bucket}/{$filename}");

            if ($response->successful()) {
                // Return public URL
                return "{$projectUrl}/storage/v1/object/public/{$bucket}/{$filename}";
            }

            Log::error('Supabase upload failed: ' . $response->body());
            return null;

        } catch (\Exception $e) {
            Log::error('Supabase upload error: ' . $e->getMessage());
            return null;
        }
    }

    public function deleteImage($imageUrl)
    {
        try {
            $projectUrl = env('SUPABASE_PROJECT_URL');
            $apiKey = env('SUPABASE_API_KEY');

            // Extract filename from URL
            $path = parse_url($imageUrl, PHP_URL_PATH);
            $parts = explode('/', $path);
            $bucket = $parts[3] ?? 'item-images';
            $filename = $parts[5] ?? '';

            if (!$filename) {
                return false;
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
            ])->delete("{$projectUrl}/storage/v1/object/{$bucket}/{$filename}");

            return $response->successful();

        } catch (\Exception $e) {
            Log::error('Supabase delete error: ' . $e->getMessage());
            return false;
        }
    }
}
