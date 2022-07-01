<?php

namespace Zxing\Qrcode\Decoder;

class QRCodeDecoderMetaData
{
	/**
	 * QRCodeDecoderMetaData constructor.
	 * @param bool $mirrored
	 */
	public function __construct(private $mirrored)
	{
	}

	public function isMirrored(): bool
	{
		return $this->mirrored;
	}
}
