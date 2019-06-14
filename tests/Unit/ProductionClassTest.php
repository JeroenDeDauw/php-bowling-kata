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
		$doubleNextThrow = false; // x 23
		$doubleThrowAfterNextOne = false;
		foreach ( explode( ' ', $throws ) as $frame ) {
			foreach ( str_split( $frame ) as $throw ) {
				$throwValue = $this->getThrowValue( $throw );

				if ($doubleNextThrow) {
					$throwValue *= 2;
					$doubleNextThrow = false;
				}
				if ($doubleThrowAfterNextOne) {
					$doubleNextThrow = true;
					$doubleThrowAfterNextOne = false;
				}
				$doubleThrowAfterNextOne = $doubleThrowAfterNextOne == 'x';
				$sum += $throwValue;
				$frameSum += $throwValue;
			}
			$doubleNextThrow = $frameSum === 10;
			$frameSum = 0;
		}

		return $sum;
	}

	private function getThrowValue(string $throw) {
		if ($throw === 'x') {
			return 10;
		}
		return $throw === '-' ? 0 : (int)$throw;
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

	public function testSecondThrowAfterSpareNotDoubled() {
		$this->assertGameScore( 14, '-- 73 12 -- -- -- -- -- -- --' );
	}

	public function testSpareGivesOneExtraThrow() {
		$this->assertGameScore( 15, '-- -- -- -- -- -- -- -- -- 735' );
	}

	public function testSingleStrike() {
		$this->assertGameScore( 10, '-- -- -- -- -- x -- -- -- --' );
	}

	public function testStrikeDoublesNextTwoThrows() {
		$this->assertGameScore( 20, '-- -- -- -- -- x 23 -- -- --' );
	}


}