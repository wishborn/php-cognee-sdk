# Cognee PHP SDK (Unofficial)

**Unofficial** PHP SDK for [Cognee](https://github.com/topoteretes/cognee) - Transform raw data into persistent AI memory for agents.

> **Note:** This is a community-maintained SDK for personal use and is not officially affiliated with or endorsed by the Cognee project.

[![PHP Version](https://img.shields.io/badge/php-%5E8.4-blue)](https://www.php.net/)
[![PSR-12](https://img.shields.io/badge/code%20style-PSR--12-green)](https://www.php-fig.org/psr/psr-12/)
[![License](https://img.shields.io/badge/license-MIT-blue)](LICENSE)
[![Tests](https://img.shields.io/badge/tests-46%20passing-brightgreen)](https://github.com/mikewolfxyou/php-cognee-sdk)
[![Unofficial](https://img.shields.io/badge/status-unofficial-orange)](https://github.com/mikewolfxyou/php-cognee-sdk)

## Features

- Full API coverage for Cognee self-hosted instances
- Type-safe models and requests using PHP 8.4 features
- Comprehensive exception handling
- PSR-12 compliant code
- >90% test coverage
- Automatic retry with exponential backoff
- Resource-based architecture for clean API

## Requirements

- PHP 8.4 or higher
- Composer
- A running Cognee instance

## Installation

Install the SDK using Composer:

```bash
composer require mikewolfxyou/php-cognee-sdk
```

## Quick Start

```php
<?php

require 'vendor/autoload.php';

use Cognee\Client;
use Cognee\Config;
use Cognee\Requests\AddDataRequest;
use Cognee\Requests\CognifyRequest;
use Cognee\Requests\SearchRequest;
use Cognee\Enums\SearchType;

// Configure the client
$config = new Config(
    baseUrl: 'http://localhost:8000',
    apiKey: 'your-api-key',
);

$client = new Client($config);

// Create a dataset
$dataset = $client->datasets()->create('my-dataset');

// Add data
$addRequest = new AddDataRequest(
    data: 'Your text content here',
    datasetId: $dataset->id,
);
$client->datasets()->add($addRequest);

// Cognify (process into knowledge graph)
$cognifyRequest = new CognifyRequest(
    datasetIds: [$dataset->id],
);
$client->datasets()->cognify($cognifyRequest);

// Search the knowledge graph
$searchRequest = new SearchRequest(
    query: 'your search query',
    searchType: SearchType::SEMANTIC,
    datasetIds: [$dataset->id],
    topK: 10,
);
$results = $client->search()->search($searchRequest);

foreach ($results->results as $result) {
    echo $result->text . " (score: {$result->score})\n";
}
```

## Configuration

### Basic Configuration

```php
use Cognee\Config;

$config = new Config(
    baseUrl: 'http://localhost:8000',  // Your Cognee instance URL
    apiKey: 'your-api-key',            // Your API key
    timeout: 30,                        // Request timeout in seconds (default: 30)
    retryAttempts: 3,                   // Number of retry attempts (default: 3)
);
```

### Updating Configuration

Configuration objects are immutable. Create a new instance with updated values:

```php
$newConfig = $config->with(['timeout' => 60]);
```

## Usage

### Datasets

#### Create a Dataset

```php
$dataset = $client->datasets()->create(
    name: 'my-dataset',
    metadata: ['description' => 'My dataset description'],
);
```

#### List Datasets

```php
$datasets = $client->datasets()->list();

foreach ($datasets as $dataset) {
    echo $dataset->name . "\n";
}
```

#### Get Dataset

```php
$dataset = $client->datasets()->get('dataset-id');
```

#### Delete Dataset

```php
$client->datasets()->delete('dataset-id');
```

#### Get Dataset Graph

```php
$graph = $client->datasets()->getGraph('dataset-id');
```

#### Get Dataset Status

```php
$status = $client->datasets()->getStatus('dataset-id');
echo $status['status']; // pending, processing, completed, failed
```

### Adding Data

```php
use Cognee\Requests\AddDataRequest;

// Add text data
$request = new AddDataRequest(
    data: 'Your text content',
    datasetId: 'dataset-id',
);
$response = $client->datasets()->add($request);

// Add data with dataset name
$request = new AddDataRequest(
    data: 'Your text content',
    datasetName: 'my-dataset',
);
$response = $client->datasets()->add($request);
```

### Cognifying (Processing Data)

```php
use Cognee\Requests\CognifyRequest;

// Process specific datasets
$request = new CognifyRequest(
    datasetIds: ['dataset-id-1', 'dataset-id-2'],
    runInBackground: false,
);
$response = $client->datasets()->cognify($request);

// Process in background
$request = new CognifyRequest(
    datasetIds: ['dataset-id'],
    runInBackground: true,
);
$response = $client->datasets()->cognify($request);

if ($response->pipelineRunId) {
    echo "Processing started: {$response->pipelineRunId}\n";
}
```

### Searching

```php
use Cognee\Requests\SearchRequest;
use Cognee\Enums\SearchType;

// Semantic search (default)
$request = new SearchRequest(
    query: 'What is artificial intelligence?',
    searchType: SearchType::SEMANTIC,
    datasetIds: ['dataset-id'],
    topK: 10,
);
$response = $client->search()->search($request);

// Keyword search
$request = new SearchRequest(
    query: 'machine learning',
    searchType: SearchType::KEYWORD,
);
$response = $client->search()->search($request);

// Hybrid search
$request = new SearchRequest(
    query: 'neural networks',
    searchType: SearchType::HYBRID,
    topK: 5,
);
$response = $client->search()->search($request);

// Process results
foreach ($response->results as $result) {
    echo "Text: {$result->text}\n";
    echo "Score: {$result->score}\n";
    echo "Dataset: {$result->datasetId}\n";
    print_r($result->metadata);
}
```

#### Get Search History

```php
$history = $client->search()->history();
```

### Authentication

```php
// Login
$user = $client->auth()->login('email@example.com', 'password');

// Register
$user = $client->auth()->register([
    'email' => 'new@example.com',
    'password' => 'secure-password',
    'name' => 'John Doe',
]);

// Logout
$client->auth()->logout();

// Forgot password
$client->auth()->forgotPassword('email@example.com');

// Verify email
$client->auth()->verify('verification-token');
```

### Permissions

```php
// Grant dataset permissions
$client->permissions()->grantDatasetPermission('user-id', [
    'read' => true,
    'write' => true,
]);

// Create role
$role = $client->permissions()->createRole([
    'name' => 'Editor',
    'permissions' => ['read', 'write'],
]);

// Assign user to role
$client->permissions()->assignUserToRole('user-id', 'role-id');

// Create tenant
$tenant = $client->permissions()->createTenant([
    'name' => 'Acme Corp',
]);
```

### Health Checks

```php
// Basic health check
$health = $client->health()->check();

// Detailed health check
$detailed = $client->health()->detailed();
```

## Exception Handling

The SDK provides specific exception types for different error scenarios:

```php
use Cognee\Exceptions\AuthenticationException;
use Cognee\Exceptions\ValidationException;
use Cognee\Exceptions\NotFoundException;
use Cognee\Exceptions\RateLimitException;
use Cognee\Exceptions\ServerException;
use Cognee\Exceptions\CogneeException;

try {
    $dataset = $client->datasets()->get('invalid-id');
} catch (NotFoundException $e) {
    echo "Dataset not found: " . $e->getMessage();
} catch (AuthenticationException $e) {
    echo "Authentication failed: " . $e->getMessage();
} catch (ValidationException $e) {
    echo "Validation error: " . $e->getMessage();
    echo "Response: " . $e->getResponseBody();
} catch (RateLimitException $e) {
    echo "Rate limit exceeded: " . $e->getMessage();
} catch (ServerException $e) {
    echo "Server error: " . $e->getMessage();
} catch (CogneeException $e) {
    echo "API error: " . $e->getMessage();
}
```

## Development

### Running Tests

```bash
# Run all tests
composer test

# Run with coverage report
composer test-coverage

# Run only unit tests
vendor/bin/phpunit --testsuite=Unit

# Run only integration tests (requires running Cognee instance)
export COGNEE_BASE_URL=http://localhost:8000
export COGNEE_API_KEY=your-api-key
vendor/bin/phpunit --testsuite=Integration
```

### Code Style

The SDK follows PSR-12 coding standards:

```bash
# Check code style
composer cs-check

# Fix code style
composer cs-fix
```

### Project Structure

```
php-cognee-sdk/
├── src/
│   ├── Client.php              # Main SDK client
│   ├── Config.php              # Configuration class
│   ├── Resources/              # API resource classes
│   ├── Models/                 # Data models
│   ├── Requests/               # Request DTOs
│   ├── Responses/              # Response DTOs
│   ├── Exceptions/             # Exception classes
│   └── Enums/                  # Enumerations
└── tests/
    ├── Unit/                   # Unit tests
    └── Integration/            # Integration tests
```

## Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Make your changes
4. Ensure all tests pass (`composer test`)
5. Ensure code follows PSR-12 (`composer cs-fix`)
6. Commit your changes (`git commit -m 'Add amazing feature'`)
7. Push to the branch (`git push origin feature/amazing-feature`)
8. Open a Pull Request

Please ensure:
- All tests pass
- Code coverage remains above 90%
- Code follows PSR-12 standards
- New features include tests

## License

MIT License. See [LICENSE](LICENSE) for details.

## Links

- [PHP SDK Repository](https://github.com/mikewolfxyou/php-cognee-sdk)
- [Report Issues](https://github.com/mikewolfxyou/php-cognee-sdk/issues)
- [Cognee GitHub](https://github.com/topoteretes/cognee)
- [Cognee Documentation](https://docs.cognee.ai)
- [PHP Documentation](https://www.php.net/)
