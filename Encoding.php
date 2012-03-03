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
 * Unicode encoding utilities.
 */
final class Encoding
{

  // {{{ constants

  /**
   * @const integer
   */
  const BIG_ENDIAN = 1;

  /**
   * @const integer
   */
  const LITTLE_ENDIAN = 2;

  /**
   * @const integer Unicode zero width no-break space.
   */
  const BYTE_ORDER_MARK = 0x0000FEFF;

  /**
   * @const integer Unicode replacement character.
   */
  const REPLACEMENT_CHARACTER = 0x0000FFFD;

  /**
   * @const integer Never an UCS-2 character.
   */
  const NOT_A_CHARACTER = 0x0000FFFF;

  /**
   * @const integer
   */
  const MAXIMUM_UCS2 = 0x0000FFFF;

  /**
   * @const integer
   */
  const MAXIMUM_UCS4 = 0x7FFFFFFF;

  /**
   * @const integer
   */
  const MAXIMUM_UTF = 0x0010FFFF;

  /**
   * @const integer
   */
  const HALF_SHIFT = 10;

  /**
   * @const integer
   */
  const HALF_BASE = 0x0010000;

  /**
   * @const integer
   */
  const HALF_MASK = 0x3FF;

  /**
   * @const integer
   */
  const SURROGATE_HIGH_START = 0xD800;

  /**
   * @const integer
   */
  const SURROGATE_HIGH_END = 0xDBFF;

  /**
   * @const integer
   */
  const SURROGATE_LOW_START = 0xDC00;

  /**
   * @const integer
   */
  const SURROGATE_LOW_END = 0xDFFF;

  /**
   * @const integer
   */
  const SURROGATE_LOW_BASE = 9216;  // -SURROGATE_LOW_START + HALF_BASE

  // }}}
  // {{{ constructor

  /**
   * Constructor.
   */
  private function __construct()
  {
  }

  // }}}
  // {{{ UTF8toUCS4()

  /**
   * Converts a sequence of bytes encoded as UTF-8 to the UCS4 string.
   *
   * The following table is from
   * {@link http://www.unicode.org/versions/corrigendum1.html Unicode 3.2}:
   *
   * <pre>
   * Code Points            1st Byte  2nd Byte  3rd Byte  4th Byte
   *
   *   U+0000..U+007F       00..7F
   *   U+0080..U+07FF       C2..DF    80..BF
   *   U+0800..U+0FFF       E0        A0..BF    80..BF
   *   U+1000..U+CFFF       E1..EC    80..BF    80..BF
   *   U+D000..U+D7FF       ED        80..9F    80..BF
   *   U+D800..U+DFFF       ******* ill-formed *******
   *   U+E000..U+FFFF       EE..EF    80..BF    80..BF
   *  U+10000..U+3FFFF      F0        90..BF    80..BF    80..BF
   *  U+40000..U+FFFFF      F1..F3    80..BF    80..BF    80..BF
   * U+100000..U+10FFFF     F4        80..8F    80..BF    80..BF
   * </pre>
   *
   * The following regular expression will match the legal UTF-8 character:
   *
   * <code>
   * /[\x00-\x7F]|[\xC2-\xDF][\x80-\xBF]|\xE0[\xA0-\xBF][\x80-\xBF]|
   *  [\xE1-\xEC][\x80-\xBF]{2}|\xED[\x80-\x9F][\x80-\xBF]|
   *  [\xEE\xEF][\x80-\xBF]{2}|\xF0[\x90-\xBF][\x80-\xBF]{2}|
   *  [\xF1-\xF3][\x80-\xBF]{3}|\xF4[\x80-\x8F][\x80-\xBF]{2}/x
   * </code>
   *
   * @param string $string A string.
   * @return array
   */
  public static function UTF8toUCS4($string)
  {
    $result = array();
    $b = \unpack('C*', $string);
    if (!empty($b))
    {
      for ($i = 1, $j = \count($b); $i <= $j; ++$i)
      {
        if (($b[$i] & 0x80) === 0x00)
        {
          // 0xxxxxxx
          $ch = $b[$i];
        }
        elseif ((($b[$i] & 0xE0) === 0xC0) && isset($b[($i + 1)]))
        {
          // 110xxxxx 10xxxxxx
          $ch = (($b[$i] & 0x1F) << 6)
              | ($b[++$i] & 0x3F);
          if ($ch < 0x80)
          {
            throw new InvalidCharacterException;
          }
        }
        elseif ((($b[$i] & 0xF0) === 0xE0) && isset($b[($i + 2)]))
        {
          // 1110xxxx 10xxxxxx 10xxxxxx
          $ch = (($b[$i] & 0x0F) << 12)
              | (($b[++$i] & 0x3F) << 6)
              | ($b[++$i] & 0x3F);
          if ($ch < 0x800)
          {
            throw new InvalidCharacterException;
          }
        }
        elseif ((($b[$i] & 0xF8) === 0xF0) && isset($b[($i + 3)]))
        {
          // 11110xxx 10xxxxxx 10xxxxxx 10xxxxxx
          $ch = (($b[$i] & 0x07) << 18)
              | (($b[++$i] & 0x3F) << 12)
              | (($b[++$i] & 0x3F) << 6)
              | ($b[++$i] & 0x3F);
          if ($ch < 0x10000)
          {
            throw new InvalidCharacterException;
          }
        }
        // NOTE: From Unicode 3.1, non-shortest form is illegal.
        //elseif ((($b[$i] & 0xFC) === 0xF8) && isset($b[($i + 4)]))
        //{
        //  // 111110xx 10xxxxxx 10xxxxxx 10xxxxxx 10xxxxxx
        //  $ch = (($b[$i] & 0x03) << 24)
        //      | (($b[++$i] & 0x3F) << 18)
        //      | (($b[++$i] & 0x3F) << 12)
        //      | (($b[++$i] & 0x3F) << 6)
        //      | ($b[++$i] & 0x3F);
        //  if ($ch < 0x200000)
        //  {
        //    throw new InvalidCharacterException;
        //  }
        //}
        //elseif ((($b[$i] & 0xFE) === 0xFC) && isset($b[($i + 5)]))
        //{
        //  // 1111110x 10xxxxxx 10xxxxxx 10xxxxxx 10xxxxxx 10xxxxxx
        //  $ch = (($b[$i] & 0x01) << 30)
        //      | (($b[++$i] & 0x3F) << 24)
        //      | (($b[++$i] & 0x3F) << 18)
        //      | (($b[++$i] & 0x3F) << 12)
        //      | (($b[++$i] & 0x3F) << 6)
        //      | ($b[++$i] & 0x3F);
        //  if ($ch < 0x4000000)
        //  {
        //    throw new InvalidCharacterException;
        //  }
        //}
        else
        {
          throw new InvalidCharacterException;
        }
        $result[] = $ch;
      }
      if ($result[0] === self::BYTE_ORDER_MARK)
      {
        \array_shift($result);
      }
    }
    return $result;
  }

  // }}}
  // {{{ UCS4toUTF8()

  /**
   * Converts a UCS4 string into the UTF-8 byte sequence.
   *
   * @param array $ucs A UCS4 string.
   * @return string
   */
  public static function UCS4toUTF8($ucs)
  {
    $result = '';
    foreach ($ucs as $cp)
    {
      if (Character::isSurrogate($cp))
      {
        throw new InvalidCharacterException;
      }
      if ($cp <= 0x7F)
      {
        $result .= \chr($cp & 0x7F);
      }
      elseif ($cp <= 0x7FF)
      {
        $result .= \chr(($cp >> 6) | 0xC0)
                 . \chr(($cp & 0x3F) | 0x80);
      }
      elseif ($cp <= 0xFFFF)
      {
        $result .= \chr(($cp >> 12) | 0xE0)
                 . \chr((($cp >> 6) & 0x3F) | 0x80)
                 . \chr(($cp & 0x3F) | 0x80);
      }
      elseif ($cp <= 0x10FFFF)
      {
        $result .= \chr(($cp >> 18) | 0xF0)
                 . \chr((($cp >> 12) & 0x3F) | 0x80)
                 . \chr((($cp >> 6) & 0x3F) | 0x80)
                 . \chr(($cp & 0x3F) | 0x80);
      }
      // NOTE: From Unicode 3.1, non-shortest form is illegal.
      //elseif ($cp <= 0x1FFFFF)
      //{
      //  $result .= \chr(($cp >> 18) | 0xF0)
      //           . \chr((($cp >> 12) & 0x3F) | 0x80)
      //           . \chr((($cp >> 6) & 0x3F) | 0x80)
      //           . \chr(($cp & 0x3F) | 0x80);
      //}
      //elseif ($cp <= 0x3FFFFFF)
      //{
      //  $result .= \chr(($cp >> 24) | 0xF8)
      //           . \chr(($cp >> 18) | 0x80)
      //           . \chr((($cp >> 12) & 0x3F) | 0x80)
      //           . \chr((($cp >> 6) & 0x3F) | 0x80)
      //           . \chr(($cp & 0x3F) | 0x80);
      //}
      //elseif ($cp <= 0x7FFFFFFF)
      //{
      //  $result .= \chr(($cp >> 30) | 0xFC)
      //           . \chr((($cp >> 24) & 0x3F) | 0x80)
      //           . \chr((($cp >> 18) & 0x3F) | 0x80)
      //           . \chr((($cp >> 12) & 0x3F) | 0x80)
      //           . \chr((($cp >> 6) & 0x3F) | 0x80)
      //           . \chr(($cp & 0x3F) | 0x80);
      //}
      else
      {
        throw new InvalidCharacterException;
      }
    }
    return $result;
  }

  // }}}
  // {{{ UTF16toUCS4()

  /**
   * Converts a sequence of bytes encoded as UTF-16 to the UCS4 string.
   *
   * @param string $string A string.
   * @param integer $endian An endian (BIG_ENDIAN or LITTLE_ENDIAN).
   * @return array
   */
  public static function UTF16toUCS4($string, $endian = self::BIG_ENDIAN)
  {
    // Input length must be dividable by 2.
    if ((\strlen($string) % 2) !== 0)
    {
      throw new InvalidEncodingException;
    }
    $result = array();
    $format = 'n*';
    if ((\substr($string, 0, 2) === \pack('v', self::BYTE_ORDER_MARK)) ||
      ($endian === self::LITTLE_ENDIAN))
    {
      $format = 'v*';
    }
    $b = \unpack($format, $string);
    if (!empty($b))
    {
      $i = 1;
      if ($b[$i] === self::BYTE_ORDER_MARK)
      {
        ++$i;
      }
      for ($j = \count($b); $i <= $j; ++$i)
      {
        // big endian:
        //   $b = unpack('C*', $string);
        //   $ch = ((($b[$i] << 8) & 0xFFFF) | $b[($i + 1)]);
        // little endian:
        //   $b = unpack('C*', $string);
        //   $ch = ((($b[($i + 1)] << 8) & 0xFFFF) | $b[$i]);
        $ch = $b[$i];
        if ($ch > self::MAXIMUM_UTF)
        {
          throw new InvalidCharacterException;
        }
        elseif (($ch >= self::SURROGATE_HIGH_START) &&
            ($ch <= self::SURROGATE_HIGH_END))
        {
          ++$i;
          if (!isset($b[$i]))
          {
            throw new InvalidCharacterException;
          }
          $lo = $b[$i];
          if (!(($lo >= self::SURROGATE_LOW_START) &&
                ($lo <= self::SURROGATE_LOW_END)))
          {
            throw new InvalidCharacterException;
          }
          $ch = ((($ch - self::SURROGATE_HIGH_START) << self::HALF_SHIFT)
            + $lo + self::SURROGATE_LOW_BASE);
        }
        $result[] = $ch;
      }
    }
    return $result;
  }

  // }}}
  // {{{ UCS4toUTF16()

  /**
   * Converts a UCS4 string into the UTF-16 byte sequence.
   *
   * @param array $ucs A UCS4 string.
   * @param integer $endian An endian (BIG_ENDIAN or LITTLE_ENDIAN).
   * @return string
   */
  public static function UCS4toUTF16($ucs, $endian = self::BIG_ENDIAN)
  {
    $result = '';
    $format = (($endian === self::LITTLE_ENDIAN) ? 'v' : 'n');
    foreach ($ucs as $cp)
    {
      if (($cp <= self::MAXIMUM_UTF) && !Character::isSurrogate($cp))
      {
        if ($cp <= self::MAXIMUM_UCS2)
        {
          // big endian:
          //   $result .= \chr(($cp & 0xFF00) >> 8) . \chr($cp & 0x00FF);
          // little endian:
          //   $result .= \chr($cp & 0x00FF) . \chr(($cp & 0xFF00) >> 8);
          $result .= \pack($format, $cp);
        }
        else
        {
          $cp -= self::HALF_BASE;
          $hi = (($cp >> self::HALF_SHIFT) + self::SURROGATE_HIGH_START);
          $lo = (($cp & self::HALF_MASK) + self::SURROGATE_LOW_START);
          $result .= \pack($format . '*', $hi, $lo);
        }
      }
      else
      {
        throw new InvalidCharacterException;
      }
    }
    return $result;
  }

  // }}}
  // {{{ UTF32toUCS4()

  /**
   * Converts a sequence of bytes encoded as UTF-32 to the UCS4 string.
   *
   * @param string $string A string.
   * @param integer $endian An endian (BIG_ENDIAN or LITTLE_ENDIAN).
   * @return array
   */
  public static function UTF32toUCS4($string, $endian = self::BIG_ENDIAN)
  {
    // Input length must be dividable by 4.
    if ((\strlen($string) % 4) !== 0)
    {
      throw new InvalidEncodingException;
    }
    $result = array();
    $format = 'N*';
    if ((\substr($string, 0, 4) === \pack('V', self::BYTE_ORDER_MARK)) ||
      ($endian === self::LITTLE_ENDIAN))
    {
      $format = 'V*';
    }
    $b = \unpack($format, $string);
    if (!empty($b))
    {
      $i = 1;
      if ($b[$i] === self::BYTE_ORDER_MARK)
      {
        ++$i;
      }
      for ($j = \count($b); $i <= $j; ++$i)
      {
        // big endian:
        //   $b = unpack('C*', $string);
        //   $ch = ($b[($i + 3)] + ($b[($i + 2)] << 8)
        //       + ($b[($i + 1)] << 16) + ($b[$i] << 24));
        // little endian:
        //   $b = unpack('C*', $string);
        //   $ch = ($b[$i] + ($b[($i + 1)] << 8)
        //       + ($b[($i + 2)] << 16) + ($b[($i + 3)] << 24));
        $ch = $b[$i];
        if ($ch > self::MAXIMUM_UTF)
        {
          throw new InvalidCharacterException;
        }
        $result[] = $ch;
      }
    }
    return $result;
  }

  // }}}
  // {{{ UCS4toUTF32()

  /**
   * Converts a UCS4 string into the UTF-32 byte sequence.
   *
   * @param array $ucs A UCS4 string.
   * @param integer $endian An endian (BIG_ENDIAN or LITTLE_ENDIAN).
   * @return string
   */
  public static function UCS4toUTF32($ucs, $endian = self::BIG_ENDIAN)
  {
    $result = '';
    $format = (($endian === self::LITTLE_ENDIAN) ? 'V' : 'N');
    foreach ($ucs as $cp)
    {
      if (($cp <= self::MAXIMUM_UTF) && !Character::isSurrogate($cp))
      {
        // big endian:
        //   $result .= \chr(($cp & 0xFF000000) >> 24)
        //            . \chr(($cp & 0x00FF0000) >> 16)
        //            . \chr(($cp & 0x0000FF00) >>  8)
        //            . \chr(($cp & 0x000000FF)      );
        // little endian:
        //   $result .= \chr(($cp & 0x000000FF)      )
        //            . \chr(($cp & 0x0000FF00) >>  8)
        //            . \chr(($cp & 0x00FF0000) >> 16)
        //            . \chr(($cp & 0xFF000000) >> 24);
        $result .= \pack($format, $cp);
      }
      else
      {
        throw new InvalidCharacterException;
      }
    }
    return $result;
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
