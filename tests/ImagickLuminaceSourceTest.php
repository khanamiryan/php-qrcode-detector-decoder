<?php

namespace Khanamiryan\QrCodeTests;

use PHPUnit\Framework\TestCase;
use Zxing\IMagickLuminanceSource;
use Imagick;

class IMagickLuminanceSourceTest extends TestCase
{
	public function setUp(): void
	{
		error_reporting(E_ALL);
	}

	public function testUnlimitedMemoryDoesNotThrowException()
	{
		// unlimited
		ini_set('memory_limit','-1');

        $imgSource = __DIR__ . "/qrcodes/hello_world.png";
        $im = new Imagick();
        $im->readImage($imgSource);
		
		$qrcode = new IMagickLuminanceSource($im, 1, 1);
        $this->assertTrue($qrcode->isCropSupported());
	}
}
