<?php declare(strict_types=1);

use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler;

require __DIR__.'/vendor/autoload.php';

const CZU = "https://is.czu.cz";
const DELAY = 100;

/**
 * Filters text using given crawler and selector string.
 * @param Crawler $crawler - the crawler
 * @param string $selecter - specified selector tag
 * @return string filtred text or null in case of fail
 */
function text(Crawler $crawler, string $selector)
{
    $new = $crawler->filter($selector);
    if (count($new)) {
        return trim($new->text());
    }

    return null;
}

/**
 * Prints names and corresponding URLs from 'https://is.czu.cz/lide'.
 * Scrapes in the given range and looks for pattern specified in $query parameter (if it's empty then prints every record).
 * @param int $from - id from where to scrape
 * @param int $to - id to where to scrape
 * @param string $query - query string e.g. 'Jan Černý', if it's empty then it will print every record
 */
function scrape(int $from, int $to, string $query)
{
    $client = new Client();

    for ($i = $from; $i <= $to; $i++)
    {
        $url = CZU . "/lide/clovek.pl?id=$i";
        $crawler = $client->request("GET", $url);
        $result["url"] = $url;
        $result["name"] = text($crawler, "font");
        // Query not specified or the name fits the query -> print
        if ($query == "" || str_contains($result["name"], $query))
            echo $result["name"] . " -> " . $result["url"] . PHP_EOL;
        usleep(DELAY);
    }
}

// MAIN
if (count($argv) !== 3 && count($argv) !== 4) {
    echo "Usage: php main.php <from> <to> [query]\n";
    exit(1);
}

$from = intval($argv[1]);
$to = intval($argv[2]);
$query = count($argv) == 4 ? $argv[3] : "";
scrape($from, $to, $query);
