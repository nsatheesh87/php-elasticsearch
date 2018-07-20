<?php

namespace App\Http\Controllers;
use Elasticsearch\ClientBuilder;

/**
 * Class CustomerController
 * @package App\Http\Controllers
 */
class InstagramController extends Controller
{
    /**
     * @param string $tag
     * @param string $sort
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index($tag = '', $sort = 'recent')
    {
        $resultSet = $this->fetchFeeds(0, $tag, $sort);
        return view('welcome', ['feeds' => $resultSet, 'sort' => $sort]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function feed()
    {
        $tags = ['9gag','barked', 'meowed', 'voyaged'];
        foreach($tags as $tag)
        {
            $feedData = $this->processApi($tag);
            $formattedFeeds = $this->formatFeed($feedData->data);
            $this->storeFeed($formattedFeeds);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Feeds are processed to elasticsearch'
        ]);
    }

    /**
     * @param string $tag
     * @return mixed
     */
    private function processApi($tag = '')
    {
        $accessToken = env('INSTAGRAM_TOKEN');
        $uri = sprintf('https://api.instagram.com/v1/tags/%s/media/recent', $tag);
        // $uri = 'https://api.instagram.com/v1/users/self/media/recent';
        $client = new \GuzzleHttp\Client();
        $res = $client->request('GET', $uri, [
            'query' => ['access_token' => $accessToken]
        ]);
        return json_decode($res->getBody());
    }

    /**
     * @return bool
     */
    private function deleteFeed()
    {
        $params = ['index' => 'instagram'];
        $client = ClientBuilder::create()->setHosts($this->getHosts())->build();
        if($client->indices()->exists($params)) {
            $client->indices()->delete($params);
        }
        return true;
    }

    /**
     * @param array $feeds
     */
    private function storeFeed($feeds = [])
    {
        // Clear old unwanted data and do the fresh insert
        $this->deleteFeed();
        $client = ClientBuilder::create()->setHosts($this->getHosts())->build();
        foreach($feeds as $rKey => $feed) {
                $params = [
                    'index' => 'instagram',
                    'type' => 'my_type',
                    'id' => $feed['id'],
                    'body' => $feed
                ];
                $client->index($params);
        }
    }

    /**
     * @param array $feeds
     * @return array
     */
    private function formatFeed($feeds = [])
    {
        $fFeeds = [];
        foreach($feeds as $rKey => $feed) {
            $fFeeds[$rKey]['id'] = $feed->id;
            $fFeeds[$rKey]['username'] = $feed->user->username;
            $fFeeds[$rKey]['profile_picture'] = $feed->user->profile_picture;
            $fFeeds[$rKey]['caption'] = $feed->caption;
            $fFeeds[$rKey]['likes_count'] = $feed->likes->count;
            if ($feed->type == 'video') {
                $fFeeds[$rKey]['post'] = $feed->images->low_resolution->url;
            } else {
                $fFeeds[$rKey]['post'] = $feed->images->low_resolution->url;
            }
            $fFeeds[$rKey]['comments_count'] = $feed->comments->count;
            $fFeeds[$rKey]['tags'] = $feed->tags;
            $fFeeds[$rKey]['type'] = $feed->type;
            $fFeeds[$rKey]['created_time'] = $feed->created_time;

        }
        return $fFeeds;
    }

    /**
     * @param int $page
     * @param string $tag
     * @param string $sort
     * @return array|int
     */
    private function fetchFeeds($page = 0, $tag = '', $sort = 'recent')
    {
        $resultSet = [];
        $client = ClientBuilder::create()->setHosts($this->getHosts())->build();
        $params = ['index' => 'instagram'];
        if(!$client->indices()->exists($params)) {
            return $resultSet['total'] = 0;
        }


        $from = $page*4;
        $order = '_id';
        if($sort != 'recent') {
            $order = '_score';
        }
        $params = [
            "size" => 4,               // how many results *per shard* you want back
            "from" => $from,
            "index" => "instagram",
            "body" => [
                "query" => [
                    "match_all" => new \stdClass()
                ],
                'sort' => [
                    [$order => ['order' => 'desc']]
                ]
            ],
        ];

        $response = $client->search($params);
        if (isset($response['hits']['hits']) && count($response['hits']['hits']) > 0) {
            $resultSet = $response['hits'];
        }
        return $resultSet;
    }

    /**
     * @param int $page
     * @param string $tag
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function fetch($page = 0, $tag = '')
    {
        $resultSet = $this->fetchFeeds($page, $tag);
        return view('feed', ['feeds' => $resultSet]);
    }

    /**
     * @return array
     */
    private function getHosts()
    {
        // elastic host was added to you hosts file automatically
        return ['elasticsearch'];
    }

}
