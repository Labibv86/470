<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class SupabaseStorageService
{
    public function uploadImage($file, $bucket = 'shop-logos')
    {
        try {
            // Get Supabase credentials
            $projectUrl = env('SUPABASE_PROJECT_URL');
            $apiKey = env('SUPABASE_API_KEY');

            Log::info('=== SUPABASE UPLOAD DEBUG START ===');
            Log::info('Project URL: ' . $projectUrl);
            Log::info('API Key exists: ' . (!empty($apiKey) ? 'Yes' : 'No'));
            Log::info('Bucket: ' . $bucket);
            Log::info('File name: ' . $file->getClientOriginalName());
            Log::info('File size: ' . $file->getSize());
            Log::info('File mime: ' . $file->getMimeType());

            if (!$projectUrl || !$apiKey) {
                Log::error('MISSING CREDENTIALS: Project URL or API Key is empty');
                return null;
            }

            // Generate unique filename
            $filename = 'shop_' . time() . '_' . uniqid() . '.' . $file->extension();
            Log::info('Generated filename: ' . $filename);

            // Upload to Supabase
            $url = "{$projectUrl}/storage/v1/object/{$bucket}/{$filename}";
            Log::info('Upload URL: ' . $url);

            $response = Http::timeout(30)->withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => $file->getMimeType(),
            ])->withBody(
                file_get_contents($file->getRealPath()),
                $file->getMimeType()
            )->post($url);

            Log::info('Response Status: ' . $response->status());
            Log::info('Response Body: ' . $response->body());
            Log::info('=== SUPABASE UPLOAD DEBUG END ===');

            if ($response->successful()) {
                $publicUrl = "{$projectUrl}/storage/v1/object/public/{$bucket}/{$filename}";
                Log::info('SUCCESS: Upload completed - ' . $publicUrl);
                return $publicUrl;
            }

            Log::error('FAILED: Supabase upload failed');
            return null;

        } catch (\Exception $e) {
            Log::error('EXCEPTION: ' . $e->getMessage());
            Log::error('Exception trace: ' . $e->getTraceAsString());
            return null;
        }
    }
}
