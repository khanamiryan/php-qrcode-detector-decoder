<?php

namespace Khanamiryan\QrCodeTests;

use PHPUnit\Framework\TestCase;
use Zxing\QrReader;
use Zxing\Result;

class QrReaderTest extends TestCase
{
	public function setUp(): void
	{
		error_reporting(E_ALL);
		ini_set('memory_limit', '2G');
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
		$hints = [
			'TRY_HARDER' => true,
			'NR_ALLOW_SKIP_ROWS' => 0
		];
		$qrcode->decode($hints);
		$this->assertSame(null, $qrcode->getError());
		$this->assertInstanceOf(Result::class, $qrcode->getResult());
		$this->assertEquals("https://www.gosuslugi.ru/covid-cert/verify/9770000014233333?lang=ru&ck=733a9d218d312fe134f1c2cc06e1a800", $qrcode->getResult()->getText());
		$this->assertSame("https://www.gosuslugi.ru/covid-cert/verify/9770000014233333?lang=ru&ck=733a9d218d312fe134f1c2cc06e1a800", $qrcode->text($hints));
	}

	public function testText3()
	{
		$image = __DIR__ . "/qrcodes/test.png";
		$qrcode = new QrReader($image);
		$qrcode->decode([
			'TRY_HARDER' => true
		]);
		$this->assertSame(null, $qrcode->getError());
		$this->assertSame("https://www.gosuslugi.ru/covid-cert/verify/9770000014233333?lang=ru&ck=733a9d218d312fe134f1c2cc06e1a800", $qrcode->text());
	}

	// TODO: fix this test
	// public function testText4()
	// {
	// 	$image = __DIR__ . "/qrcodes/174419877-f6b5dae1-2251-4b67-95f1-5e1143e40fae.jpg";
	// 	$qrcode = new QrReader($image);
	// 	$qrcode->decode([
	// 		'TRY_HARDER' => true,
	// 		'NR_ALLOW_SKIP_ROWS' => 0,
	// 		// 'ALLOWED_DEVIATION' => 0.1,
	// 		// 'MAX_VARIANCE' => 0.7
	// 	]);
	// 	$this->assertSame(null, $qrcode->getError());
	// 	$this->assertSame("some text", $qrcode->text());
	// }
}
