<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: google/ads/googleads/v12/common/targeting_setting.proto

namespace Google\Ads\GoogleAds\V12\Common\TargetRestrictionOperation;

use UnexpectedValueException;

/**
 * The operator.
 *
 * Protobuf type <code>google.ads.googleads.v12.common.TargetRestrictionOperation.Operator</code>
 */
class Operator
{
    /**
     * Unspecified.
     *
     * Generated from protobuf enum <code>UNSPECIFIED = 0;</code>
     */
    const UNSPECIFIED = 0;
    /**
     * Used for return value only. Represents value unknown in this version.
     *
     * Generated from protobuf enum <code>UNKNOWN = 1;</code>
     */
    const UNKNOWN = 1;
    /**
     * Add the restriction to the existing restrictions.
     *
     * Generated from protobuf enum <code>ADD = 2;</code>
     */
    const ADD = 2;
    /**
     * Remove the restriction from the existing restrictions.
     *
     * Generated from protobuf enum <code>REMOVE = 3;</code>
     */
    const REMOVE = 3;

    private static $valueToName = [
        self::UNSPECIFIED => 'UNSPECIFIED',
        self::UNKNOWN => 'UNKNOWN',
        self::ADD => 'ADD',
        self::REMOVE => 'REMOVE',
    ];

    public static function name($value)
    {
        if (!isset(self::$valueToName[$value])) {
            throw new UnexpectedValueException(sprintf(
                    'Enum %s has no name defined for value %s', __CLASS__, $value));
        }
        return self::$valueToName[$value];
    }


    public static function value($name)
    {
        $const = __CLASS__ . '::' . strtoupper($name);
        if (!defined($const)) {
            throw new UnexpectedValueException(sprintf(
                    'Enum %s has no value defined for name %s', __CLASS__, $name));
        }
        return constant($const);
    }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Operator::class, \Google\Ads\GoogleAds\V12\Common\TargetRestrictionOperation_Operator::class);

