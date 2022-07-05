<?php

namespace Zxing\Common;

/**
 * Encapsulates a Character Set ECI, according to "Extended Channel
 * Interpretations" 5.3.1.1 of ISO 18004.
 */
final class CharacterSetECI
{
	/**#@+
  * Character set constants.
  */
	/**
	 * @var int
	 */
	public const CP437 = 0;
	/**
  * @var int
  */
	public const ISO8859_1 = 1;
	/**
  * @var int
  */
	public const ISO8859_2 = 4;
	/**
  * @var int
  */
	public const ISO8859_3 = 5;
	/**
  * @var int
  */
	public const ISO8859_4 = 6;
	/**
  * @var int
  */
	public const ISO8859_5 = 7;
	/**
  * @var int
  */
	public const ISO8859_6 = 8;
	/**
  * @var int
  */
	public const ISO8859_7 = 9;
	/**
  * @var int
  */
	public const ISO8859_8 = 10;
	/**
  * @var int
  */
	public const ISO8859_9 = 11;
	/**
  * @var int
  */
	public const ISO8859_10 = 12;
	/**
  * @var int
  */
	public const ISO8859_11 = 13;
	/**
  * @var int
  */
	public const ISO8859_12 = 14;
	/**
  * @var int
  */
	public const ISO8859_13 = 15;
	/**
  * @var int
  */
	public const ISO8859_14 = 16;
	/**
  * @var int
  */
	public const ISO8859_15 = 17;
	/**
  * @var int
  */
	public const ISO8859_16 = 18;
	/**
  * @var int
  */
	public const SJIS = 20;
	/**
  * @var int
  */
	public const CP1250 = 21;
	/**
  * @var int
  */
	public const CP1251 = 22;
	/**
  * @var int
  */
	public const CP1252 = 23;
	/**
  * @var int
  */
	public const CP1256 = 24;
	/**
  * @var int
  */
	public const UNICODE_BIG_UNMARKED = 25;
	/**
  * @var int
  */
	public const UTF8 = 26;
	/**
  * @var int
  */
	public const ASCII = 27;
	/**
  * @var int
  */
	public const BIG5 = 28;
	/**
  * @var int
  */
	public const GB18030 = 29;
	/**
  * @var int
  */
	public const EUC_KR = 30;
	/**
  * Map between character names and their ECI values.
  */
	private static array $nameToEci = [
		'ISO-8859-1' => self::ISO8859_1,
		'ISO-8859-2' => self::ISO8859_2,
		'ISO-8859-3' => self::ISO8859_3,
		'ISO-8859-4' => self::ISO8859_4,
		'ISO-8859-5' => self::ISO8859_5,
		'ISO-8859-6' => self::ISO8859_6,
		'ISO-8859-7' => self::ISO8859_7,
		'ISO-8859-8' => self::ISO8859_8,
		'ISO-8859-9' => self::ISO8859_9,
		'ISO-8859-10' => self::ISO8859_10,
		'ISO-8859-11' => self::ISO8859_11,
		'ISO-8859-12' => self::ISO8859_12,
		'ISO-8859-13' => self::ISO8859_13,
		'ISO-8859-14' => self::ISO8859_14,
		'ISO-8859-15' => self::ISO8859_15,
		'ISO-8859-16' => self::ISO8859_16,
		'SHIFT-JIS' => self::SJIS,
		'WINDOWS-1250' => self::CP1250,
		'WINDOWS-1251' => self::CP1251,
		'WINDOWS-1252' => self::CP1252,
		'WINDOWS-1256' => self::CP1256,
		'UTF-16BE' => self::UNICODE_BIG_UNMARKED,
		'UTF-8' => self::UTF8,
		'ASCII' => self::ASCII,
		'GBK' => self::GB18030,
		'EUC-KR' => self::EUC_KR,
	];
	/**#@-*/
	/**
	 * Additional possible values for character sets.
	 */
	private static array $additionalValues = [
		self::CP437 => 2,
		self::ASCII => 170,
	];
	private static int|string|null $name = null;

	/**
  * Gets character set ECI by value.
  *
  *
  * @return CharacterSetEci|null
  */
	public static function getCharacterSetECIByValue(string $value)
	{
		if ($value < 0 || $value >= 900) {
			throw new \InvalidArgumentException('Value must be between 0 and 900');
		}
		if (false !== ($key = array_search($value, self::$additionalValues))) {
			$value = $key;
		}
		array_search($value, self::$nameToEci);
		try {
			self::setName($value);

			return new self($value);
		} catch (\UnexpectedValueException) {
			return null;
		}
	}

	/**
	 * @param (int|string) $value
	 *
	 * @psalm-param array-key $value
	 *
	 * @return null|true
	 */
	private static function setName($value)
	{
		foreach (self::$nameToEci as $name => $key) {
			if ($key == $value) {
				self::$name = $name;

				return true;
			}
		}
		if (self::$name == null) {
			foreach (self::$additionalValues as $name => $key) {
				if ($key == $value) {
					self::$name = $name;

					return true;
				}
			}
		}
	}

	/**
	 * Gets character set ECI name.
	 *
	 * @return int|null|string set ECI name|null
	 */
	public static function name(): string|int|null
	{
		return self::$name;
	}

	/**
  * Gets character set ECI by name.
  *
  *
  * @return CharacterSetEci|null
  */
	public static function getCharacterSetECIByName(string $name)
	{
		$name = strtoupper($name);
		if (isset(self::$nameToEci[$name])) {
			return new self(self::$nameToEci[$name]);
		}

		return null;
	}
}
