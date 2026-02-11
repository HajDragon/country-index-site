# Implementation Plan: N8N Webhook Service for Laravel Database Actions

## Overview
A reusable service layer that enables Laravel applications to perform database operations through N8N webhooks, providing external automation and workflow integration.

## Architecture Components

### 1. Service Layer
**File**: `app/Services/WebhookService.php`

**Responsibilities**:
- HTTP client wrapper for webhook requests
- Request/response formatting
- Error handling and logging
- Retry logic for failed requests
- Response parsing and validation

**Methods**:
- `getData(array $params)` - GET requests (retrieve operations)
- `insertData(array $data)` - POST requests (create/update operations)
- `deleteData(array $params)` - DELETE requests (delete operations)
- `testConnection()` - Health check endpoint

### 2. Configuration
**File**: `config/webhook.php`

**Settings**:
- `n8n_webhook_url` - Base webhook URL
- `timeout` - Request timeout (default: 30s)
- `retry_attempts` - Number of retry attempts
- `retry_delay` - Delay between retries
- `verify_ssl` - SSL verification toggle
- `logging_enabled` - Enable/disable request logging
- `debug_mode` - Verbose error messages

**Environment Variables**:
```env
N8N_WEBHOOK_URL=http://localhost:5678/webhook/database
N8N_WEBHOOK_TIMEOUT=30
N8N_WEBHOOK_RETRY=3
```

### 3. Service Provider
**File**: `app/Providers/WebhookServiceProvider.php`

**Purpose**:
- Register webhook service as singleton
- Load configuration
- Register any webhook-related bindings
- Setup event listeners for logging

### 4. Request/Response Contracts
**File**: `app/Contracts/WebhookInterface.php`

**Interface Definition**:
- Standardize method signatures
- Enable dependency injection
- Facilitate testing with mocks
- Support multiple webhook providers

### 5. Data Transfer Objects (DTOs)
**Files**: 
- `app/DTOs/WebhookRequest.php`
- `app/DTOs/WebhookResponse.php`

**Purpose**:
- Type-safe request building
- Response parsing and validation
- Data transformation layer
- Consistent structure across application

### 6. Exception Handling
**File**: `app/Exceptions/WebhookException.php`

**Custom Exceptions**:
- `WebhookConnectionException` - Connection failures
- `WebhookTimeoutException` - Timeout errors
- `WebhookValidationException` - Invalid data
- `WebhookNotFoundException` - 404 responses
- `WebhookServerException` - 5xx errors

### 7. Middleware (Optional)
**File**: `app/Http/Middleware/ValidateWebhookRequest.php`

**Purpose**:
- Validate incoming webhook data
- Rate limiting for webhook endpoints
- Authentication/authorization checks
- Request sanitization

### 8. Console Commands
**Files**:
- `app/Console/Commands/TestWebhookConnection.php`
- `app/Console/Commands/SyncDatabaseWithWebhook.php`

**Commands**:
```bash
php artisan webhook:test
php artisan webhook:sync {model}
```

### 9. Logging Strategy
**Implementation**:
- Dedicated log channel for webhook operations
- Log all requests/responses in development
- Log only errors in production
- Include request ID for tracing
- Performance metrics logging

**Log Structure**:
```php
[
    'webhook_request' => [
        'id' => 'uuid',
        'url' => 'endpoint',
        'method' => 'POST',
        'payload' => [],
        'timestamp' => 'datetime'
    ],
    'webhook_response' => [
        'id' => 'uuid',
        'status' => 200,
        'duration' => '150ms',
        'data' => []
    ]
]
```

### 10. Database Migrations (Optional)
**File**: `database/migrations/xxxx_create_webhook_logs_table.php`

**Purpose**:
- Store webhook request history
- Audit trail for operations
- Debug failed requests
- Analytics and monitoring

**Schema**:
- `id`, `request_id`, `endpoint`, `method`
- `payload`, `response`, `status_code`
- `duration_ms`, `error_message`
- `created_at`, `updated_at`

## Installation Steps

### Step 1: Install Dependencies
```bash
composer require guzzlehttp/guzzle
```

### Step 2: Publish Configuration
```bash
php artisan vendor:publish --tag=webhook-config
```

### Step 3: Setup Environment
Add webhook URL and settings to `.env`

### Step 4: Register Service Provider
Add to `config/app.php` providers array (or auto-discovery)

### Step 5: Create Service Class
Implement WebhookService with HTTP client wrapper

### Step 6: Setup N8N Workflows
- Import workflow JSON
- Configure database connections
- Set webhook URLs
- Test endpoints

## N8N Workflow Structure

### Required Nodes:
1. **Webhook Trigger** - Entry point for Laravel requests
2. **Function Node** - Parse incoming request, extract action
3. **Switch Node** - Route based on action type
4. **MySQL Nodes** - Execute database operations per action
5. **Response Node** - Format and return results

### Action Mapping:
- `get_all` → SELECT query with optional limit
- `get_by_id` → SELECT WHERE id = ?
- `create` → INSERT with validation
- `update` → UPDATE WHERE id = ?
- `delete` → DELETE WHERE id = ?
- `test_connection` → Simple ping/pong

### Response Format:
```json
{
    "success": true,
    "data": {},
    "status": 200,
    "error": null,
    "message": "Operation completed"
}
```

## Security Considerations

### Authentication:
- API tokens in headers
- HMAC signature verification
- IP whitelist in N8N
- Rate limiting per IP/token

### Data Validation:
- Input sanitization in service
- Database constraints in N8N
- Response validation before returning
- SQL injection prevention

### Error Exposure:
- Hide sensitive data in production
- Generic error messages to client
- Detailed logs server-side only
- No stack traces in API responses

## Testing Strategy

### Unit Tests:
- Test service methods in isolation
- Mock HTTP client responses
- Validate request formatting
- Test exception handling

### Integration Tests:
- Test full webhook flow
- Use test N8N instance
- Verify database changes
- Test failure scenarios

### Feature Tests:
- Test business logic with webhook
- End-to-end workflows
- Performance benchmarks
- Load testing

## Usage Examples

### Controller Usage:
```php
public function __construct(
    private WebhookService $webhookService
) {}

public function index() {
    $result = $this->webhookService->getData([
        'action' => 'get_all',
        'limit' => 10
    ]);
}
```

### Command Usage:
```php
public function handle(WebhookService $service) {
    $result = $service->testConnection();
}
```

### Facade Usage (Optional):
```php
Webhook::getData(['action' => 'get_all']);
```

## Monitoring & Maintenance

### Health Checks:
- Scheduled connection tests
- Response time monitoring
- Error rate tracking
- Alert on failures

### Performance:
- Cache frequent queries
- Async processing for bulk operations
- Queue long-running requests
- Optimize N8N workflows

### Documentation:
- API endpoint documentation
- Action parameter reference
- Response format specifications
- Error code definitions
- Integration examples

## Scalability Considerations

- Queue webhook requests for bulk operations
- Implement circuit breaker pattern
- Add Redis caching layer
- Load balance N8N instances
- Database connection pooling
- Horizontal scaling of N8N workers

## File Structure Summary

```
app/
├── Services/
│   └── WebhookService.php
├── Contracts/
│   └── WebhookInterface.php
├── DTOs/
│   ├── WebhookRequest.php
│   └── WebhookResponse.php
├── Exceptions/
│   └── WebhookException.php
├── Http/
│   └── Middleware/
│       └── ValidateWebhookRequest.php
├── Console/
│   └── Commands/
│       ├── TestWebhookConnection.php
│       └── SyncDatabaseWithWebhook.php
└── Providers/
    └── WebhookServiceProvider.php

config/
└── webhook.php

database/
└── migrations/
    └── xxxx_create_webhook_logs_table.php

tests/
├── Unit/
│   └── WebhookServiceTest.php
├── Feature/
│   └── WebhookIntegrationTest.php
└── Integration/
    └── N8NWorkflowTest.php
```

## Quick Start Guide

### 1. Copy Core Files
Copy the following from your current implementation:
- `app/Services/WebhookService.php`
- N8N workflow JSON files

### 2. Update Environment
```env
N8N_WEBHOOK_URL=your_webhook_url
N8N_WEBHOOK_TIMEOUT=30
```

### 3. Create Config File
```php
// config/webhook.php
return [
    'url' => env('N8N_WEBHOOK_URL', 'http://localhost:5678/webhook'),
    'timeout' => env('N8N_WEBHOOK_TIMEOUT', 30),
    'retry_attempts' => env('N8N_WEBHOOK_RETRY', 3),
    'verify_ssl' => env('N8N_WEBHOOK_VERIFY_SSL', true),
];
```

### 4. Register Service
```php
// In AppServiceProvider or dedicated WebhookServiceProvider
$this->app->singleton(WebhookService::class, function ($app) {
    return new WebhookService(
        config('webhook.url'),
        config('webhook.timeout')
    );
});
```

### 5. Use in Controllers/Services
```php
use App\Services\WebhookService;

class YourController extends Controller
{
    public function __construct(
        private WebhookService $webhookService
    ) {}
    
    public function index()
    {
        $bikes = $this->webhookService->getData([
            'action' => 'get_all',
            'limit' => 10
        ]);
        
        return response()->json($bikes);
    }
}
```

## Best Practices

1. **Always validate input** before sending to webhook
2. **Handle errors gracefully** with try-catch blocks
3. **Log all webhook operations** for debugging
4. **Use queues** for non-blocking operations
5. **Implement retry logic** for transient failures
6. **Cache responses** when appropriate
7. **Monitor performance** and response times
8. **Version your webhook API** for backward compatibility
9. **Document all actions** and their parameters
10. **Test thoroughly** before production deployment
