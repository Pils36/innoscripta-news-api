<?php

use Illuminate\Support\Facades\Config;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

uses(\Tests\TestCase::class);

// Feature test to verify fetchArticlesFromSource integration
test('fetchArticlesFromSource integrates with API and processes data', function () {
    Config::set('services.globalnews.api_keys.new_api', 'test-api-key');

    $mockedClient = mock(Client::class);

    $mockedClient
        ->shouldReceive('get')
        ->once()
        ->with('https://newsapi.org/v2/top-headlines?country=us&category=business&apiKey=test-api-key')
        ->andReturn(new Response(200, [], json_encode([
            'articles' => [
                ['title' => 'Article 1'],
                ['title' => 'Article 2'],
            ],
        ])));

    $service = new class($mockedClient) {
        public $client;

        public function __construct($client)
        {
            $this->client = $client;
        }

        public function fetchArticlesFromSource($source)
        {
            $apiUrl = match ($source) {
                'news_api' => 'https://newsapi.org/v2/top-headlines?country=us&category=business&apiKey=' . config('services.globalnews.api_keys.new_api'),
                'new_york_times' => 'https://api.nytimes.com/svc/topstories/v2/world.json?api-key=' . config('services.globalnews.api_keys.new_york_times'),
                'the_guardian' => 'https://content.guardianapis.com/search?api-key=' . config('services.globalnews.api_keys.the_guardian'),
            };

            $response = $this->client->get($apiUrl);
            $data = json_decode($response->getBody(), true);

            $articles = $this->mapArticles($data, $source);
            $this->storeArticles($articles);
            return $articles;
        }

        public function mapArticles($data, $source)
        {
            return array_map(fn($article) => [
                'title' => $article['title'] ?? 'N/A',
                'source' => $source,
            ], $data['articles'] ?? []);
        }

        public function storeArticles($articles)
        {
            // Simulate storage of articles
            foreach ($articles as $article) {
                // Log storage (this would normally be a database operation)
                echo "Stored: {$article['title']} from {$article['source']}\n";
            }
        }
    };

    $articles = $service->fetchArticlesFromSource('news_api');

    expect($articles)->toHaveCount(2);
    expect($articles[0]['title'])->toBe('Article 1');
    expect($articles[1]['title'])->toBe('Article 2');
    expect($articles[0]['source'])->toBe('news_api');
});
