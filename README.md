# Laravel Copernica REST Client for PHP

A fluent, modern PHP (Laravel) client for the [Copernica Marketing Software](https://www.copernica.com) REST API v4. Built with clean architecture, strongly-typed DTOs, and first-class Laravel support.

## Requirements

- PHP 8.3 or higher
- [Composer](https://getcomposer.org)
- A Copernica account with a REST API v4 access token - Generate your [access_token](https://copernica.com)
- GuzzleHTTP 7.0+ (installed automatically via Composer)

## Installation

```bash
composer require budgetlens/laravel-copernica-rest-client
```

### Laravel

The package supports Laravel auto-discovery. After installation, add your access token to `.env`:

```env
COPERNICA_ACCESS_TOKEN=your-api-token-here
```

Optionally publish the configuration file:

```bash
php artisan vendor:publish --provider="Budgetlens\Copernica\RestClient\CopernicaServiceProvider"
```

This publishes `config/copernica.php` where you can configure:

| Option | Env Variable | Default |
|---|---|---|
| `access_token` | `COPERNICA_ACCESS_TOKEN` | `""` |
| `base_url` | `COPERNICA_BASE_URL` | `https://api.copernica.com/v4` |
| `timeout` | `COPERNICA_TIMEOUT` | `30` |

> **Tip:** Use `https://rest.copernica.com/v4` as base URL for large dataset retrieval.

### Standalone (without Laravel)

```php
use Budgetlens\Copernica\RestClient\CopernicaClient;

$client = new CopernicaClient(
    accessToken: 'your-api-token-here',
    baseUrl: 'https://api.copernica.com/v4', // optional
    timeout: 30, // optional
);
```

## Quick Start

```php
// Laravel (via Facade)
use Budgetlens\Copernica\RestClient\Facades\Copernica;

$databases = Copernica::databases()->list();

// Laravel (via dependency injection)
use Budgetlens\Copernica\RestClient\CopernicaClient;

public function index(CopernicaClient $copernica)
{
    $databases = $copernica->databases()->list();
}

// Standalone
$client = new CopernicaClient('your-token');
$databases = $client->databases()->list();
```

## Usage

### Account Information

```php
// Get account identity
$identity = $client->identity();

// Get API consumption/usage
$consumption = $client->consumption();
```

### Databases

```php
// List all databases
$databases = $client->databases()->list();

// Get a single database
$database = $client->database(123)->get();

echo $database->name;
echo $database->description;

// Create a new database
$id = $client->databases()->create([
    'name' => 'Newsletter',
    'description' => 'Newsletter subscribers',
]);

// Update a database
$client->database(123)->update([
    'description' => 'Updated description',
]);

// Copy a database
$newId = $client->database(123)->copy([
    'name' => 'Newsletter Copy',
]);

// Delete a database
$client->database(123)->delete();
```

### Fields

```php
// List database fields
$fields = $client->database(123)->fields()->list();

// Create a field
$fieldId = $client->database(123)->fields()->create([
    'name' => 'email',
    'type' => 'email',
]);

// Update a field
$client->database(123)->fields()->update($fieldId, [
    'name' => 'email_address',
]);

// Delete a field
$client->database(123)->fields()->delete($fieldId);

// Collection fields work the same way
$fields = $client->database(123)->collection(456)->fields()->list();
```

### Profiles

```php
// List profiles in a database
$profiles = $client->database(123)->profiles()->list();

// Paginated iteration (lazy-loads all pages)
foreach ($client->database(123)->profiles()->each() as $profile) {
    echo $profile->field('email');
}

// Filter profiles by field values
$results = $client->database(123)->profiles()->where('email', 'john@example.com');

// Filter by multiple fields
$results = $client->database(123)->profiles()->where([
    'city' => 'Amsterdam',
    'newsletter' => 'yes',
]);

// Get a single profile
$profile = $client->database(123)->profile(456)->get();

echo $profile->id;
echo $profile->field('email');
echo $profile->fields;    // all fields as array
echo $profile->interests; // interests array
echo $profile->createdAt; // DateTimeImmutable

// Create a profile
$profileId = $client->database(123)->profiles()->create([
    'fields' => [
        'email' => 'john@example.com',
        'firstname' => 'John',
        'lastname' => 'Doe',
    ],
    'interests' => [
        'newsletter' => true,
    ],
]);

// Update a profile
$client->database(123)->profile(456)->update([
    'fields' => [
        'lastname' => 'Smith',
    ],
]);

// Get/update profile fields directly
$fields = $client->database(123)->profile(456)->fields();
$client->database(123)->profile(456)->updateFields(['city' => 'Rotterdam']);

// Get/update interests
$interests = $client->database(123)->profile(456)->interests();
$client->database(123)->profile(456)->updateInterests(['newsletter' => true]);

// Delete a profile
$client->database(123)->profile(456)->delete();
```

### Subprofiles

```php
// List subprofiles of a profile (optionally scoped to a collection)
$subprofiles = $client->database(123)->profile(456)->subprofiles()->list();
$subprofiles = $client->database(123)->profile(456)->subprofiles(collectionId: 789)->list();

// List subprofiles in a collection
$subprofiles = $client->database(123)->collection(789)->subprofiles()->list();

// Paginated iteration
foreach ($client->database(123)->profile(456)->subprofiles()->each() as $subprofile) {
    echo $subprofile->field('order_number');
}

// Get a single subprofile
$subprofile = $client->database(123)->profile(456)->subprofile(101)->get();

// Create a subprofile
$subId = $client->database(123)->profile(456)->subprofiles()->create([
    'fields' => [
        'order_number' => 'ORD-001',
        'amount' => '49.95',
    ],
]);

// Update a subprofile
$client->database(123)->profile(456)->subprofile(101)->update([
    'fields' => ['amount' => '59.95'],
]);

// Delete a subprofile
$client->database(123)->profile(456)->subprofile(101)->delete();
```

### Collections

```php
// List collections in a database
$collections = $client->database(123)->collections()->list();

// Get a single collection
$collection = $client->database(123)->collection(456)->get();

// Create a collection
$id = $client->database(123)->collections()->create([
    'name' => 'Orders',
]);

// Update a collection
$client->database(123)->collection(456)->update([
    'name' => 'Purchase Orders',
]);

// Manage unsubscribe behavior
$settings = $client->database(123)->collection(456)->unsubscribe();
$client->database(123)->collection(456)->setUnsubscribe([
    'behavior' => 'update',
    'field' => 'newsletter',
]);
```

### Views

```php
// List views for a database
$views = $client->database(123)->views()->list();

// Get a single view
$view = $client->database(123)->view(456)->get();

// Create a view
$viewId = $client->database(123)->views()->create([
    'name' => 'Active subscribers',
    'description' => 'All active newsletter subscribers',
]);

// Get profiles from a view
$profiles = $client->database(123)->view(456)->profiles()->list();

// Paginate profiles in a view
foreach ($client->database(123)->view(456)->eachProfile() as $profile) {
    // ...
}

// Get profile IDs from a view
$ids = $client->database(123)->view(456)->profileIds();

// Manage view rules
$rules = $client->database(123)->view(456)->rules();
$ruleId = $client->database(123)->view(456)->createRule([
    'name' => 'Is active',
    'conditions' => [
        ['type' => 'field', 'field' => 'active', 'value' => 'yes'],
    ],
]);

// Rebuild a view
$client->database(123)->view(456)->rebuild();

// Update / delete
$client->database(123)->view(456)->update(['name' => 'Renamed view']);
$client->database(123)->view(456)->delete();
```

### Emailings

```php
// --- HTML Emailings ---

// List all HTML emailings
$emailings = $client->emailings()->list();

// Paginate emailings
foreach ($client->emailings()->each() as $emailing) {
    echo $emailing->subject;
}

// Get scheduled emailings
$scheduled = $client->emailings()->scheduled();

// Get a single emailing
$emailing = $client->emailing(123)->get();

echo $emailing->id;
echo $emailing->subject;
echo $emailing->fromAddress;

// Create an emailing
$id = $client->emailings()->create([
    'subject' => 'Monthly newsletter',
    'database' => 123,
    'template' => 456,
]);

// --- Drag & Drop Emailings ---

$emailings = $client->dragAndDropEmailings()->list();
$emailing = $client->dragAndDropEmailing(123)->get();

// --- Statistics ---

$stats = $client->emailing(123)->statistics();

echo $stats->destinations;
echo $stats->deliveries;
echo $stats->impressions;
echo $stats->clicks;
echo $stats->unsubscribes;
echo $stats->abuses;
echo $stats->errors;

// --- Destinations ---

$destinations = $client->emailing(123)->destinations();

// Paginate destinations
foreach ($client->emailing(123)->eachDestination() as $dest) {
    echo $dest->profileId;
    echo $dest->timestamp; // DateTimeImmutable
}

// --- Result details ---

$deliveries   = $client->emailing(123)->deliveries();
$impressions  = $client->emailing(123)->impressions();
$clicks       = $client->emailing(123)->clicks();
$errors       = $client->emailing(123)->errors();
$unsubscribes = $client->emailing(123)->unsubscribes();
$abuses       = $client->emailing(123)->abuses();

// Get the emailing snapshot (the email as it was sent)
$snapshot = $client->emailing(123)->snapshot();
```

### Webhooks

```php
// List all webhooks
$webhooks = $client->webhooks()->list();

// Get a single webhook
$webhook = $client->webhook(123)->get();

echo $webhook->handler;
echo $webhook->url;
echo $webhook->trigger;

// Create a webhook
$id = $client->webhooks()->create([
    'handler' => 'profile',
    'url' => 'https://example.com/webhook',
    'trigger' => 'create',
    'database' => 123,
]);

// Update a webhook
$client->webhook(123)->update([
    'url' => 'https://example.com/webhook-v2',
]);

// Delete a webhook
$client->webhook(123)->delete();
```

### Raw API Access

For endpoints not yet covered by the client, you can use the raw HTTP methods:

```php
$response = $client->get('some/endpoint', ['param' => 'value']);
$response = $client->post('some/endpoint', ['key' => 'value']);
$response = $client->put('some/endpoint', ['key' => 'value']);
$response = $client->delete('some/endpoint');
```

## Error Handling

The client throws specific exceptions for different error scenarios:

```php
use Budgetlens\Copernica\RestClient\Exceptions\AuthenticationException;
use Budgetlens\Copernica\RestClient\Exceptions\NotFoundException;
use Budgetlens\Copernica\RestClient\Exceptions\ValidationException;
use Budgetlens\Copernica\RestClient\Exceptions\RateLimitException;
use Budgetlens\Copernica\RestClient\Exceptions\CopernicaException;

try {
    $profile = $client->database(123)->profile(999)->get();
} catch (AuthenticationException $e) {
    // 401/403 - Invalid or expired access token
} catch (NotFoundException $e) {
    // 404 - Resource not found
} catch (ValidationException $e) {
    // 400/422 - Invalid request data
    $details = $e->errors; // detailed error info from the API
} catch (RateLimitException $e) {
    // 429 - Too many requests
} catch (CopernicaException $e) {
    // Any other API error
}
```

All exceptions extend `CopernicaException`, which extends `RuntimeException`. You can catch `CopernicaException` to handle all API errors at once.

## DTOs

All API responses are mapped to strongly-typed Data Transfer Objects:

| DTO | Description |
|---|---|
| `Database` | Database with ID, name, description, archived status, fields, interests, collections |
| `Collection` | Database collection with ID, name, parent database, and fields |
| `Field` | Database/collection field with type, length, and display settings |
| `Profile` | Contact record with fields, interests, and timestamps |
| `Subprofile` | Collection subprofile with fields and timestamps |
| `View` | Database view with rules and rebuild status |
| `Emailing` | Email campaign (HTML or Drag & Drop) with content details |
| `EmailingDestination` | Single emailing recipient with profile/subprofile ID |
| `EmailingStatistics` | Aggregated statistics (deliveries, impressions, clicks, etc.) |
| `Webhook` | Webhook subscription with handler, URL, and trigger type |

All DTOs implement the `FromArray` contract and are immutable (`readonly` properties).

## Architecture

```
src/
├── CopernicaClient.php          # Main entry point
├── CopernicaServiceProvider.php # Laravel service provider
├── Facades/
│   └── Copernica.php            # Laravel facade
├── Http/
│   ├── CopernicaHttpClient.php  # Guzzle-based HTTP layer
│   └── PaginatedResponse.php    # Lazy-loading paginator
├── Resources/
│   ├── Resource.php             # Abstract base resource
│   ├── DatabaseResource.php     # /database endpoints
│   ├── ProfileResource.php      # /profile endpoints
│   ├── SubprofileResource.php   # /subprofile endpoints
│   ├── CollectionResource.php   # /collection endpoints
│   ├── FieldResource.php        # Field management
│   ├── ViewResource.php         # /view endpoints
│   ├── EmailingResource.php     # Emailing endpoints
│   └── WebhookResource.php      # /webhook endpoints
├── DTOs/
│   ├── Contracts/FromArray.php  # DTO factory contract
│   ├── Concerns/ParsesDate.php  # Date parsing trait
│   ├── Database.php
│   ├── Collection.php
│   ├── Field.php
│   ├── Profile.php
│   ├── Subprofile.php
│   ├── View.php
│   ├── Emailing.php
│   ├── EmailingDestination.php
│   ├── EmailingStatistics.php
│   └── Webhook.php
└── Exceptions/
    ├── CopernicaException.php        # Base exception
    ├── AuthenticationException.php   # 401/403
    ├── NotFoundException.php         # 404
    ├── ValidationException.php       # 400/422
    └── RateLimitException.php        # 429
```

## Testing

```bash
composer test
```

Tests use PHPUnit 11 and Orchestra Testbench for Laravel integration testing.

## License

MIT License. See [LICENSE](LICENSE) for details.
