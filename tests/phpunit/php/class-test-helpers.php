<?php
/**
 * Tests for Helpers class.
 *
 * @package MaterialThemeBuilder
 */

namespace MaterialThemeBuilder;

use MaterialThemeBuilder\Helpers;
use function MaterialThemeBuilder\get_plugin_instance;

/**
 * Tests for Helpers class.
 */
class Test_Helpers extends \WP_UnitTestCase {

	/**
	 * Test hex_to_rgb.
	 *
	 * @see Helpers::hex_to_rgb()
	 */
	public function test_hex_to_rgb() {
		$colors = [
			'#000000' => [ 0, 0, 0 ],
			'#ffffff' => [ 255, 255, 255 ],
			'#ff0000' => [ 255, 0, 0 ],
			'#00ffff' => [ 0, 255, 255 ],
			'#0ff'    => [ 0, 255, 255 ],
			'#ff5733' => [ 255, 87, 51 ],
			'#2a2d5a' => [ 42, 45, 90 ],
			'#5a2a48' => [ 90, 42, 72 ],
			'#07971d' => [ 7, 151, 29 ],
			'fff'     => [ 255, 255, 255 ],
			'ffff'    => false,
		];

		foreach ( $colors as $hex => $rgb ) {
			$this->assertEquals( $rgb, Helpers::hex_to_rgb( $hex ) );
		}

		$this->assertEquals( [ 7, 151, 29 ], Helpers::hex_to_rgb( [ 7, 151, 29 ] ) );
	}

	/**
	 * Test mix_colors.
	 *
	 * @see Helpers::mix_colors()
	 */
	public function test_mix_colors() {
		$this->assertEquals( '#698aa2', Helpers::mix_colors( '#036', '#d2e1dd' ) );
		$this->assertEquals( '#355f84', Helpers::mix_colors( '#036', '#d2e1dd', .75 ) );
		$this->assertEquals( '#9eb6bf', Helpers::mix_colors( '#036', '#d2e1dd', .25 ) );
	}

	/**
	 * Test hexdec.
	 *
	 * @see Helpers::hexdec()
	 */
	public function test_hexdec() {
		$this->assertEquals( 255, Helpers::hexdec( 'f' ) );
		$this->assertEquals( 255, Helpers::hexdec( 'ff' ) );
		$this->assertEquals( 15, Helpers::hexdec( '0f' ) );
	}

	/**
	 * Test dechex.
	 *
	 * @see Helpers::dechex()
	 */
	public function test_dechex() {
		$this->assertEquals( '0f', Helpers::dechex( 15 ) );
		$this->assertEquals( 'ff', Helpers::dechex( 255 ) );
		$this->assertEquals( 'cd', Helpers::dechex( 205 ) );
	}
}