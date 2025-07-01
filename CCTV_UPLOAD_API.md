# CCTV Upload API Documentation

## Overview

The CCTV Upload API provides a public endpoint for third-party clients to upload detection files (images/videos) directly to the detection archive system. Files are automatically stored in MinIO with a structured path format for easy organization and retrieval.

## Endpoint Details

**URL:** `POST /api/cctv/upload`  
**Access:** Public (no authentication required)  
**Content-Type:** `multipart/form-data`  
**Max File Size:** 100MB

## Parameters

### Required Parameters

| Parameter | Type | Description | Example |
|-----------|------|-------------|---------|
| `file` | File | Media file to upload (image/video) | `detection.jpg` |
| `cctv_name` | String | Camera name/identifier | `"Camera 001"` |
| `detection_type` | String | Type of detection | `"person"` |

### Optional Parameters

| Parameter | Type | Description | Example |
|-----------|------|-------------|---------|
| `timestamp` | String | ISO 8601 datetime string | `"2025-07-01T14:30:22Z"` |

### Supported File Types

- **Images:** jpg, jpeg, png
- **Videos:** mp4, avi, mov

### Detection Types

- `person`
- `vehicle` 
- `motion`
- `face`
- `package`
- `animal`
- `object`

## Storage Format

Files are stored in MinIO using the following path structure:

```
{camera-name}/{yyyy}/{mm}/{dd}/{detection_type}/{original_filename}.{ext}
```

**Examples:**
- `camera001/2025/07/01/person/detection_image.jpg`
- `entrance_cam/2025/07/01/vehicle/car_detected.mp4`
- `back_door/2025/07/01/motion/motion_alert.png`

## Response Format

### Success Response (HTTP 201)

```json
{
  "success": true,
  "message": "File uploaded successfully",
  "data": {
    "storage_path": "camera001/2025/07/01/person/detection.jpg",
    "cctv_name": "Camera 001",
    "detection_type": "person",
    "timestamp": "2025-07-01T14:30:22.000000Z",
    "file_size": "2.4 MB",
    "original_filename": "detection.jpg"
  }
}
```

### Error Response (HTTP 400/500)

```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "detection_type": ["The selected detection type is invalid."],
    "file": ["The file field is required."]
  }
}
```

## Usage Examples

### cURL Example

```bash
curl -X POST \
  http://localhost:8000/api/cctv/upload \
  -F "file=@/path/to/detection.jpg" \
  -F "cctv_name=Camera 001" \
  -F "detection_type=person" \
  -F "timestamp=2025-07-01T14:30:22Z"
```

### PHP Example

```php
<?php
$ch = curl_init();

curl_setopt_array($ch, [
    CURLOPT_URL => 'http://localhost:8000/api/cctv/upload',
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => [
        'file' => new CURLFile('/path/to/detection.jpg', 'image/jpeg', 'detection.jpg'),
        'cctv_name' => 'Camera 001',
        'detection_type' => 'person',
        'timestamp' => '2025-07-01T14:30:22Z'
    ],
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => ['Accept: application/json']
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$data = json_decode($response, true);
if ($httpCode === 201 && $data['success']) {
    echo "Upload successful: " . $data['data']['storage_path'];
} else {
    echo "Upload failed: " . $data['message'];
}
?>
```

### JavaScript/Node.js Example

```javascript
const fs = require('fs');
const FormData = require('form-data');
const fetch = require('node-fetch');

const form = new FormData();
form.append('file', fs.createReadStream('/path/to/detection.jpg'));
form.append('cctv_name', 'Camera 001');
form.append('detection_type', 'person');
form.append('timestamp', new Date().toISOString());

fetch('http://localhost:8000/api/cctv/upload', {
    method: 'POST',
    body: form
})
.then(response => response.json())
.then(data => {
    if (data.success) {
        console.log('Upload successful:', data.data.storage_path);
    } else {
        console.error('Upload failed:', data.message);
    }
})
.catch(error => console.error('Error:', error));
```

### Python Example

```python
import requests
from datetime import datetime

url = 'http://localhost:8000/api/cctv/upload'

files = {'file': ('detection.jpg', open('/path/to/detection.jpg', 'rb'), 'image/jpeg')}
data = {
    'cctv_name': 'Camera 001',
    'detection_type': 'person',
    'timestamp': datetime.now().isoformat() + 'Z'
}

response = requests.post(url, files=files, data=data)

if response.status_code == 201:
    result = response.json()
    print(f"Upload successful: {result['data']['storage_path']}")
else:
    print(f"Upload failed: {response.text}")
```

## Error Handling

### Common Error Scenarios

1. **File Too Large (HTTP 413)**
   - File exceeds 100MB limit
   - Solution: Compress or reduce file size

2. **Invalid File Type (HTTP 400)**
   - File extension not supported
   - Solution: Convert to supported format (jpg, jpeg, png, mp4, avi, mov)

3. **Missing Required Fields (HTTP 400)**
   - Required parameters not provided
   - Solution: Include all required fields

4. **Invalid Detection Type (HTTP 400)**
   - Detection type not in allowed list
   - Solution: Use valid detection type

5. **Storage Error (HTTP 500)**
   - MinIO connection or storage issue
   - Solution: Check MinIO configuration and connectivity

## Security Considerations

- **No Authentication Required:** This is a public endpoint designed for third-party integration
- **File Validation:** Only specific file types and sizes are allowed
- **Path Sanitization:** Camera names are sanitized for safe file system usage
- **Logging:** All uploads are logged for monitoring and debugging

## MinIO Configuration

Ensure MinIO is properly configured in your Laravel `.env` file:

```env
MINIO_ENDPOINT=localhost:9000
MINIO_ACCESS_KEY=minioadmin
MINIO_SECRET_KEY=minioadmin
MINIO_BUCKET=detection-archive
MINIO_REGION=us-east-1
MINIO_USE_SSL=false
```

## Testing the Endpoint

Use the provided test scripts to validate functionality:

1. **Configuration Test:** `php test_cctv_upload_api.php`
2. **Functional Test:** `php test_cctv_upload_functional.php`

## Integration Checklist

- [ ] MinIO server is running and accessible
- [ ] MinIO credentials are configured in `.env`
- [ ] Laravel application is running
- [ ] Test upload with sample file
- [ ] Verify file appears in MinIO bucket
- [ ] Check file appears in Detection Archive UI
- [ ] Implement error handling in client code
- [ ] Add logging/monitoring for uploads

## Monitoring and Logs

All upload activities are logged in Laravel logs with the following information:

- Upload success/failure
- File details (size, type, original name)
- Storage path
- Timestamp and camera information
- Any errors encountered

Check logs at: `storage/logs/laravel.log`

## Rate Limiting

Currently no rate limiting is implemented. Consider adding rate limiting for production environments if needed.

## Support

For technical support or questions about the CCTV Upload API, check the application logs and ensure MinIO connectivity is working properly.
