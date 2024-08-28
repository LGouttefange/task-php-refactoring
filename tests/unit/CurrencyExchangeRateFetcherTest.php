<?php

namespace Tests;

use App\Exception\ExchangeRateNotFoundException;
use App\Infrastructure\Http\CurrencyExchange\CurrencyExchangeRateFetcher;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

class CurrencyExchangeRateFetcherTest extends \PHPUnit\Framework\TestCase
{
    private CurrencyExchangeRateFetcher $currencyExchangeRate;
    private MockHandler $mock;

    protected function setUp(): void
    {

        $this->mock = new MockHandler([
            new Response(200, [], file_get_contents('data/tests/mocks/exchange_rates.json')),
        ]);

        $handlerStack = HandlerStack::create($this->mock);
        $client = new Client(['handler' => $handlerStack]);
        $this->currencyExchangeRate = new CurrencyExchangeRateFetcher($client, new ArrayAdapter());
    }


    public function test_it_fetches_currency_exchange_rate()
    {
        $this->assertEquals($this->currencyExchangeRate->getExchangeRate('GBP'), 0.841657);
    }

    public function test_it_fails_on_unknown_currency()
    {
        $this->expectException(ExchangeRateNotFoundException::class);
        $this->currencyExchangeRate->getExchangeRate('UKNOWN');
    }

    public function test_it_handles_unavailable_service()
    {
        $exception = new ServerException('Server unavailable', new \GuzzleHttp\Psr7\Request('GET', 'test'), new Response(500));
        $this->mock->reset();
        $this->mock->append(
            $exception
        );

        $this->expectExceptionObject($exception);
        $this->currencyExchangeRate->getExchangeRate('GBP');
    }

    public function test_it_caches_responses()
    {
        $this->mock->append(
            new ServerException('Server unavailable', new \GuzzleHttp\Psr7\Request('GET', 'test'), new Response(500))
        );

        $this->expectNotToPerformAssertions();
        $this->currencyExchangeRate->getExchangeRate('GBP');
        $this->currencyExchangeRate->getExchangeRate('GBP');
    }
}
