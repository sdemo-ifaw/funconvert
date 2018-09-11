<?php

/*
-- Based on the work by @joshlawton https://github.com/joshlawton/Currency-Converter.
-- The following code was rewritten for the sake of the assignment
-- Overall, there could be more error handling before it hit a production environment.
-- As much of the code remained unaltered, I used the remaining time instead to review for opportunity, comment, and explain functionality to show a deeper understanding.
*/

class funtechConvert {

// Defining properties within the class
private $xml='https://wikitech.wikimedia.org/wiki/Fundraising/tech/Currency_conversion_sample?ctype=text/xml&action=raw';
private $basecurrency='USD';
private $exchangeRates=array();
private $mysqli=NULL;

// Constructor method
public function __construct($uri=NULL) {
  if (isset($uri) && !empty($uri)) {
$this->xml=$uri;
}

// Database connection details preset in local XAMPP for testing
$servername="localhost";
$username="root";
$password="";
$dbname="convert";

// Connection statement
if ($this->mysqli === NULL) {
$this->mysqli=new mysqli($servername, $username, $password, $dbname);
}
  // Added error message for connection issues
  if ($this->mysqli->connect_errno) {
      printf("Connect failed: %s\n", $this->mysqli->connect_error);
      exit();
}
$this->getExchangeRates();
}
// Method to parse external xml file and input currency exchange rates into database
private function getExchangeRates() {
$values=array();
$exchangeRateXML=simplexml_load_file($this->xml);

  // Increased the DECIMAL(precision, scale) in create.sql to handle weaker currencies, such as the VND
  foreach ($exchangeRateXML->conversion as $conversion) {
    $values[]='("' . (string)$conversion->currency . '", ' . (float)$conversion->rate . ')';
}

//  TO DO: Write ON DUPLICATE KEY UPDATE statement to prevent duplicating rows each time query is performed.
$this->mysqli->query('INSERT INTO currencyrates (currencyType, currencyRate) VALUES ' . implode(',', $values));
}

// Could not think of a more elegant solution to array vs. single amount.
public function convertCurrency($transaction) {
  if (is_array($transaction)) {
return $this->convertCurrencyByArray($transaction);
} else {
return $this->convertCurrencyByAmount($transaction);
}
}

private function convertCurrencyByArray($transactions) {
$convertedCurrency=array();

// Once again, I found that by using prepare, we allow for "?" to be specified later as $stmt which works well in a foreach

$stmt=$this->mysqli->prepare("SELECT currencyType, currencyRate FROM currencyrates WHERE currencyType=?");

// and then bind parameter as string type, execute, and get the result as return
foreach ($transactions as $transaction) {
list($currency, $amount)=explode(" ", $transaction);
$stmt->bind_param("s", $currency);
$stmt->execute();
$res=$stmt->get_result();
$row=$res->fetch_assoc();

$convertedCurrency[]=$this->basecurrency . ' ' . number_format((float)$row['currencyRate'] * (float)$amount, 2);
}

$stmt->close();

return $convertedCurrency;
}

// Similarly to convertCurrencyByArray(), but with just a specific amount
private function convertCurrencyByAmount($transaction) {
list($currency, $amount)=explode(" ", $transaction);

$stmt=$this->mysqli->prepare("SELECT currencyType, currencyRate FROM currencyrates WHERE currencyType=?");
$stmt->bind_param("s", $currency);
$stmt->execute();
$res=$stmt->get_result();
$row=$res->fetch_assoc();
$stmt->close();

return $this->basecurrency . ' ' . number_format((float)$row['currencyRate'] * (float)$amount, 2);
}

// Cleanly closes the connection
public function __destruct() {
$this->mysqli->close();
}
}

// To do:  Adjust arguments to the specific input
$cc=new funtechConvert();
echo $cc->convertCurrency('AUD 10000') . "\n"; // 10689.00
print_r($cc->convertCurrency(array('JPY 10000', 'CZK 10000'))); // 131.25, 519.00
