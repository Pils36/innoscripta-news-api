<?php

namespace App\Services;

use GuzzleHttp\Client;
use App\Models\Article;
use Illuminate\Support\Carbon;

class NewsFetcher
{
    private $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function fetchArticlesFromSource($source)
    {
        $apiUrl = match ($source) {
            'news_api' => 'https://newsapi.org/v2/top-headlines?country=us&category=business&apiKey='.config('services.globalnews.api_keys.new_api'),
            'new_york_times' => 'https://api.nytimes.com/svc/topstories/v2/world.json?api-key='.config('services.globalnews.api_keys.new_york_times'),
            'the_guardian' => 'https://content.guardianapis.com/search?api-key='.config('services.globalnews.api_keys.the_guardian'),
        };

        $response = $this->client->get($apiUrl);
        $data = json_decode($response->getBody(), true);

        $articles = $this->mapArticles($data, $source);
        $this->storeArticles($articles);
    }

    private function mapArticles($data, $source)
    {

        // Map API data to Article format based on $source
        return collect($data['articles'] ?? $data['results'] ?? [])->map(function ($item) use ($source) {
            $published_at = $item['publishedAt'] ?? $item['webPublicationDate'] ?? $item['published_date'];
            return [
                'title' => $item['title'] ?? $item['webTitle'],
                'author' => $item['author'] ?? $item['byline'] ?? null,
                'content' => $item['content'] ?? $item['abstract'] ?? null,
                'url' => $item['url'] ?? $item['webUrl'],
                'source' => $source,
                'category' => $item['category'] ?? $item['kicker'] ?? $item['pillarName'] ?? null,
                'published_at' => Carbon::parse($published_at)->toDateTimeString(),
            ];
        });
    }

    private function storeArticles($articles)
    {
        foreach ($articles as $article) {
            Article::updateOrCreate(['url' => $article['url']], $article);
        }
    }

}
