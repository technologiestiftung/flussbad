<?php


/**
 * Cast binary values to a floating point type
 */
class Floating {

	/**
	 * Byte order of the machine
	 */
	private static $machineByteOrder = null;

	/**
	 * Check the byte order of the machine
	 */
	public static function endianTest() {

		if ( !isset(self::$machineByteOrder) ) {

			// 0x45 == d69
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

	/**
	 * Cast the parameter to 32 bit float type
	 */
	public static function Float($float, Order $order) {
		self::endianTest();
		if ( self::$machineByteOrder == $order->getValue() ) {
			return unpack('f',$float)[1];
		} else {
			return unpack('f',strrev($float))[1];
		}
	}

	/**
	 * Cast the parameter to 64 bit double type
	 */
	public static function Double($double, Order $order) {
		self::endianTest();
		if ( self::$machineByteOrder == $order->getValue() ) {
			return unpack('d',$double)[1];
		} else {
			return unpack('d',strrev($double))[1];
		}
	}

}
