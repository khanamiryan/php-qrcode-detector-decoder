<?php

namespace Khanamiryan\QrCodeTests;

use PHPUnit\Framework\TestCase;
use Zxing\QrReader;

class QrReaderTest extends TestCase
{
	public function setUp(): void
	{
		error_reporting(E_ALL);
	}

	public function testText1()
	{
		$image = __DIR__ . "/qrcodes/hello_world.png";

		$qrcode = new QrReader($image);
		$this->assertSame("Hello world!", $qrcode->text());
	}

	public function testNoText()
	{
		$image = __DIR__ . "/qrcodes/empty.png";
		$qrcode = new QrReader($image);
		$this->assertSame(false, $qrcode->text());
	}

	public function testText2()
	{
		$image = __DIR__ . "/qrcodes/139225861-398ccbbd-2bfd-4736-889b-878c10573888.png";
		$qrcode = new QrReader($image);
		$qrcode->decode([
			'TRY_HARDER' => true
		]);
		$this->assertSame(null, $qrcode->getError());
	}

	public function testText3()
	{
		$image = __DIR__ . "/qrcodes/test.png";
		$qrcode = new QrReader($image);
		$qrcode->decode([
			'TRY_HARDER' => true
		]);
		$this->assertSame(null, $qrcode->getError());
	}
}
