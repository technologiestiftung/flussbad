<?php

/**
 * Integer data type class
 */
class Integer {

	private static $machineByteOrder = null;

	private static function checkParam($param) {
		if ( null === $param || !isset($param) || !is_string($param) ) {
			throw new InvalidArgumentException('');
		}
	}

	/**
	 * Check the byte order of the machine
	 */
	public static function endianTest() {

		if ( !isset(self::$machineByteOrder) ) {
			$test = "\x00\x00\x00\x45";
			$pack = pack('L', $test);

			if ( 69 == $pack[1] ) {
				self::$machineByteOrder = Order::BIG_ENDIAN();
			} else {
				self::$machineByteOrder = Order::LITTLE_ENDIAN();
			}

			return self::$machineByteOrder;

		} else {
			return self::$machineByteOrder;
		}
	}

	/*
	 * Cast the parameter to 8 bit singed integer
	 */
	public static function sInt8($int) {
		self::checkParam($int);
		return unpack('c',$int)[1];
	}

	/**
	 * Cast the parameter to 8 bit unsigned integer
	 */
	public static function uInt8($int) {
		self::checkParam($int);
		return unpack('C',$int)[1];
	}

	/**
	 * Cast the parameter to 16 bit signed integer
	 */
	public static function sInt16($int, Order $order) {
		self::checkParam($int);

		self::endianTest();

		if ( self::$machineByteOrder == $order->getValue() ) {
			return unpack('s',$int)[1];
		} else {
			return unpack('s',strrev($int))[1];
		}
	}

	/**
	 * Cast the parameter to 16 bit unsigned integer
	 */
	public static function uInt16($int, Order $order) {
		self::checkParam($int);

		self::endianTest();

		if ( Order::BIG_ENDIAN() == $order->getValue() ) {
			return unpack('n',$int)[1];
		}

		if ( Order::LITTLE_ENDIAN() == $order->getValue() ) {
			return unpack('v',$int)[1];
		}
	}

	/**
	 * Cast the parameter to 32 bit signed integer
	 */
	public static function sInt32($int, Order $order) {
		self::checkParam($int);

		self::endianTest();

		if ( self::$machineByteOrder == $order->getValue() ) {
			return unpack('l',$int)[1];
		} else {
			return unpack('l',strrev($int))[1];
		}
	}

	/**
	 * Cast the parameter to 32 bit unsigned integer
	 */
	public static function uInt32($int, Order $order) {
		self::checkParam($int);

		if ( Order::BIG_ENDIAN() == $order->getValue() ) {
			return unpack('N',$int)[1];
		}

		if ( Order::LITTLE_ENDIAN() == $order->getValue() ) {
			return unpack('V',$int)[1];
		}
	}

	/**
	 * Cast the parameter to 64 bit signed integer
	 */
	public static function sInt64($int, Order $order) {
		self::checkParam($int);

		self::endianTest();

		if ( self::$machineByteOrder == $order->getValue() ) {
			return unpack('q',$int)[1];
		} else {
			return unpack('q',strrev($int))[1];
		}
	}

	/**
	 * Cast the parameter to 64 bit unsigned integer
	 */
	public static function uInt64($int, Order $order) {
		self::checkParam($int);

		if ( Order::BIG_ENDIAN() == $order->getValue() ) {
			return unpack('J',$int)[1];
		}

		if ( Order::LITTLE_ENDIAN() == $order->getValue() ) {
			return unpack('P',$int)[1];
		}
	}
}
