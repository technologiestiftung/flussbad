<?php

/**
 * ByteOrder
 */
final class Order {

	private $value; /**< ByteOrder */

	/**
	 *
	 */
	private function __construct($value) {
		$this->value = $value;
	}

	/**
	 * big endian byte order. Creates a new instance of a big endian byte order type.
	 */
	public static function BIG_ENDIAN() {
		return new self(__METHOD__);
	}

	/**
	 * little endian byte order. Create a new instance of a little endian byte order type.
	 */
	public static function LITTLE_ENDIAN() {
		return new self(__METHOD__);
	}

	/**
	 * get the byte order
	 */
	public function getValue() {
		return $this->value;
	}

	/**
	 *
	 */
	public function __toString() {
		return (string) $this->value;
	}
}
