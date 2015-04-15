# Valutabank.hu parser package for Laravel 5
With this package you can get the actual HUF (Hungarian Forint) value of different currencies.
## Installation
Install with the following Composer command
```
$ composer require subdesign/laravel-valutabank
```
Add the following line in the `app/config/app.php` file into the _service provider_ array
```
'Subdesign\Valutabank\Provider\ValutabankServiceProvider',
```
And add the facade to the _aliases_ array
```
'Valutabank' => 'Subdesign\Valutabank\Facade\Valutabank',
```
Publish the config file 
``` 
$ php artisan vendor:publish
```
## Setting up
You can edit the config file `app/config/valutabank.php`, options are
```
return [
	'currencies' => ['USD', 'EUR', 'CHF'], // array of currencies or the string "all" if you want all (array/string)
	'returntype' => 'array',               // html OR array OR json (string)
	'show_bank'	 => true, 				   // show the bank name for rate	
	'curl' 		 => true,	               // use CURL (bool) 
	'cache'		 => true,	               // use caching of server data (bool)
	'cache_ttl'	 => 60,	                   // if caching enabled, set minutes for TTL value (integer)
	'icon_path'  => '/assets/images/',     // relative path (from public/) to the icon images (string)
	'icon_name'  => 'icon',                // icon name prefix. it will be "icon-usd", "icon-eur" etc. (string)
	'icon_ext'   => 'jpg'                  // extension of icon image files (string)
];
```
## Usage
If you use it in your controller, `use` the Facade of the package like
```
use Subdesign\Valutabank\Facade\Valutabank;
```
then 
``` 
$result = Valutabank::get();
```
or you can add a backslash to the facade
```
$result = \Valutabank::get();
```
## Dependency
The package uses `anlutro/curl` for optionally replace `file_get_contents()` in the code, if the latter is disabled in your server for some reason.
## Example data
Getting the USD - HUF exchange rate as `array`
```
array(2) {
  ["lastUpdate"]=>
  string(19) "2015-04-15 14:40:17"
  ["USD"]=>
  array(3) {
    ["buying_rate"]=>
    array(2) {
      ["value"]=>
      string(6) "273.71"
      ["bank"]=>
      string(8) "KDB Bank"
    }
    ["selling_rate"]=>
    array(2) {
      ["value"]=>
      string(6) "284.31"
      ["bank"]=>
      string(8) "KDB Bank"
    }
    ["icon"]=>
    string(27) "/assets/images/icon-usd.jpg"
  }
}
```
## Notes
The source code is PSR-1 and PSR-2 compilant.  
Please show the link of valutabank.hu somewhere in your view code below the rendered data, for credits.
## Copyright
&copy; 2015 Barna Szalai 
## License
[MIT](http://opensource.org/licenses/MIT)
## Contact
twitter: @devartpro  
email: <szalai.b@gmail.com>  
irc: @BarnaSzalai at #laravel.hu/#laravel (freenode)  
slackchat: [https://laravelbp.slack.com](https://laravelbp.slack.com)