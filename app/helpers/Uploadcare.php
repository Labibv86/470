<?php

use Uploadcare\Api;
use Uploadcare\Uploader\Uploader;

if (!function_exists('uploadToUploadcare')) {
    function uploadToUploadcare($imageFile)
    {
        try {
            $publicKey = env('UPLOADCARE_PUBLIC_KEY');
            $secretKey = env('UPLOADCARE_SECRET_KEY');

            $api = new Api($publicKey, $secretKey);
            $uploader = new Uploader($api);

            $file = $uploader->fromPath($imageFile->getRealPath());

            // Return the CDN URL
            return 'https://ucarecdn.com/' . $file->getUuid() . '/';
        } catch (\Exception $e) {
            return null;
        }
    }
}
