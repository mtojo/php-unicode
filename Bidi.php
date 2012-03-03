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
 * Bidi.
 */
final class Bidi
{

  // {{{ constants

  /**
   * @const integer
   */
  const LEFT_TO_RIGHT = 0;

  /**
   * @const integer
   */
  const RIGHT_TO_LEFT = 1;

  /**
   * @const integer
   */
  const EUROPEAN_NUMBER = 2;

  /**
   * @const integer
   */
  const EUROPEAN_NUMBER_SEPARATOR = 3;

  /**
   * @const integer
   */
  const EUROPEAN_NUMBER_TERMINATOR = 4;

  /**
   * @const integer
   */
  const ARABIC_NUMBER = 5;

  /**
   * @const integer
   */
  const COMMON_NUMBER_SEPARATOR = 6;

  /**
   * @const integer
   */
  const BLOCK_SEPARATOR = 7;

  /**
   * @const integer
   */
  const SEGMENT_SEPARATOR = 8;

  /**
   * @const integer
   */
  const WHITE_SPACE_NEUTRAL = 9;

  /**
   * @const integer
   */
  const OTHER_NEUTRAL = 10;

  /**
   * @const integer
   */
  const LEFT_TO_RIGHT_EMBEDDING = 11;

  /**
   * @const integer
   */
  const LEFT_TO_RIGHT_OVERRIDE = 12;

  /**
   * @const integer
   */
  const RIGHT_TO_LEFT_ARABIC = 13;

  /**
   * @const integer
   */
  const RIGHT_TO_LEFT_EMBEDDING = 14;

  /**
   * @const integer
   */
  const RIGHT_TO_LEFT_OVERRIDE = 15;

  /**
   * @const integer
   */
  const POP_DIRECTIONAL_FORMAT = 16;

  /**
   * @const integer
   */
  const DIR_NON_SPACING_MARK = 17;

  /**
   * @const integer
   */
  const BOUNDARY_NEUTRAL = 18;

  /**
   * @const integer
   */
  const CHAR_DIRECTION_COUNT = 19;

  // }}}
  // {{{ constructor

  /**
   * Constructor.
   */
  private function __construct()
  {
  }

  // }}}
  // {{{ bidirectional()

  /**
   * Returns the bidirectional category assigned to the character.
   *
   * @param integer $char An unicode code points.
   * @return integer
   */
  public static function bidirectional($char)
  {
    /**
     * @todo This method does not provide an complete implementation.
     */
    $type = self::LEFT_TO_RIGHT;
    if ((($char >= 0x0590)  && ($char <= 0x05FF)) ||
        (($char >= 0x07C0)  && ($char <= 0x08FF)) ||
        (($char >= 0xFB1D)  && ($char <= 0xFB4F)) ||
        (($char >= 0x10800) && ($char <= 0x10FFF)))
    {
      $type = self::RIGHT_TO_LEFT;
    }
    elseif ((($char >= 0x0600) && ($char <= 0x07BF)) ||
            (($char >= 0xFB50) && ($char <= 0xFDCF)) ||
            (($char >= 0xFDF0) && ($char <= 0xFDFF)) ||
            (($char >= 0xFE70) && ($char <= 0xFEFF)))
    {
      $type = self::RIGHT_TO_LEFT_ARABIC;
    }
    elseif ((($char >= 0x2060)  && ($char <= 0x206F))  ||
            (($char >= 0xFDD0)  && ($char <= 0xFDEF))  ||
            (($char >= 0xFFF0)  && ($char <= 0xFFF8))  ||
            (($char >= 0xFE70)  && ($char <= 0xFEFF))  ||
            (($char >= 0xE0000) && ($char <= 0xE0FFF)) ||
            // FFFE-FFFF, 1FFFE-1FFFF, 2FFFE-2FFFF, 3FFFE-3FFFF,
            // 4FFFE-4FFFF, 5FFFE-5FFFF, 6FFFE-6FFFF, 7FFFE-7FFFF,
            // 8FFFE-8FFFF, 9FFFE-9FFFF, AFFFE-AFFFF, BFFFE-BFFFF,
            // CFFFE-CFFFF, DFFFE-DFFFF, EFFFE-EFFFF, FFFFE-FFFFF
            (($char % 0x10000) === 0xFFFE) ||
            (($char % 0x10000) === 0xFFFF))
    {
      $type = self::BOUNDARY_NEUTRAL;
    }
    return $type;
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
