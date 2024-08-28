<?php

namespace App\Cli;

use App\Exception\BinNotFoundException;
use App\Infrastructure\Http\Bin\BinListFetcherInterface;
use App\Infrastructure\Http\CurrencyExchange\CurrencyExchangeRateFetcherInterface;
use App\Service\AmountToEuroConverterInterface;
use App\Service\CommissionAmountCalculatorInterface;
use App\Specification\IsEuropeanAlphaCode;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand('calculate-commisions')]
class CalculateCommissions extends Command
{
    public function __construct(
        private readonly CurrencyExchangeRateFetcherInterface $currencyExchangeRateFetcher,
        private readonly BinListFetcherInterface $binListFetcher,
        private readonly AmountToEuroConverterInterface $amountCurrencyConverter,
        private readonly CommissionAmountCalculatorInterface $commissionAmountCalculator,
    )
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->addArgument('input', InputArgument::REQUIRED, 'The input file')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $stream = fopen($input->getArgument('input'), 'r+');
        $io = new SymfonyStyle($input, $output);
        
        while ($row =  fgets($stream)) {
            if (empty($row)) {
                break;
            }
            if(!json_decode($row)) {
                break;
            }

            ['bin' => $bin, 'amount' => $amount, 'currency' => $currency] = json_decode($row, true);
            
            

            $rate = $this->currencyExchangeRateFetcher->getExchangeRate($currency);
            $amountInEuros = $this->amountCurrencyConverter->toEuros($amount, $currency, $rate);

            try {
                $binResults = $this->binListFetcher->getBin($bin);
            } catch (BinNotFoundException $e) {
                $io->error($e);
                return Command::FAILURE;
            }
            
            $issuedInEu = (new IsEuropeanAlphaCode())->isSatisfiedBy($binResults->alpha2);
            
            $commission = $this->commissionAmountCalculator->calculateCommission($amountInEuros, $issuedInEu);
            
            $output->writeln($commission);
        }
        return Command::SUCCESS;
    }

}
