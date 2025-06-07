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

    /**
     * The following test is meant to check if it works with QRCodes containing raw binary data.
     * The test qrcode image was generated with `qrencode -8 -r 'binary-test.bin' -o 'test-binary-test.png'`.
     *
     * @return void
     */
    public function testBinary() {
        $image = __DIR__ . "/qrcodes/binary-test.png";
        $expected = hex2bin(
                '000102030405060708090a0b0c0d0e0f101112131415161718191a1b1c1d1e1f'.
                '202122232425262728292a2b2c2d2e2f303132333435363738393a3b3c3d3e3f'.
                '404142434445464748494a4b4c4d4e4f505152535455565758595a5b5c5d5e5f'.
                '606162636465666768696a6b6c6d6e6f707172737475767778797a7b7c7d7e7f'.
                '808182838485868788898a8b8c8d8e8f909192939495969798999a9b9c9d9e9f'.
                'a0a1a2a3a4a5a6a7a8a9aaabacadaeafb0b1b2b3b4b5b6b7b8b9babbbcbdbebf'.
                'c0c1c2c3c4c5c6c7c8c9cacbcccdcecfd0d1d2d3d4d5d6d7d8d9dadbdcdddedf'.
                'e0e1e2e3e4e5e6e7e8e9eaebecedeeeff0f1f2f3f4f5f6f7f8f9fafbfcfdfeff'
        );

        $qrcode = new QrReader($image);
        $qrcode->decode([
            'BINARY_MODE' => true
        ]);
        $this->assertSame(null, $qrcode->getError());
        $result = $qrcode->getResult();
        $this->assertInstanceOf(Result::class, $result);
        $text = $result->getText();
        $this->assertEquals($expected, $text);
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
