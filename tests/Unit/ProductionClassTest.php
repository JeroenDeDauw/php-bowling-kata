<?php

declare( strict_types = 1 );

namespace Such\NewProject\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Such\NewProject\ProductionClass;

/**
 * @covers \Such\NewProject\ProductionClass
 *
 * @licence GNU GPL v2+
 */
class ProductionClassTest extends TestCase {

	public function testGutterGameHasScoreZero() {
		$this->assertGameScore( 0, '-- -- -- -- -- -- -- -- -- --' );
	}

	private function assertGameScore( int $expectedScore, string $throws ) {
		$this->assertSame(
			$expectedScore,
			$this->game( $throws )
		);
	}

	private function game( string $throws ) {
		$sum = 0;
		$frameSum = 0;
		$wasSpare = false;
		foreach ( str_split( $throws ) as $throw ) {
			$throwValue = $throw === '-' ? 0 : (int)$throw;
			if ($wasSpare) {
				$throwValue *= 2;
				$wasSpare = false;
			}
			$sum += $throwValue;
			$frameSum += $throwValue;

			if ($throw === ' ') {
				$wasSpare = $frameSum === 10;
				$frameSum = 0;
			}
		}

		return $sum;
	}

	/**
	 * @dataProvider gameWithOnlyTheFirstNotAGutterProvider
	 */
	public function testFirstThrowIsCounted( int $expectedScore, string $throws ) {
		$this->assertGameScore( $expectedScore, $throws );
	}

	public function gameWithOnlyTheFirstNotAGutterProvider() {
		yield [ 1, '1- -- -- -- -- -- -- -- -- --' ];
		yield [ 5, '5- -- -- -- -- -- -- -- -- --' ];
		yield [ 6, '6- -- -- -- -- -- -- -- -- --' ];
		yield [ 9, '9- -- -- -- -- -- -- -- -- --' ];
	}

	public function testFirstTwoThrowsGetCounted() {
		$this->assertGameScore( 2, '11 -- -- -- -- -- -- -- -- --' );
		$this->assertGameScore( 5, '23 -- -- -- -- -- -- -- -- --' );
		$this->assertGameScore( 3, '-3 -- -- -- -- -- -- -- -- --' );
		$this->assertGameScore( 10, '73 -- -- -- -- -- -- -- -- --' );
	}

	public function testAllRolesAreCounted() {
		$this->assertGameScore( 5, '-- 1- -- -- -- -1 -- -- -- -3' );
	}

	public function testSpare() {
		$this->assertGameScore( 12, '-- 73 1- -- -- -- -- -- -- --' );
	}
}