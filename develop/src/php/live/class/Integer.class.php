<?php

class Integer {
	
	public static function endianTest() {
		$test = "\x12\x34\x56\x78";
		if ( "\x78" == $test[0] ) {
			return Order::LITTLE_ENDIAN();
		} else {
			return Order::BIG_ENDIAN();
		}
	}

	public static function sInt8($int) {
		return unpack('c',$int);
	}

	public static function uInt8($int) {
		return unpack('C',$int);
	}

	public static function sInt16($int, Order $order) {

		if ( Order::BIG_ENDIAN() == $order->getValue() ) {
			return unpack('n',$int);
		}

		if ( Order::LITTLE_ENDIAN() == $order->getValue() ) {
			return unpack('v',$int);
		}
	}

	public static function uInt16($int, Order $order) {

		if ( Order::BIG_ENDIAN() == $order->getValue() ) {
			return unpack('n',$int);
		}

		if ( Order::LITTLE_ENDIAN() == $order->getValue() ) {
			return unpack('v',$int);
		}
	}

	public static function sInt32($int, Order $order) {

		if ( Order::BIG_ENDIAN() == $order->getValue() ) {
			
			dumpVar($int, '$int '.__FILE__.':'.__LINE__);
			$temp = $int[0] . $int[1] . $int[2] . $int[3];
			dumpVar($temp, '$temp '.__FILE__.':'.__LINE__);
			return unpack('l',$temp);
		}

		if ( Order::LITTLE_ENDIAN() == $order->getValue() ) {
			
			dumpVar($int, '$int '.__FILE__.':'.__LINE__);
			$temp = $int[3] . $int[2] . $int[1] . $int[0];
			dumpVar($temp, '$temp '.__FILE__.':'.__LINE__);
			return unpack('l',$temp);
		}
	}

	public static function uInt32($int, Order $order) {

		if ( Order::BIG_ENDIAN() == $order->getValue() ) {
			return unpack('N',$int);
		}

		if ( Order::LITTLE_ENDIAN() == $order->getValue() ) {
			return unpack('V',$int);
		}
	}

	public static function sInt64($int, Order $order) {

		if ( Order::BIG_ENDIAN() == $order->getValue() ) {

			dumpVar($int, '$int '.__FILE__.':'.__LINE__);
			$temp = $int[0] . $int[1] . $int[2] . $int[3] . $int[4] . $int[5] . $int[6] . $int[7];
			return unpack('q',$temp);
		}

		if ( Order::LITTLE_ENDIAN() == $order->getValue() ) {

			dumpVar($int, '$int '.__FILE__.':'.__LINE__);
			$temp = $int[7] . $int[6] . $int[5] . $int[4] . $int[3] . $int[2] . $int[1] . $int[0];
			return unpack('q',$temp);
		}
	}

	public static function uInt64($int, Order $order) {

		if ( Order::BIG_ENDIAN() == $order->getValue() ) {
			return unpack('J',$int);
		}

		if ( Order::LITTLE_ENDIAN() == $order->getValue() ) {
			return unpack('P',$int);
		}
	}
}
