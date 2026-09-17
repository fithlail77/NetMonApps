# NetMon Enterprise - API Documentation

**Version:** 1.0.0  
**Base URL:** `http://localhost:8000/api`  
**Authentication:** Laravel Sanctum (Bearer Token)  
**Content-Type:** `application/json`

---

## Table of Contents

1. [Authentication](#1-authentication)
2. [Users](#2-users)
3. [Devices](#3-devices)
4. [Alerts](#4-alerts)
5. [Metrics](#5-metrics)
6. [Topology](#6-topology)
7. [Error Handling](#7-error-handling)
8. [Pagination](#8-pagination)
9. [Role & Permissions](#9-role--permissions)

---

## 1. Authentication

### 1.1 Login

Authenticate user and receive access token.

```
POST /api/login
```

**Request Body:**

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| email | string | Yes | User email address |
| password | string | Yes | User password |
| device_name | string | Yes | Device identifier (e.g., "iPhone 15", "Samsung Galaxy S24") |

**Request Example:**

```json
{
    "email": "admin@netmon.com",
    "password": "password123",
    "device_name": "iPhone 15 Pro"
}
```

**Success Response (200):**

```json
{
    "user": {
        "id": 1,
        "name": "Admin User",
        "email": "admin@netmon.com",
        "role": "admin",
        "is_active": true,
        "last_login_at": "2026-09-17T07:00:00.000000Z",
        "created_at": "2026-01-01T00:00:00.000000Z",
        "updated_at": "2026-09-17T07:00:00.000000Z"
    },
    "token": "1|abc123def456ghi789..."
}
```

**Error Response (422):**

```json
{
    "message": "The provided credentials are incorrect.",
    "errors": {
        "email": ["Invalid credentials."]
    }
}
```

**Error Response - Deactivated Account (422):**

```json
{
    "message": "The provided credentials are incorrect.",
    "errors": {
        "email": ["Your account has been deactivated."]
    }
}
```

---

### 1.2 Logout

Invalidate current access token.

```
POST /api/logout
```

**Headers:**

| Key | Value |
|-----|-------|
| Authorization | Bearer {token} |

**Success Response (200):**

```json
{
    "message": "Logged out."
}
```

---

### 1.3 Get Current User

Get authenticated user profile.

```
GET /api/user
```

**Headers:**

| Key | Value |
|-----|-------|
| Authorization | Bearer {token} |

**Success Response (200):**

```json
{
    "id": 1,
    "name": "Admin User",
    "email": "admin@netmon.com",
    "role": "admin",
    "is_active": true,
    "last_login_at": "2026-09-17T07:00:00.000000Z",
    "created_at": "2026-01-01T00:00:00.000000Z",
    "updated_at": "2026-09-17T07:00:00.000000Z"
}
```

---

## 2. Users

### 2.1 Get User Profile

Same as [1.3 Get Current User](#13-get-current-user).

---

## 3. Devices

### 3.1 List Devices

Get paginated list of all devices.

```
GET /api/devices
```

**Headers:**

| Key | Value |
|-----|-------|
| Authorization | Bearer {token} |

**Query Parameters:**

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| status | string | No | Filter by status: `up`, `down`, `warning`, `unknown` |
| device_type_id | integer | No | Filter by device type ID |
| page | integer | No | Page number (default: 1) |
| per_page | integer | No | Items per page (default: 20) |

**Request Example:**

```
GET /api/devices?status=up&page=1
```

**Success Response (200):**

```json
{
    "current_page": 1,
    "data": [
        {
            "id": 1,
            "name": "Router Utama",
            "ip_address": "10.100.4.254",
            "device_type_id": 1,
            "snmp_community": "public",
            "snmp_version": "v1",
            "snmp_port": 161,
            "location": "Rack Server",
            "description": "Router Utama GUM",
            "status": "up",
            "last_seen_at": "2026-09-17T07:23:07.000000Z",
            "created_by": 1,
            "updated_by": 1,
            "created_at": "2026-09-15T20:47:04.000000Z",
            "updated_at": "2026-09-17T07:23:07.000000Z",
            "device_type": {
                "id": 1,
                "name": "Router",
                "icon": "router",
                "description": "Network Router"
            }
        }
    ],
    "first_page_url": "http://localhost:8000/api/devices?page=1",
    "from": 1,
    "last_page": 1,
    "last_page_url": "http://localhost:8000/api/devices?page=1",
    "links": [...],
    "next_page_url": null,
    "path": "http://localhost:8000/api/devices",
    "per_page": 20,
    "prev_page_url": null,
    "to": 1,
    "total": 2
}
```

---

### 3.2 Get Device Detail

Get detailed information about a specific device.

```
GET /api/devices/{device}
```

**Headers:**

| Key | Value |
|-----|-------|
| Authorization | Bearer {token} |

**URL Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| device | integer | Device ID |

**Success Response (200):**

```json
{
    "device": {
        "id": 1,
        "name": "Router Utama",
        "ip_address": "10.100.4.254",
        "device_type_id": 1,
        "snmp_community": "public",
        "snmp_version": "v1",
        "snmp_port": 161,
        "location": "Rack Server",
        "description": "Router Utama GUM",
        "status": "up",
        "last_seen_at": "2026-09-17T07:23:07.000000Z",
        "created_by": 1,
        "updated_by": 1,
        "created_at": "2026-09-15T20:47:04.000000Z",
        "updated_at": "2026-09-17T07:23:07.000000Z",
        "device_type": {
            "id": 1,
            "name": "Router",
            "icon": "router",
            "description": "Network Router"
        }
    },
    "metrics": {
        "latency": [
            {
                "id": 1,
                "device_id": 1,
                "metric_type": "latency",
                "value": 12.5,
                "unit": "ms",
                "recorded_at": "2026-09-17T07:23:07.000000Z"
            }
        ],
        "packet_loss": [...],
        "cpu_usage": [...],
        "memory_usage": [...]
    }
}
```

---

### 3.3 Get Device Metrics

Get metric data for a specific device (for chart rendering).

```
GET /api/devices/{device}/metrics
```

**Headers:**

| Key | Value |
|-----|-------|
| Authorization | Bearer {token} |

**URL Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| device | integer | Device ID |

**Query Parameters:**

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| metric_type | string | No | Metric type: `latency`, `packet_loss`, `cpu_usage`, `memory_usage` (default: `latency`) |
| hours | integer | No | Time range in hours (default: 24) |

**Request Example:**

```
GET /api/devices/1/metrics?metric_type=latency&hours=24
```

**Success Response (200):**

```json
{
    "labels": ["07:00", "07:01", "07:02", "07:03"],
    "values": [12.5, 11.8, 13.2, 12.0]
}
```

---

## 4. Alerts

### 4.1 List Alerts

Get paginated list of alerts.

```
GET /api/alerts
```

**Headers:**

| Key | Value |
|-----|-------|
| Authorization | Bearer {token} |

**Query Parameters:**

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| status | string | No | Filter by status: `triggered`, `acknowledged`, `resolved` (default: active alerts only) |
| severity | string | No | Filter by severity: `critical`, `warning`, `info` |
| page | integer | No | Page number (default: 1) |

**Request Example:**

```
GET /api/alerts?status=triggered&severity=critical
```

**Success Response (200):**

```json
{
    "current_page": 1,
    "data": [
        {
            "id": 1,
            "alert_rule_id": 1,
            "device_id": 2,
            "message": "Device is down - 10.100.7.18 is not responding",
            "severity": "critical",
            "status": "triggered",
            "triggered_at": "2026-09-17T06:57:58.000000Z",
            "acknowledged_at": null,
            "acknowledged_by": null,
            "resolved_at": null,
            "created_at": "2026-09-17T06:57:58.000000Z",
            "updated_at": "2026-09-17T06:57:58.000000Z",
            "device": {
                "id": 2,
                "name": "Switch Lantai 2",
                "ip_address": "10.100.7.18",
                "status": "down"
            },
            "alert_rule": {
                "id": 1,
                "name": "Device Down Alert",
                "metric_type": "status",
                "condition": "equals",
                "threshold": "down",
                "severity": "critical"
            }
        }
    ],
    "first_page_url": "http://localhost:8000/api/alerts?page=1",
    "from": 1,
    "last_page": 1,
    "last_page_url": "http://localhost:8000/api/alerts?page=1",
    "links": [...],
    "next_page_url": null,
    "path": "http://localhost:8000/api/alerts",
    "per_page": 20,
    "prev_page_url": null,
    "to": 1,
    "total": 1
}
```

---

### 4.2 Acknowledge Alert

Mark an alert as acknowledged.

```
POST /api/alerts/{alert}/acknowledge
```

**Headers:**

| Key | Value |
|-----|-------|
| Authorization | Bearer {token} |

**URL Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| alert | integer | Alert ID |

**Success Response (200):**

```json
{
    "message": "Alert acknowledged."
}
```

---

### 4.3 Resolve Alert

Mark an alert as resolved.

```
POST /api/alerts/{alert}/resolve
```

**Headers:**

| Key | Value |
|-----|-------|
| Authorization | Bearer {token} |

**URL Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| alert | integer | Alert ID |

**Success Response (200):**

```json
{
    "message": "Alert resolved."
}
```

---

## 5. Metrics

### 5.1 Get Metrics Data

Get metric data across all or specific devices.

```
GET /api/metrics
```

**Headers:**

| Key | Value |
|-----|-------|
| Authorization | Bearer {token} |

**Query Parameters:**

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| device_id | integer | No | Filter by specific device ID |
| metric_type | string | No | Metric type: `latency`, `packet_loss`, `cpu_usage`, `memory_usage` (default: `latency`) |
| hours | integer | No | Time range in hours (default: 24) |

**Request Example:**

```
GET /api/metrics?device_id=1&metric_type=cpu_usage&hours=6
```

**Success Response (200):**

```json
{
    "labels": ["07:00", "07:05", "07:10", "07:15"],
    "values": [45.2, 48.5, 42.1, 44.8]
}
```

---

## 6. Topology

### 6.1 Get Network Topology

Get network topology data (nodes and edges).

```
GET /api/topology
```

**Headers:**

| Key | Value |
|-----|-------|
| Authorization | Bearer {token} |

**Success Response (200):**

```json
{
    "nodes": [
        {
            "id": 1,
            "label": "Router Utama",
            "x": 200,
            "y": 150,
            "status": "up",
            "ip": "10.100.4.254",
            "type": "Router"
        },
        {
            "id": 2,
            "label": "Switch Lantai 2",
            "x": 400,
            "y": 250,
            "status": "down",
            "ip": "10.100.7.18",
            "type": "Switch"
        }
    ],
    "edges": [
        {
            "id": 1,
            "from": 1,
            "to": 2,
            "label": "Fiber Link",
            "status": "up"
        }
    ]
}
```

---

## 7. Error Handling

### 7.1 Validation Error (422)

```json
{
    "message": "The provided credentials are incorrect.",
    "errors": {
        "email": ["The email field is required."],
        "password": ["The password field is required."]
    }
}
```

### 7.2 Unauthorized Error (401)

```json
{
    "message": "Unauthenticated."
}
```

### 7.3 Forbidden Error (403)

```json
{
    "message": "This action is unauthorized."
}
```

### 7.4 Not Found Error (404)

```json
{
    "message": "No query results for model [App\\Models\\Device] 999."
}
```

### 7.5 Server Error (500)

```json
{
    "message": "Server error."
}
```

---

## 8. Pagination

All list endpoints return paginated results with the following structure:

```json
{
    "current_page": 1,
    "data": [...],
    "first_page_url": "http://localhost:8000/api/devices?page=1",
    "from": 1,
    "last_page": 5,
    "last_page_url": "http://localhost:8000/api/devices?page=5",
    "links": [
        {
            "url": null,
            "label": "&laquo; Previous",
            "active": false
        },
        {
            "url": "http://localhost:8000/api/devices?page=1",
            "label": "1",
            "active": true
        },
        {
            "url": "http://localhost:8000/api/devices?page=2",
            "label": "2",
            "active": false
        },
        {
            "url": null,
            "label": "Next &raquo;",
            "active": false
        }
    ],
    "next_page_url": "http://localhost:8000/api/devices?page=2",
    "path": "http://localhost:8000/api/devices",
    "per_page": 20,
    "prev_page_url": null,
    "to": 20,
    "total": 100
}
```

---

## 9. Role & Permissions

### 9.1 User Roles

| Role | Description |
|------|-------------|
| `admin` | Full access to all features |
| `network_engineer` | Can manage devices, view metrics, acknowledge/resolve alerts |
| `it_manager` | Can view dashboard, metrics, SLA reports |
| `helpdesk` | Can view alerts and basic device status |

### 9.2 API Access by Role

| Endpoint | Admin | Network Engineer | IT Manager | Helpdesk |
|----------|-------|------------------|------------|----------|
| Login/Logout | ✅ | ✅ | ✅ | ✅ |
| Get User | ✅ | ✅ | ✅ | ✅ |
| List Devices | ✅ | ✅ | ✅ | ✅ |
| Get Device | ✅ | ✅ | ✅ | ✅ |
| Get Device Metrics | ✅ | ✅ | ✅ | ❌ |
| List Alerts | ✅ | ✅ | ✅ | ✅ |
| Acknowledge Alert | ✅ | ✅ | ❌ | ❌ |
| Resolve Alert | ✅ | ✅ | ❌ | ❌ |
| Get Metrics | ✅ | ✅ | ✅ | ❌ |
| Get Topology | ✅ | ✅ | ✅ | ❌ |

---

## 10. Mobile Integration Guide

### 10.1 Authentication Flow

```
1. User opens app → Show Login Screen
2. User enters email + password → Call POST /api/login
3. Store token securely (Keychain/Keystore)
4. Add token to all subsequent requests: Authorization: Bearer {token}
5. On 401 error → Redirect to Login Screen
```

### 10.2 Recommended HTTP Client Setup

**Android (Kotlin/Retrofit):**

```kotlin
// Add interceptor for Authorization header
val client = OkHttpClient.Builder()
    .addInterceptor { chain ->
        val request = chain.request().newBuilder()
            .addHeader("Authorization", "Bearer $token")
            .addHeader("Content-Type", "application/json")
            .build()
        chain.proceed(request)
    }
    .build()

val retrofit = Retrofit.Builder()
    .baseUrl("http://your-server.com/api/")
    .client(client)
    .addConverterFactory(GsonConverterFactory.create())
    .build()
```

**iOS (Swift/URLSession):**

```swift
func makeRequest(url: String, method: String = "GET") -> URLRequest {
    var request = URLRequest(url: URL(string: "http://your-server.com/api/\(url)")!)
    request.httpMethod = method
    request.setValue("Bearer \(token)", forHTTPHeaderField: "Authorization")
    request.setValue("application/json", forHTTPHeaderField: "Content-Type")
    return request
}
```

### 10.3 Token Storage

**Android:** Use `EncryptedSharedPreferences` or `DataStore`

**iOS:** Use `Keychain`

### 10.4 Auto-Refresh Token

The current API does not support token refresh. When token expires (401 response), user must re-login.

### 10.5 Offline Handling

- Cache last known device status locally
- Show cached data when offline
- Sync when connection is restored
- Display "Last updated: X minutes ago" indicator

### 10.6 Push Notifications

For real-time alerts, integrate with:
- **Android:** Firebase Cloud Messaging (FCM)
- **iOS:** Apple Push Notification Service (APNS)

The server can send alerts via:
- Email (configured in Settings)
- Telegram Bot (configured in Settings)

---

## 11. Data Models

### 11.1 User

```json
{
    "id": "integer",
    "name": "string",
    "email": "string",
    "role": "enum: admin|network_engineer|it_manager|helpdesk",
    "is_active": "boolean",
    "avatar": "string|null",
    "last_login_at": "datetime|null",
    "created_at": "datetime",
    "updated_at": "datetime"
}
```

### 11.2 Device

```json
{
    "id": "integer",
    "name": "string",
    "ip_address": "string",
    "device_type_id": "integer",
    "snmp_community": "string|null",
    "snmp_version": "enum: v1|v2c|v3",
    "snmp_port": "integer",
    "location": "string|null",
    "description": "string|null",
    "status": "enum: up|down|warning|unknown",
    "last_seen_at": "datetime|null",
    "created_by": "integer|null",
    "updated_by": "integer|null",
    "created_at": "datetime",
    "updated_at": "datetime",
    "device_type": "DeviceType"
}
```

### 11.3 DeviceType

```json
{
    "id": "integer",
    "name": "string",
    "icon": "string|null",
    "description": "string|null"
}
```

### 11.4 Alert

```json
{
    "id": "integer",
    "alert_rule_id": "integer",
    "device_id": "integer",
    "message": "string",
    "severity": "enum: critical|warning|info",
    "status": "enum: triggered|acknowledged|resolved",
    "triggered_at": "datetime",
    "acknowledged_at": "datetime|null",
    "acknowledged_by": "integer|null",
    "resolved_at": "datetime|null",
    "created_at": "datetime",
    "updated_at": "datetime",
    "device": "Device",
    "alert_rule": "AlertRule"
}
```

### 11.5 AlertRule

```json
{
    "id": "integer",
    "name": "string",
    "device_id": "integer",
    "metric_type": "string",
    "condition": "enum: greater_than|less_than|equals|not_equals",
    "threshold": "float",
    "severity": "enum: critical|warning|info",
    "is_active": "boolean",
    "notify_email": "boolean",
    "notify_telegram": "boolean"
}
```

### 11.6 DeviceMetric

```json
{
    "id": "integer",
    "device_id": "integer",
    "metric_type": "string",
    "value": "float",
    "unit": "string|null",
    "recorded_at": "datetime"
}
```

### 11.7 TopologyNode

```json
{
    "id": "integer",
    "device_id": "integer",
    "label": "string|null",
    "x_position": "float",
    "y_position": "float"
}
```

### 11.8 TopologyEdge

```json
{
    "id": "integer",
    "source_node_id": "integer",
    "target_node_id": "integer",
    "label": "string|null",
    "status": "string"
}
```

---

## 12. Changelog

### v1.0.0 (2026-09-17)

- Initial API release
- Authentication (Login/Logout)
- Device listing and details
- Device metrics
- Alert management (List/Acknowledge/Resolve)
- Network topology
- Global metrics

---

**Last Updated:** September 17, 2026  
**Maintainer:** NetMon Enterprise Team
