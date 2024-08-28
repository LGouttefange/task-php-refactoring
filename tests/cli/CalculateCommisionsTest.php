<?php

namespace cli;

use App\Cli\CalculateCommissions;
use App\Infrastructure\Http\Bin\BinListFetcher;
use App\Infrastructure\Http\CurrencyExchange\CurrencyExchangeRateFetcher;
use App\Service\AmountToEuroConverter;
use App\Service\CommissionAmountCalculator;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class CalculateCommisionsTest extends TestCase
{
    private CalculateCommissions $command;
    private MockHandler $binFetcherMock;
    private MockHandler $exchangeRatesFetcherMock;

    protected function setUp(): void
    {

        $binHttpClient = new Client([
            'handler' => $this->binFetcherMock = new MockHandler([
                new Response(200, [], file_get_contents('data/tests/mocks/binlist.json')),
            ])
        ]);
        $exchangeRatesHttpClient = new Client([
            'handler' => $this->exchangeRatesFetcherMock = new MockHandler([
                new Response(200, [], file_get_contents('data/tests/mocks/exchange_rates.json')),
            ])
        ]);

        $this->command = new CalculateCommissions(
            new CurrencyExchangeRateFetcher(
                $exchangeRatesHttpClient,
                new ArrayAdapter(),
            ),
            new BinListFetcher(
                $binHttpClient,
                new ArrayAdapter(),
            ),
            new AmountToEuroConverter(),
            new CommissionAmountCalculator(0.01, 0.02)
        );
    }

    public function test_it_outputs_eur_commission_amount()
    {
        $tester = new CommandTester($this->command);
        $tester->execute(['input' => 'data/tests/inputs/input_eur.txt']);
        $this->assertEquals("1\n", $tester->getDisplay(true));
    }
    public function test_it_outputs_non_eu_commission_amount()
    {
        $tester = new CommandTester($this->command);
        $tester->execute(['input' => 'data/tests/inputs/input_outside_eu.txt']);
        $this->assertEquals("0.5\n", $tester->getDisplay(true));
    }
    public function test_it_skips_empty_and_invalid_lines()
    {
        $tester = new CommandTester($this->command);
        $tester->execute(['input' => 'data/tests/inputs/input_invalid.txt']);
        
        $this->assertEmpty($tester->getDisplay());
    }
    
    public function test_unknown_bic_stops_application()
    {
        $this->binFetcherMock->reset();
        $this->binFetcherMock->append(new Response( body: '{"success": "false"}'));
        $this->binFetcherMock->append(new Response(body: file_get_contents('data/tests/mocks/binlist.json')));
        $tester = new CommandTester($this->command);
        $tester->execute(['input' => 'data/tests/inputs/input_unknown_bic.txt']);
        
        $this->assertEquals(Command::FAILURE, $tester->getStatusCode());
        $this->assertStringNotContainsString("\n0.5\n", $tester->getDisplay());
    }
}
