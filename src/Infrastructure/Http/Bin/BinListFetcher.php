<?php

namespace App\Infrastructure\Http\Bin;


use App\Exception\BinNotFoundException;
use App\Infrastructure\Http\Bin\Dto\BinResult;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Cache\CacheItemInterface;
use Symfony\Contracts\Cache\CacheInterface;

class BinListFetcher implements BinListFetcherInterface
{
    public function __construct(
        private ClientInterface $client,
        private CacheInterface $cache,
    )
    {
    }

    /**
     * @throws GuzzleException
     * @throws BinNotFoundException
     */
    public function getBin(string $binCode): BinResult
    {
        $response = $this->cache->get("api.binlist.{$binCode}", function(CacheItemInterface $cacheItem) use ($binCode) {
            
            $cacheItem->expiresAfter(\DateInterval::createFromDateString('1 day'));
            
            try {
                return (string) $this->client->get("https://lookup.binlist.net/{$binCode}")->getBody();
            } catch (ClientException $e) {
                if($e->getCode() === 404) {
                    throw new BinNotFoundException("Bin '$binCode' could not be found");
                }

                throw $e;
            }
        });
        

        $binResult = json_decode($response, true);
        $alpha2 = $binResult['country']['alpha2'] ?? null;
        
        if(!$alpha2) {
            throw new BinNotFoundException("Bin '$binCode' could not be found");
        }
        
        return new BinResult(alpha2: $alpha2);
    }
}
