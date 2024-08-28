<?php

namespace App\Infrastructure\Http\CurrencyExchange;

use App\Exception\ExchangeRateNotFoundException;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\TransferException;
use Psr\Cache\CacheItemInterface;
use Symfony\Contracts\Cache\CacheInterface;

class CurrencyExchangeRateFetcher implements CurrencyExchangeRateFetcherInterface
{
    public function __construct(
        private ClientInterface $client,
        private CacheInterface $cache,
    )
    {
    }

    public function getExchangeRate(string $currency): float
    {
        $body = $this->cache->get('api.exchange_rates.latest', function(CacheItemInterface $cacheItem) {
            $cacheItem->expiresAfter(\DateInterval::createFromDateString('1 minutes'));

            $response = $this->client->get('https://api.exchangeratesapi.io/latest');
            $body = (string)$response->getBody();
            $success = @json_decode($body, true)['success'] ?? false;
            
            if(!$success) {
                throw new TransferException($body);
            }
            return $body;
        });
        
        $rate = json_decode($body, true)['rates'][$currency] ?? null;
        
        if(!$rate) {
            throw new ExchangeRateNotFoundException();
        }
        
        return $rate;
    }
}
