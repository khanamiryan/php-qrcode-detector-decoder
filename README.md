# QR code decoder / reader for PHP
This is a PHP library to detect and decode QR-codes.<br />This is first and only QR code reader that works without extensions.<br />
Ported from [ZXing library](https://github.com/zxing/zxing)


## Usage 
```php
composer require dijkma/qrcodedecoder
```

To use the class include the autoload file and use the class:
````php
require_once __DIR__.'/vendor/autoload.php';

use Dijkma\QRReader\QrReader
````

After that you can use the class like this:

````php
$qr = new QrReader('Location/to/image/with/qrcode')
echo $qr->toText;
````

## Requirements 
* PHP >= 5.6
* GD Library or Imagick


## Contributing

You can help the project by adding features, cleaning the code and update everything for php 7.

 
1. Fork it
2. Create your feature branch: `git checkout -b my-new-feature`
3. Commit your changes: `git commit -am 'Add some feature'`
4. Push to the branch: `git push origin my-new-feature`
5. Submit a pull request
