php-rest-sdk
============

## Installation

The easiest way to start using the SDK is to download it with [Composer](http://www.getcomposer.org). Simply add this to your composer.json file:

```json
{
   "require": {
     "chartblocks/php-rest-sdk": "~1.1."
   }
}
```

Alternatively download the source code manually, and include the classes in `src/ChartBlocks`

## Usage

### Tokens

You can get your API token and secret key by registering for an account, and then generating an API token from your profile page. Take these values and store them in your application config somewhere.

### Example

```php
<?php
$client = new \ChartBlocks\Client(array(
  'token' => 'PASTE_TOKEN',
  'secret' => 'PASTE_SECRET'
  ));
  
$dataSets = $client->getRepository('dataSet');
$myDataSet = $dataSets->findById('52f139ea054ff30f1f000004');

$myDataSet->append(array(
  new \ChartBlocks\DataSet\Row(null, array('a', 'b', 'c')),
  new \ChartBlocks\DataSet\Row(null, array('d', 'e', 'f'))
));
?>
```

## Development info

The default server URL for requests is http://api.chartblocks.com/v1/. To overwrite this setting, set the environment variable CB_API_URL.
