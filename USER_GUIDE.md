# AI Control Panel - User Guide

## Overview

AI Control Panel is an advanced monitoring and security management system that combines CCTV surveillance, social media monitoring, and detection analysis capabilities. The system provides comprehensive tools for threat detection, data analysis, and automated response management.

## Table of Contents

1. [Authentication & Access](#authentication--access)
2. [Dashboard Overview](#dashboard-overview)
3. [CCTV Management](#cctv-management)
4. [Detection Archive](#detection-archive)
5. [Social Media Monitoring](#social-media-monitoring)
6. [WhatsApp Broadcasting](#whatsapp-broadcasting)
7. [User Management](#user-management)
8. [Storage & Settings](#storage--settings)
9. [API Documentation](#api-documentation)
10. [Troubleshooting](#troubleshooting)

---

## Authentication & Access

### User Roles

The system supports two user roles:

- **Admin**: Full access to all features including user management, settings, and advanced controls
- **User**: Limited access to viewing functions and basic monitoring

### Login Process

1. Navigate to `/login`
2. Enter your email and password
3. System automatically redirects based on your role:
   - Admins: Dashboard (`/dashboard`)
   - Users: Home page (`/home`)

### Features by Role

| Feature | Admin | User |
|---------|-------|------|
| Dashboard Analytics | ✅ | ❌ |
| CCTV Management | ✅ | ✅ (View Only) |
| Detection Archive | ✅ | ❌ |
| Social Media Monitoring | ✅ | ✅ (View Only) |
| User Management | ✅ | ❌ |
| System Settings | ✅ | ❌ |
| WhatsApp Broadcasting | ✅ | ❌ |

---

## Dashboard Overview

### Admin Dashboard (`/dashboard`)

The admin dashboard provides a comprehensive overview of system activities and statistics:

#### Key Features:
- **Recent Activities**: Last 6 system activities with timestamps
- **Activity Statistics**: Breakdown of different activity types
- **Quick Access**: Direct links to major system components
- **System Status**: Overview of connected services and storage

#### Activity Monitoring:
- User login/logout events
- CCTV camera operations (add, edit, delete)
- Detection events
- Configuration changes
- Social media scraping activities

### User Home (`/home`)

Limited dashboard for regular users focusing on monitoring capabilities:
- View CCTV cameras
- Access social media monitoring results
- Basic system status information

---

## CCTV Management

### Camera Management

#### Adding New Cameras (`/cctvs/create`)

**Required Information:**
- **Name**: Descriptive camera identifier
- **IP Address/URL**: Camera stream endpoint (supports both IP addresses and URLs)
- **Location**: Physical location description

**Process:**
1. Navigate to CCTV Management
2. Click "Add New Camera"
3. Fill in camera details
4. System validates connection
5. Camera added to external CCTV service

#### Camera Operations

| Operation | Route | Description |
|-----------|--------|-------------|
| View All | `/cctvs` | List all configured cameras |
| Add New | `/cctvs/create` | Add camera to system |
| Edit | `/cctvs/{id}/edit` | Modify camera settings |
| Delete | `/cctvs/{id}` | Remove camera from system |
| Stream | `/cctvs/{id}/stream` | Access live camera feed |
| Status | `/cctvs/{id}/status` | Check camera connectivity |

#### Camera Status Monitoring

- **Active**: Camera is online and streaming
- **Inactive**: Camera is offline or unreachable
- **Error**: Configuration or connection issues

### CCTV Settings (`/settings/cctv`)

Configure connection to external CCTV service:

**Configuration Options:**
- **Service URL**: External CCTV service endpoint
- **API Key**: Authentication for external service
- **Timeout Settings**: Connection timeout values
- **Retry Logic**: Failed connection retry parameters

**Connection Testing:**
- Built-in connection test functionality
- Validates API connectivity
- Confirms authentication credentials

---

## Detection Archive

### Overview (`/admin/detection-archive`)

The Detection Archive provides comprehensive access to all detection files stored in the system.

#### File Organization

Files are organized in a hierarchical structure:
```
{camera-name}/{yyyy}/{mm}/{dd}/{detection_type}/{filename}
```

**Example:**
```
Camera001/2025/07/05/person/detection_143052.jpg
EntranceCam/2025/07/05/vehicle/parking_violation.mp4
```

#### Filtering Options

**Camera Filter:**
- Show All Cameras
- Select Specific Camera
- Includes both identified and unidentified cameras

**Date Filter:**
- Specific Date: Files from selected date only
- Show All Dates: Files from all available dates

**Detection Type Filter:**
- All Types
- Person Detection
- Vehicle Detection
- Motion Detection
- Face Detection
- Package Detection
- Animal Detection
- Object Detection

**Time Range Filter:**
- All Day
- Morning (6:00 AM - 12:00 PM)
- Afternoon (12:00 PM - 6:00 PM)
- Evening (6:00 PM - 12:00 AM)
- Night (12:00 AM - 6:00 AM)

#### File Operations

**Preview (`/admin/detection-archive/preview`)**
- In-browser preview for images and videos
- Generates temporary secure URLs
- Supports multiple file formats

**Download (`/admin/detection-archive/download`)**
- Direct file download
- Preserves original filename
- Includes file size information

#### Camera Discovery

The system automatically discovers cameras from MinIO storage:
- **Identified Cameras**: Cameras configured in CCTV management
- **Unidentified Cameras**: Cameras found in storage but not configured

---

## Social Media Monitoring

### Instagram Scraper

#### Scraper Form (`/admin/scraper`)

**Required Parameters:**
- **Platform**: Instagram, X (Twitter), or Twitter
- **Account**: Select from configured dummy accounts
- **Suspected Account**: Target account username
- **Post Count**: Number of posts to analyze (minimum 1)
- **Comment Count**: Number of comments to analyze per post (minimum 1)

#### Dummy Account Management (`/dummy-accounts`)

**Account Configuration:**
- Username and password for platform access
- Platform association (Instagram, Twitter, X)
- Account status and validation

**Operations:**
- Add new dummy accounts
- Edit existing credentials
- Delete unused accounts
- View account usage history

#### Scraping Results

**Results View (`/admin/scraper-results`)**
- List of all scraping operations
- Individual result analysis
- Data export capabilities

**Result Data Includes:**
- Post captions and content
- Comment analysis
- URL references
- Engagement metrics
- Timestamp information

### Social Detection Results (`/admin/social-detection-results`)

Advanced analysis of scraped social media data:
- Pattern recognition
- Suspicious activity detection
- Cross-platform correlation
- Risk assessment scoring

---

## WhatsApp Broadcasting

### Sender Number Management (`/broadcast/sender-numbers`)

**Configuration:**
- Phone number setup
- Authentication tokens
- Status monitoring
- Rate limiting settings

### Broadcast Recipients (`/broadcast/broadcast-recipients`)

**Recipient Management:**
- Contact information
- Group assignments
- Delivery preferences
- Status tracking

### Broadcast Sending (`/broadcast/send`)

**Message Composition:**
- Text message creation
- Recipient selection
- Delivery scheduling
- Template usage

**Detection Integration:**
- Automatic alerts based on detection results
- Customizable message templates
- Emergency broadcast capabilities

---

## User Management

### User Administration (`/users`)

**User Operations:**
- Create new user accounts
- Edit user profiles
- Assign user roles (Admin/User)
- Deactivate accounts
- Password management

**User Information:**
- Name and email
- Role assignment
- Last login tracking
- Activity history

### Suspected Account Tracking (`/suspected-accounts`)

**Account Monitoring:**
- Suspicious account identification
- Cross-platform tracking
- Risk assessment
- Action history

---

## Storage & Settings

### Storage Settings (`/admin/storage-settings`)

**MinIO Configuration:**
- Endpoint configuration
- Access credentials
- Bucket management
- Connection testing

**Storage Features:**
- File organization
- Automatic backup
- Retention policies
- Space monitoring

### System Configuration

**Environment Settings:**
- Service endpoints
- API configurations
- Security settings
- Performance tuning

---

## API Documentation

### Public API Endpoints

#### CCTV Upload API

**Endpoint:** `POST /api/cctv/upload`

Upload detection files directly to the system.

**Parameters:**
- `file`: Media file (required)
- `cctv_name`: Camera identifier (required)
- `detection_type`: Type of detection (required)
- `timestamp`: ISO 8601 datetime (optional)

**Response:**
```json
{
  "success": true,
  "message": "File uploaded successfully",
  "data": {
    "storage_path": "camera001/2025/07/05/person/detection.jpg",
    "cctv_name": "Camera 001",
    "detection_type": "person",
    "timestamp": "2025-07-05T14:30:22.000000Z",
    "file_size": "2.4 MB"
  }
}
```

#### File Download API

**Endpoint:** `GET /api/archive/fetch/{camera_name}/{year}/{month}/{day}/{detection_type}/{filename}`

Direct file download from storage.

### Webhook Endpoints

#### CCTV Webhooks (`/api/webhooks/cctv/`)

- `POST camera-status`: Camera status changes
- `POST service-status`: Service status updates
- `POST detection-event`: Real-time detection events

---

## System Integration

### External Services

**CCTV Service Integration:**
- External camera management API
- Real-time status monitoring
- Stream redirection
- Detection event handling

**MinIO Storage:**
- Object storage for detection files
- Hierarchical file organization
- Secure access controls
- Scalable storage capacity

**Social Media APIs:**
- Instagram data scraping
- Twitter/X monitoring
- Cross-platform analysis
- Rate limiting compliance

### Data Flow

1. **Detection Events**: Cameras → CCTV Service → AI Control Panel
2. **File Storage**: Detection Files → MinIO → Archive Interface
3. **Social Monitoring**: Scrapers → Analysis → Results Database
4. **Notifications**: Events → WhatsApp Broadcasting → Recipients

---

## Troubleshooting

### Common Issues

#### CCTV Connection Problems

**Symptoms:**
- "Unable to connect to CCTV service"
- Cameras showing as offline
- Stream unavailable

**Solutions:**
1. Check CCTV service URL in settings
2. Verify API credentials
3. Test network connectivity
4. Review service logs

#### MinIO Storage Issues

**Symptoms:**
- File upload failures
- Archive not loading
- Download errors

**Solutions:**
1. Verify MinIO endpoint configuration
2. Check access credentials
3. Confirm bucket exists and is accessible
4. Test storage connection in settings

#### Social Media Scraping Failures

**Symptoms:**
- Scraper timeouts
- Authentication errors
- No results returned

**Solutions:**
1. Verify dummy account credentials
2. Check platform rate limits
3. Update account passwords if needed
4. Review scraper logs

### Log Files

**Location:** `storage/logs/laravel.log`

**Key Log Events:**
- Authentication attempts
- CCTV operations
- Storage transactions
- Scraping activities
- System errors

### Performance Optimization

**Database:**
- Regular cleanup of old activities
- Index optimization
- Query performance monitoring

**Storage:**
- File archival policies
- Storage space monitoring
- Backup strategies

**Network:**
- Connection pooling
- Timeout optimization
- Rate limiting

---

## Security Considerations

### Access Control

- Role-based permissions
- Session management
- Secure authentication
- Activity logging

### Data Protection

- Encrypted storage
- Secure transmission
- Access logging
- Retention policies

### API Security

- Input validation
- File type restrictions
- Size limitations
- Rate limiting

---

## System Requirements

### Server Requirements

- PHP 8.1 or higher
- Laravel 10.x
- MySQL/PostgreSQL database
- MinIO object storage
- Redis (recommended for caching)

### Client Requirements

- Modern web browser
- JavaScript enabled
- Minimum 1024x768 resolution
- Stable internet connection

### External Dependencies

- CCTV service API
- MinIO storage service
- Social media platform access
- WhatsApp Business API (for broadcasting)

---

## Support and Maintenance

### Regular Maintenance

- Database cleanup
- Log rotation
- Storage monitoring
- Security updates

### Monitoring

- System health checks
- Performance metrics
- Error tracking
- Usage analytics

### Backup and Recovery

- Database backups
- File storage backups
- Configuration backups
- Disaster recovery procedures

For technical support or questions, check the application logs at `storage/logs/laravel.log` and ensure all external services are properly configured and accessible.
