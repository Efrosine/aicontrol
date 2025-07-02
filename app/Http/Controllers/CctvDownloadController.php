<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;

class CctvDownloadController extends Controller
{
    /**
     * Handle public CCTV file download from the archive.
     *
     * @param Request $request
     * @param string $camera_name
     * @param string $year
     * @param string $month
     * @param string $day
     * @param string $detection_type
     * @param string $filename
     * @return \Symfony\Component\HttpFoundation\StreamedResponse|\Illuminate\Http\JsonResponse
     */
    public function download(Request $request, $camera_name, $year, $month, $day, $detection_type, $filename)
    {
        try {
            // Reconstruct the storage path from the URL parameters.
            $storagePath = sprintf(
                '%s/%s/%s/%s/%s/%s',
                $camera_name,
                $year,
                $month,
                $day,
                $detection_type,
                $filename
            );

            Log::info('File download requested', ['path' => $storagePath]);

            // Check if AWS SDK is available
            if (!class_exists('\Aws\S3\S3Client')) {
                Log::error('AWS S3 SDK not found');
                return response()->json([
                    'success' => false,
                    'message' => 'Storage service not available. AWS SDK not installed.'
                ], 500);
            }

            // Get MinIO client and bucket name
            $client = $this->getMinIOClient();
            $bucket = config('filesystems.disks.minio.bucket', 'detection-archive');

            // Check if the file exists in MinIO
            try {
                $client->headObject([
                    'Bucket' => $bucket,
                    'Key' => $storagePath
                ]);
            } catch (\Aws\S3\Exception\S3Exception $e) {
                if ($e->getAwsErrorCode() === 'NotFound' || $e->getAwsErrorCode() === '404') {
                    Log::warning('File not found for download', ['path' => $storagePath]);
                    return response()->json([
                        'success' => false,
                        'message' => 'File not found.'
                    ], 404);
                }
                throw $e;
            }

            // Get the file object from MinIO
            $object = $client->getObject([
                'Bucket' => $bucket,
                'Key' => $storagePath
            ]);

            // Get content type from metadata
            $contentType = $object['ContentType'] ?? 'application/octet-stream';
            
            // Stream the file to the client
            return Response::stream(function() use ($object) {
                $stream = $object['Body'];
                while (!$stream->eof()) {
                    echo $stream->read(8192);
                    if (ob_get_level()) {
                        ob_flush();
                    }
                    flush();
                }
            }, 200, [
                'Content-Type' => $contentType,
                'Content-Disposition' => 'attachment; filename="' . basename($filename) . '"',
                'Content-Length' => $object['ContentLength'] ?? 0,
                'Cache-Control' => 'no-cache, must-revalidate',
            ]);

        } catch (\Aws\S3\Exception\S3Exception $e) {
            Log::error('MinIO S3 error during file download', [
                'path' => $storagePath ?? 'N/A',
                'error' => $e->getMessage(),
                'aws_error_code' => $e->getAwsErrorCode()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Storage service error: ' . $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            Log::error('An unexpected error occurred during file download', [
                'path' => $storagePath ?? 'N/A',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'An internal server error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get MinIO S3 client instance
     * 
     * @return \Aws\S3\S3Client
     */
    private function getMinIOClient()
    {
        $endpoint = config('storage.minio.endpoint');
        $accessKey = config('storage.minio.access_key');
        $secretKey = config('storage.minio.secret_key');
        $region = config('storage.minio.region', 'us-east-1');

        \Log::info('Creating MinIO client', [
            'endpoint' => $endpoint,
            'access_key' => $accessKey,
            'region' => $region
        ]);
        // Ensure endpoint has proper protocol
        if (!str_starts_with($endpoint, 'http://') && !str_starts_with($endpoint, 'https://')) {
            $endpoint = 'http://' . $endpoint;
        }
        
        return new \Aws\S3\S3Client([
            'version' => 'latest',
            'region' => $region,
            'endpoint' => $endpoint,
            'use_path_style_endpoint' => true,
            'credentials' => [
                'key' => $accessKey,
                'secret' => $secretKey,
            ],
        ]);
    }
}
