<?php

// vim: set expandtab tabstop=2 shiftwidth=2 softtabstop=2:

/**
 * php-unicode
 *
 * Copyright (c) 2008, Masaki Tojo
 * All rights reserved.
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public License
 * as published by the Free Software Foundation; either version 2.1
 * of the License, or (at your option) any later version.
 *
 * @copyright Copyright (c) 2008 Masaki Tojo
 * @license http://opensource.org/licenses/lgpl-license.php GNU Lesser General Public License
 */

namespace unicode;

/**
 * Unicode character functions.
 */
final class Character
{

  // {{{ properties

  /**
   * Space characters.
   *
   * @var array
   */
  private static $space = array(
    0x0009,     // [Cc] CHARACTER TABULATION
    0x000A,     // [Cc] LINE FEED (LF)
    0x000B,     // [Cc] LINE TABULATION
    0x000C,     // [Cc] FORM FEED (FF)
    0x000D,     // [Cc] CARRIAGE RETURN (CR)
    0x0020,     // [Zs] SPACE
    0x0085,     // [Cc] NEXT LINE (NEL)
    0x00A0,     // [Zs] NO-BREAK SPACE
    0x1680,     // [Zs] OGHAM SPACE MARK
    0x180E,     // [Zs] MONGOLIAN VOWEL SEPARATOR
    0x2000,     // [Zs] EN QUAD
    0x2001,     // [Zs] EM QUAD
    0x2002,     // [Zs] EN SPACE
    0x2003,     // [Zs] EM SPACE
    0x2004,     // [Zs] THREE-PER-EM SPACE
    0x2005,     // [Zs] FOUR-PER-EM SPACE
    0x2006,     // [Zs] SIX-PER-EM SPACE
    0x2007,     // [Zs] FIGURE SPACE
    0x2008,     // [Zs] PUNCTUATION SPACE
    0x2009,     // [Zs] THIN SPACE
    0x200A,     // [Zs] HAIR SPACE
    0x2028,     // [Zl] LINE SEPARATOR
    0x2029,     // [Zp] PARAGRAPH SEPARATOR
    0x202F,     // [Zs] NARROW NO-BREAK SPACE
    0x205F,     // [Zs] MEDIUM MATHEMATICAL SPACE
    0x3000,     // [Zs] IDEOGRAPHIC SPACE
  );

  /**
   * Control characters.
   *
   * @var array
   */
  private static $control = array(
    0x0000,     //
    0x0001,     //
    0x0002,     //
    0x0003,     //
    0x0004,     //
    0x0005,     //
    0x0006,     //
    0x0007,     //
    0x0008,     //
    0x0009,     //
    0x000A,     //
    0x000B,     //
    0x000C,     //
    0x000D,     //
    0x000E,     //
    0x000F,     //
    0x0010,     //
    0x0011,     //
    0x0012,     //
    0x0013,     //
    0x0014,     //
    0x0015,     //
    0x0016,     //
    0x0017,     //
    0x0018,     //
    0x0019,     //
    0x001A,     //
    0x001B,     //
    0x001C,     //
    0x001D,     //
    0x001E,     //
    0x001F,     //
    0x007F,     // DELETE
    0x0080,     //
    0x0081,     //
    0x0082,     //
    0x0083,     //
    0x0084,     //
    0x0085,     //
    0x0086,     //
    0x0087,     //
    0x0088,     //
    0x0089,     //
    0x008A,     //
    0x008B,     //
    0x008C,     //
    0x008D,     //
    0x008E,     //
    0x008F,     //
    0x0090,     //
    0x0091,     //
    0x0092,     //
    0x0093,     //
    0x0094,     //
    0x0095,     //
    0x0096,     //
    0x0097,     //
    0x0098,     //
    0x0099,     //
    0x009A,     //
    0x009B,     //
    0x009C,     //
    0x009D,     //
    0x009E,     //
    0x009F,     //
    0x06DD,     // ARABIC END OF AYAH
    0x070F,     // SYRIAC ABBREVIATION MARK
    0x180E,     // MONGOLIAN VOWEL SEPARATOR
    0x200C,     // ZERO WIDTH NON-JOINER
    0x200D,     // ZERO WIDTH JOINER
    0x2028,     // LINE SEPARATOR
    0x2029,     // PARAGRAPH SEPARATOR
    0x2060,     // WORD JOINER
    0x2061,     // FUNCTION APPLICATION
    0x2062,     // INVISIBLE TIMES
    0x2063,     // INVISIBLE SEPARATOR
    0x206A,     //
    0x206B,     //
    0x206C,     //
    0x206D,     //
    0x206E,     //
    0x206F,     //
    0xFEFF,     // ZERO WIDTH NO-BREAK SPACE
    0xFFF9,     //
    0xFFFA,     //
    0xFFFB,     //
    0xFFFC,     //
    0x1D173,    //
    0x1D174,    //
    0x1D175,    //
    0x1D176,    //
    0x1D177,    //
    0x1D178,    //
    0x1D179,    //
    0x1D17A,    //
  );

  // }}}
  // {{{ constructor

  /**
   * Constructor.
   */
  private function __construct()
  {
  }

  // }}}
  // {{{ isSpace()

  /**
   * Determines whether the character is a space.
   *
   * @param integer $char An unicode code points.
   * @return boolean
   */
  public static function isSpace($char)
  {
    return \in_array($char, self::$space);
  }

  // }}}
  // {{{ isControl()

  /**
   * Determines whether the character is a control.
   *
   * @param integer $char An unicode code points.
   * @return boolean
   */
  public static function isControl($char)
  {
    return \in_array($char, self::$control);
  }

  // }}}
  // {{{ isPrivateUse()

  /**
   * Determines whether the character is a private use.
   *
   * @param integer $char An unicode code points.
   * @return boolean
   */
  public static function isPrivateUse($char)
  {
    return ((($char >= 0xE000  ) && ($char <= 0xF8FF )) ||  // PLANE 0
            (($char >= 0xF0000 ) && ($char <= 0xFFFFD)) ||  // PLANE 15
            (($char >= 0x100000) && ($char <= 0x10FFFD)));  // PLANE 16
  }

  // }}}
  // {{{ isNonCharacter()

  /**
   * Determines whether the character is a non-character.
   *
   * @param integer $char An unicode code points.
   * @return boolean
   */
  public static function isNonCharacter($char)
  {
    return ((($char >= 0xFDD0) && ($char <= 0xFDEF)) ||
            // FFFE-FFFF, 1FFFE-1FFFF, 2FFFE-2FFFF, 3FFFE-3FFFF,
            // 4FFFE-4FFFF, 5FFFE-5FFFF, 6FFFE-6FFFF, 7FFFE-7FFFF,
            // 8FFFE-8FFFF, 9FFFE-9FFFF, AFFFE-AFFFF, BFFFE-BFFFF,
            // CFFFE-CFFFF, DFFFE-DFFFF, EFFFE-EFFFF, FFFFE-FFFFF
            (($char % 0x10000) === 0xFFFE) ||
            (($char % 0x10000) === 0xFFFF));
  }

  // }}}
  // {{{ isSurrogate()

  /**
   * Determines whether the character is a surrogate.
   *
   * @param integer $char An unicode code points.
   * @return boolean
   */
  public static function isSurrogate($char)
  {
    // (($char >= SURROGATE_HIGH_START) &&
    //  ($char <= SURROGATE_LOW_END))
    return (($char & 0xF800) === 0xD800);
  }

  // }}}

}

/*
 * Local variables:
 * tab-width: 2
 * c-basic-offset: 2
 * c-hanging-comment-ender-p: nil
 * End:
 */
