<?php

final class Order {
	private $value;

	private function __construct($value) {
		$this->value = $value;
	}

	public static function BIG_ENDIAN() {
		return new self(__METHOD__);
	}

	public static function LITTLE_ENDIAN() {
		return new self(__METHOD__);
	}

	public function getValue() {
		return $this->value;
	}

	public function __toString() {
		return (string) $this->value;
	}
}
