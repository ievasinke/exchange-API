<?php
/*
 * Create application that allows you to enter amount INCLUDING currency,
 * then conversion currency and display the result.
 * Example: "100 EUR" -> "USD" should display how much 100 eur is in USD.
 * I should also be able to type " 100 eur" (lowercase)
 * The data can be gathered from free API source - https://github.com/fawazahmed0/exchange-api

 * CODE MUST ALIGN WITH PSR STANDARTS
 * DO NOT USE PACKIGIST / COMPOSER / GUZZLE
 */
function getCurrency(string $url): ?string
{
    $options = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER => false
    ];

    $ch = curl_init($url);
    curl_setopt_array($ch, $options);
    $content = curl_exec($ch);

    curl_close($ch);
    return $content;
}

$inputAmount = strtolower((string)readline("Enter the amount and currency (seperated by space): "));
$conversionCurrency = strtolower((string)readline("Enter the conversion currency: "));

$inputParts = explode(" ", $inputAmount);
if (count($inputParts) !== 2) {
    exit("Invalid input format. Please enter amount and currency separated by space.\n");
}
$amount = $inputParts[0];
$passingCurrency = $inputParts[1];
$url = "https://cdn.jsdelivr.net/npm/@fawazahmed0/currency-api@latest/v1/currencies/$passingCurrency.json";
$fallBackUrl = "https://latest.currency-api.pages.dev/v1/currencies/$passingCurrency.json";

$jsonData = json_decode(getCurrency($url));
if ($jsonData === null) {
    $jsonData = json_decode(getCurrency($fallBackUrl));
}
if ($jsonData === null || !isset($jsonData->$passingCurrency)) {
    exit("Could not retrieve currency data for $passingCurrency.\n");
}

$rate = $jsonData->$passingCurrency->$conversionCurrency ?? null;
if ($rate === null) {
    exit("Could not find exchange rate for $passingCurrency to $conversionCurrency.\n");
}

$exchangedAmount = number_format(($amount * $rate), 2);
echo "$amount "
    . strtoupper($passingCurrency)
    . " are $exchangedAmount "
    . strtoupper($conversionCurrency)
    . PHP_EOL;