<?php

namespace Tests;

use App\Exception\BinNotFoundException;
use App\Infrastructure\Http\Bin\BinListFetcher;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

class BinFetcherTest extends TestCase
{
    private const BINCODE = '4745030';
    private BinListFetcher $binFetcher;
    private MockHandler $mock;

    protected function setUp(): void
    {
        $this->mock = new MockHandler([
            new Response(200, [], file_get_contents('data/tests/mocks/binlist.json')),
        ]);

        $handlerStack = HandlerStack::create($this->mock);
        $client = new Client(['handler' => $handlerStack]);
        $this->binFetcher = new BinListFetcher($client, new ArrayAdapter());
    }

    public function test_it_fetches_bin()
    {
        $result = $this->binFetcher->getBin(self::BINCODE);
        
        $this->assertEquals('DK', $result->alpha2);
    }

    public function test_it_throws_exception_on_unknown_bin()
    {
        $this->mock->reset();
        $this->mock->append(new Response(404));
        $this->expectException(BinNotFoundException::class);
        $result = $this->binFetcher->getBin('unknownbin');
        
        
        $this->assertEquals('DK', $result->alpha2);
    }

    public function test_it_caches_calls_to_same_bin_code()
    {
        $this->expectNotToPerformAssertions();
        $this->mock->append(new Response(500));
        
        $this->binFetcher->getBin(self::BINCODE);
        $this->binFetcher->getBin(self::BINCODE);
    }

    public function test_it_does_not_cache_calls_to_other_bin_codes()
    {
        $this->expectException(ServerException::class);
        $this->mock->append(new Response(500));
        
        $this->binFetcher->getBin(self::BINCODE);
        $this->binFetcher->getBin('UNKNOWN');
    }
}
