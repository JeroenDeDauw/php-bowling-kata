<?php

declare( strict_types = 1 );

namespace Such\NewProject\Tests\Unit;

use PHPUnit\Framework\TestCase;

class Bowling {
	private $previousFrame = 'normal';

	public function game( string $throws ) {
		$frameStack = [];

		foreach ( explode( ' ', $throws ) as $frame ) {
			$frameStack[] = $this->getFrameSum( $frame );
		}

		return array_sum( $frameStack );
	}

	private function getFrameSum( string $frame ) {
		$frameSum = 0;

		foreach ( str_split( $frame ) as $throwNumberInFrame => $throw ) {
			$throwValue = $this->getThrowValue( $throw );

			if ( $this->throwNeedsToBeDoubled( $throwNumberInFrame ) ) {
				$throwValue *= 2;
			}

			$frameSum += $throwValue;
		}

		if ( $throw === 'x' ) {
			$this->previousFrame = 'strike';
		}
		elseif ( $frameSum === 10 ) {
			$this->previousFrame = 'spare';
		}
		else {
			$this->previousFrame = 'normal';
		}

		return $frameSum;
	}

	private function throwNeedsToBeDoubled( $throwNumberInFrame ): bool {
		return ( $throwNumberInFrame === 0 && in_array( $this->previousFrame, [ 'strike', 'spare' ] ) )
			|| $throwNumberInFrame === 1 && $this->previousFrame === 'strike';
	}

	private function getThrowValue(string $throw) {
		if ($throw === 'x') {
			return 10;
		}
		return $throw === '-' ? 0 : (int)$throw;
	}
}

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
			(new Bowling() )->game( $throws )
		);
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