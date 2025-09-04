<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class SupabaseStorageService
{
    public function uploadImage($file, $bucket = 'shop-logos')
    {
        // Immediate debug output
        Log::info('=== SUPABASE UPLOAD START ===');
        Log::info('Bucket: ' . $bucket);
        Log::info('File: ' . $file->getClientOriginalName());
        Log::info('Size: ' . $file->getSize() . ' bytes');
        Log::info('MIME: ' . $file->getMimeType());

        try {
            // Get Supabase credentials
            $projectUrl = env('SUPABASE_PROJECT_URL');
            $apiKey = env('SUPABASE_API_KEY');

            Log::info('Project URL: ' . $projectUrl);
            Log::info('API Key exists: ' . (!empty($apiKey) ? 'Yes' : 'No'));

            if (!$projectUrl || !$apiKey) {
                Log::error('MISSING: Supabase credentials not configured');
                return null;
            }

            // Generate unique filename
            $filename = 'shop_' . time() . '_' . uniqid() . '.' . $file->extension();
            Log::info('Generated filename: ' . $filename);

            // Upload to Supabase
            $uploadUrl = "{$projectUrl}/storage/v1/object/{$bucket}/{$filename}";
            Log::info('Upload URL: ' . $uploadUrl);

            // Read file contents
            $fileContents = file_get_contents($file->getRealPath());
            Log::info('File contents size: ' . strlen($fileContents) . ' bytes');

            $response = Http::timeout(30)->withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => $file->getMimeType(),
            ])->withBody(
                $fileContents,
                $file->getMimeType()
            )->post($uploadUrl);

            Log::info('Response Status: ' . $response->status());
            Log::info('Response Body: ' . $response->body());

            if ($response->successful()) {
                $publicUrl = "{$projectUrl}/storage/v1/object/public/{$bucket}/{$filename}";
                Log::info('SUCCESS: Upload completed - ' . $publicUrl);
                Log::info('=== SUPABASE UPLOAD END ===');
                return $publicUrl;
            }

            Log::error('FAILED: Supabase upload failed with status ' . $response->status());
            Log::info('=== SUPABASE UPLOAD END ===');
            return null;

        } catch (\Exception $e) {
            Log::error('EXCEPTION: ' . $e->getMessage());
            Log::error('Exception trace: ' . $e->getTraceAsString());
            Log::info('=== SUPABASE UPLOAD END ===');
            return null;
        }
    }
}
