# CCTV File Upload and Download API

This document describes the public API endpoints for uploading and downloading CCTV detection files.

## Upload Endpoint

### Endpoint
`POST /api/cctv/upload`

### Access
Public (no authentication required)

### Request Format
`multipart/form-data`

### Required Parameters
- `file`: The media file (image/video) to upload
- `cctv_name`: String representing the camera name
- `detection_type`: String (e.g., person, vehicle, motion, face, package)

### Optional Parameters
- `timestamp`: ISO date string (if not provided, server time is used)

### File Constraints
- Maximum file size: 200MB
- Supported formats: `.jpg`, `.jpeg`, `.png`, `.mp4`, `.avi`, `.mov`

### Storage Format
Files are stored in MinIO using the structure:
```
{camera-name}/{yyyy}/{mm}/{dd}/{detection_type}/{original_filename}.{ext}
```

### Example Upload
```bash
curl -X POST http://your-domain.com/api/cctv/upload \
  -F "file=@detection_image.jpg" \
  -F "cctv_name=camera001" \
  -F "detection_type=person" \
  -F "timestamp=2025-07-01T14:30:00Z"
```

### Response Example (Success)
```json
{
  "success": true,
  "message": "File uploaded successfully",
  "data": {
    "storage_path": "camera001/2025/07/01/person/detection_image.jpg",
    "cctv_name": "camera001",
    "detection_type": "person",
    "timestamp": "2025-07-01T14:30:00.000000Z",
    "file_size": "1.2 MB",
    "original_filename": "detection_image.jpg"
  }
}
```

### Response Example (Error)
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "file": ["The file field is required."],
    "cctv_name": ["The cctv name field is required."]
  }
}
```

## Download Endpoint

### Endpoint
`GET /api/archive/fetch/{camera_name}/{year}/{month}/{day}/{detection_type}/{filename}`

### Access
Public (no authentication required)

### URL Parameters
- `camera_name`: Name of the camera (must match upload cctv_name)
- `year`: 4-digit year (e.g., 2025)
- `month`: 2-digit month (e.g., 07)
- `day`: 2-digit day (e.g., 01)
- `detection_type`: Type of detection (e.g., person, vehicle)
- `filename`: Complete filename with extension

### Example Download
```bash
# Download a specific file
curl -O http://your-domain.com/api/archive/fetch/camera001/2025/07/01/person/detection_image.jpg

# Or use wget
wget http://your-domain.com/api/archive/fetch/camera001/2025/07/01/person/detection_image.jpg
```

### Response (Success)
- Status: 200 OK
- Headers: 
  - `Content-Type`: Original file MIME type
  - `Content-Disposition`: attachment; filename="..."
  - `Content-Length`: File size in bytes
- Body: Binary file content

### Response (File Not Found)
```json
{
  "success": false,
  "message": "File not found."
}
```

### Response (Server Error)
```json
{
  "success": false,
  "message": "Storage service error: [error details]"
}
```

## Complete Workflow Example

### 1. Upload a file
```bash
curl -X POST http://localhost:8000/api/cctv/upload \
  -F "file=@test_image.jpg" \
  -F "cctv_name=frontdoor_cam" \
  -F "detection_type=person"
```

### 2. Download the same file
If uploaded on July 1, 2025, the file would be accessible at:
```bash
curl -O http://localhost:8000/api/archive/fetch/frontdoor_cam/2025/07/01/person/test_image.jpg
```

## Error Codes

- **200**: Success (download)
- **201**: Success (upload)
- **400**: Bad request (validation error)
- **404**: File not found
- **500**: Internal server error

## Notes

1. **Camera Name Sanitization**: Camera names are sanitized for file paths (special characters replaced with underscores)
2. **File Organization**: Files are automatically organized by date and detection type
3. **No Authentication**: Both endpoints are public and do not require authentication
4. **Large Files**: Large files are streamed to avoid memory issues
5. **Logging**: All upload and download activities are logged for audit purposes

## Configuration

### Environment Variables
```env
# MinIO Configuration
MINIO_ENDPOINT="localhost:9000"
MINIO_ACCESS_KEY_ID="minioadmin"
MINIO_SECRET_ACCESS_KEY="minioadmin123"
MINIO_DEFAULT_REGION="us-east-1"
MINIO_BUCKET="detection-archive"
```

### Laravel Configuration
The endpoints use the `minio` disk configuration in `config/filesystems.php`:

```php
'minio' => [
    'driver' => 's3',
    'key' => env('MINIO_ACCESS_KEY_ID'),
    'secret' => env('MINIO_SECRET_ACCESS_KEY'),
    'region' => env('MINIO_DEFAULT_REGION', 'us-east-1'),
    'bucket' => env('MINIO_BUCKET', 'detection-archive'),
    'endpoint' => env('MINIO_ENDPOINT', 'http://localhost:9000'),
    'use_path_style_endpoint' => true,
    'throw' => false,
    'report' => false,
],
```
