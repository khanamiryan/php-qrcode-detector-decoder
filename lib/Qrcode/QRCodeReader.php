<?php
/*
 * Copyright 2007 ZXing authors
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Zxing\Qrcode;

use Zxing\BinaryBitmap;
use Zxing\ChecksumException;
use Zxing\Common\BitMatrix;
use Zxing\FormatException;
use Zxing\NotFoundException;
use Zxing\Qrcode\Decoder\Decoder;
use Zxing\Qrcode\Detector\Detector;
use Zxing\Reader;
use Zxing\Result;

/**
 * This implementation can detect and decode QR Codes in an image.
 *
 * @author Sean Owen
 */
class QRCodeReader implements Reader
{
	private static array $NO_POINTS = [];
	private readonly Decoder $decoder;

	public function __construct()
	{
		$this->decoder = new Decoder();
	}

	/**
  * @param null         $hints
  *
  * @return Result
  * @throws \Zxing\FormatException
  * @throws \Zxing\NotFoundException
  */
	public function decode(BinaryBitmap $image, $hints = null)
	{
		$decoderResult = null;
		if ($hints !== null && array_key_exists('PURE_BARCODE', $hints) && $hints['PURE_BARCODE']) {
			$bits = self::extractPureBits($image->getBlackMatrix());
			$decoderResult = $this->decoder->decode($bits, $hints);
			$points = self::$NO_POINTS;
		} else {
			$detector = new Detector($image->getBlackMatrix());
			$detectorResult = $detector->detect($hints);

			$decoderResult = $this->decoder->decode($detectorResult->getBits(), $hints);
			$points = $detectorResult->getPoints();
		}
		$result = new Result($decoderResult->getText(), $decoderResult->getRawBytes(), $points, 'QR_CODE');//BarcodeFormat.QR_CODE

		$byteSegments = $decoderResult->getByteSegments();
		if ($byteSegments !== null) {
			$result->putMetadata('BYTE_SEGMENTS', $byteSegments);//ResultMetadataType.BYTE_SEGMENTS
		}
		$ecLevel = $decoderResult->getECLevel();
		if ($ecLevel !== null) {
			$result->putMetadata('ERROR_CORRECTION_LEVEL', $ecLevel);//ResultMetadataType.ERROR_CORRECTION_LEVEL
		}
		if ($decoderResult->hasStructuredAppend()) {
			$result->putMetadata(
				'STRUCTURED_APPEND_SEQUENCE',//ResultMetadataType.STRUCTURED_APPEND_SEQUENCE
				$decoderResult->getStructuredAppendSequenceNumber()
			);
			$result->putMetadata(
				'STRUCTURED_APPEND_PARITY',//ResultMetadataType.STRUCTURED_APPEND_PARITY
				$decoderResult->getStructuredAppendParity()
			);
		}

		return $result;
	}

	/**
	 * Locates and decodes a QR code in an image.
	 * This method detects a code in a "pure" image -- that is, pure monochrome image
	 * which contains only an unrotated, unskewed, image of a code, with some white border
	 * around it. This is a specialized method that works exceptionally fast in this special
	 * case.
	 *
	 * @return BitMatrix a String representing the content encoded by the QR code
	 * @throws NotFoundException if a QR code cannot be found
	 * @throws FormatException if a QR code cannot be decoded
	 * @throws ChecksumException if error correction fails
	 *
	 * @see com.google.zxing.datamatrix.DataMatrixReader#extractPureBits(BitMatrix)
	 */
	private static function extractPureBits(BitMatrix $image): BitMatrix
	{
		$leftTopBlack = $image->getTopLeftOnBit();
		$rightBottomBlack = $image->getBottomRightOnBit();
		if ($leftTopBlack === null || $rightBottomBlack == null) {
			throw new NotFoundException("Top left or bottom right on bit not found");
		}

		$moduleSize = self::moduleSize($leftTopBlack, $image);

		$top = $leftTopBlack[1];
		$bottom = $rightBottomBlack[1];
		$left = $leftTopBlack[0];
		$right = $rightBottomBlack[0];

		// Sanity check!
		if ($left >= $right || $top >= $bottom) {
			throw new NotFoundException("Left vs. right ($left >= $right) or top vs. bottom ($top >= $bottom) sanity violated.");
		}

		if ($bottom - $top != $right - $left) {
			// Special case, where bottom-right module wasn't black so we found something else in the last row
			// Assume it's a square, so use height as the width
			$right = $left + ($bottom - $top);
		}

		$matrixWidth = round(($right - $left + 1) / $moduleSize);
		$matrixHeight = round(($bottom - $top + 1) / $moduleSize);
		if ($matrixWidth <= 0 || $matrixHeight <= 0) {
			throw new NotFoundException("Matrix dimensions <= 0 ($matrixWidth, $matrixHeight)");
		}
		if ($matrixHeight != $matrixWidth) {
			// Only possibly decode square regions
			throw new NotFoundException("Matrix height  $matrixHeight != matrix width $matrixWidth");
		}

		// Push in the "border" by half the module width so that we start
		// sampling in the middle of the module. Just in case the image is a
		// little off, this will help recover.
		$nudge = (int)($moduleSize / 2.0);// $nudge = (int) ($moduleSize / 2.0f);
		$top += $nudge;
		$left += $nudge;

		// But careful that this does not sample off the edge
		// "right" is the farthest-right valid pixel location -- right+1 is not necessarily
		// This is positive by how much the inner x loop below would be too large
		$nudgedTooFarRight = $left + (int)(($matrixWidth - 1) * $moduleSize) - $right;
		if ($nudgedTooFarRight > 0) {
			if ($nudgedTooFarRight > $nudge) {
				// Neither way fits; abort
				throw new NotFoundException("Nudge too far right ($nudgedTooFarRight > $nudge), no fit found");
			}
			$left -= $nudgedTooFarRight;
		}
		// See logic above
		$nudgedTooFarDown = $top + (int)(($matrixHeight - 1) * $moduleSize) - $bottom;
		if ($nudgedTooFarDown > 0) {
			if ($nudgedTooFarDown > $nudge) {
				// Neither way fits; abort
				throw new NotFoundException("Nudge too far down ($nudgedTooFarDown > $nudge), no fit found");
			}
			$top -= $nudgedTooFarDown;
		}

		// Now just read off the bits
		$bits = new BitMatrix($matrixWidth, $matrixHeight);
		for ($y = 0; $y < $matrixHeight; $y++) {
			$iOffset = (int)($top + (int)($y * $moduleSize));
			for ($x = 0; $x < $matrixWidth; $x++) {
				if ($image->get((int)($left + (int)($x * $moduleSize)), $iOffset)) {
					$bits->set($x, $y);
				}
			}
		}

		return $bits;
	}

	/**
	 * @psalm-param array{0: mixed, 1: mixed} $leftTopBlack
	 */
	private static function moduleSize(array $leftTopBlack, BitMatrix $image)
	{
		$height = $image->getHeight();
		$width = $image->getWidth();
		$x = $leftTopBlack[0];
		$y = $leftTopBlack[1];
		/*$x           = $leftTopBlack[0];
		$y           = $leftTopBlack[1];*/
		$inBlack = true;
		$transitions = 0;
		while ($x < $width && $y < $height) {
			if ($inBlack != $image->get((int)round($x), (int)round($y))) {
				if (++$transitions == 5) {
					break;
				}
				$inBlack = !$inBlack;
			}
			$x++;
			$y++;
		}
		if ($x == $width || $y == $height) {
			throw new NotFoundException("$x == $width || $y == $height");
		}

		return ($x - $leftTopBlack[0]) / 7.0; //return ($x - $leftTopBlack[0]) / 7.0f;
	}

	public function reset(): void
	{
		// do nothing
	}

	final protected function getDecoder(): Decoder
	{
		return $this->decoder;
	}
}
