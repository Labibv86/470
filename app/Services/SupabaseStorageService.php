<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class SupabaseStorageService
{
    public function uploadImage($file, $bucket = 'item-images')
    {
        try {
            // Get Supabase credentials
            $projectUrl = env('SUPABASE_PROJECT_URL');
            $apiKey = env('SUPABASE_API_KEY');

            \Log::info('Supabase credentials check:', [
                'project_url' => $projectUrl,
                'api_key_exists' => !empty($apiKey),
                'bucket' => $bucket
            ]);

            if (!$projectUrl || !$apiKey) {
                \Log::error('Supabase credentials missing');
                throw new \Exception('Storage configuration error');
            }

            // Generate unique filename
            $filename = 'shop_' . time() . '_' . uniqid() . '.' . $file->extension();

            \Log::info('Attempting upload:', ['filename' => $filename]);

            // Upload to Supabase
            $response = Http::timeout(30)->withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => $file->getMimeType(),
            ])->withBody(
                file_get_contents($file->getRealPath()),
                $file->getMimeType()
            )->post("{$projectUrl}/storage/v1/object/{$bucket}/{$filename}");

            \Log::info('Supabase response:', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            if ($response->successful()) {
                $url = "{$projectUrl}/storage/v1/object/public/{$bucket}/{$filename}";
                \Log::info('Upload successful:', ['url' => $url]);
                return $url;
            }

            \Log::error('Supabase upload failed: ' . $response->body());
            return null;

        } catch (\Exception $e) {
            \Log::error('Supabase upload error: ' . $e->getMessage());
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
