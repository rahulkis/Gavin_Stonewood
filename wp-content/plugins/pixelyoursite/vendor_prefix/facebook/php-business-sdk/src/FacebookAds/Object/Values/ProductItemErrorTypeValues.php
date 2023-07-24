<?php

/**
 * Copyright (c) 2015-present, Facebook, Inc. All rights reserved.
 *
 * You are hereby granted a non-exclusive, worldwide, royalty-free license to
 * use, copy, modify, and distribute this software in source code or binary
 * form for use in connection with the web services and APIs provided by
 * Facebook.
 *
 * As with any software that integrates with the Facebook platform, your use
 * of this software is subject to the Facebook Developer Principles and
 * Policies [http://developers.facebook.com/policy/]. This copyright notice
 * shall be included in all copies or substantial portions of the software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
 * THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
 * DEALINGS IN THE SOFTWARE.
 *
 */
namespace PYS_PRO_GLOBAL\FacebookAds\Object\Values;

use PYS_PRO_GLOBAL\FacebookAds\Enum\AbstractEnum;
/**
 * This class is auto-generated.
 *
 * For any issues or feature requests related to this class, please let us know
 * on github and we'll fix in our codegen framework. We'll not be able to accept
 * pull request for this class.
 *
 * @method static ProductItemErrorTypeValues getInstance()
 */
class ProductItemErrorTypeValues extends \PYS_PRO_GLOBAL\FacebookAds\Enum\AbstractEnum
{
    const AR_DELETED_DUE_TO_UPDATE = 'AR_DELETED_DUE_TO_UPDATE';
    const AR_POLICY_VIOLATED = 'AR_POLICY_VIOLATED';
    const AVAILABLE = 'AVAILABLE';
    const BAD_QUALITY_IMAGE = 'BAD_QUALITY_IMAGE';
    const CANNOT_EDIT_SUBSCRIPTION_PRODUCTS = 'CANNOT_EDIT_SUBSCRIPTION_PRODUCTS';
    const CRAWLED_AVAILABILITY_MISMATCH = 'CRAWLED_AVAILABILITY_MISMATCH';
    const DIGITAL_GOODS_NOT_AVAILABLE_FOR_CHECKOUT = 'DIGITAL_GOODS_NOT_AVAILABLE_FOR_CHECKOUT';
    const DUPLICATE_IMAGES = 'DUPLICATE_IMAGES';
    const DUPLICATE_TITLE_AND_DESCRIPTION = 'DUPLICATE_TITLE_AND_DESCRIPTION';
    const GENERIC_INVALID_FIELD = 'GENERIC_INVALID_FIELD';
    const HIDDEN_UNTIL_PRODUCT_LAUNCH = 'HIDDEN_UNTIL_PRODUCT_LAUNCH';
    const IMAGE_RESOLUTION_LOW = 'IMAGE_RESOLUTION_LOW';
    const INACTIVE_SHOPIFY_PRODUCT = 'INACTIVE_SHOPIFY_PRODUCT';
    const INVALID_COMMERCE_TAX_CATEGORY = 'INVALID_COMMERCE_TAX_CATEGORY';
    const INVALID_IMAGES = 'INVALID_IMAGES';
    const INVALID_MONETIZER_RETURN_POLICY = 'INVALID_MONETIZER_RETURN_POLICY';
    const INVALID_PRE_ORDER_PARAMS = 'INVALID_PRE_ORDER_PARAMS';
    const INVALID_SHIPPING_PROFILE_PARAMS = 'INVALID_SHIPPING_PROFILE_PARAMS';
    const INVALID_SUBSCRIPTION_DISABLE_PARAMS = 'INVALID_SUBSCRIPTION_DISABLE_PARAMS';
    const INVALID_SUBSCRIPTION_ENABLE_PARAMS = 'INVALID_SUBSCRIPTION_ENABLE_PARAMS';
    const INVALID_SUBSCRIPTION_PARAMS = 'INVALID_SUBSCRIPTION_PARAMS';
    const INVENTORY_ZERO_AVAILABILITY_IN_STOCK = 'INVENTORY_ZERO_AVAILABILITY_IN_STOCK';
    const IN_ANOTHER_PRODUCT_LAUNCH = 'IN_ANOTHER_PRODUCT_LAUNCH';
    const ITEM_GROUP_NOT_SPECIFIED = 'ITEM_GROUP_NOT_SPECIFIED';
    const ITEM_NOT_SHIPPABLE_FOR_SCA_SHOP = 'ITEM_NOT_SHIPPABLE_FOR_SCA_SHOP';
    const ITEM_OVERRIDE_NOT_VISIBLE = 'ITEM_OVERRIDE_NOT_VISIBLE';
    const ITEM_STALE_OUT_OF_STOCK = 'ITEM_STALE_OUT_OF_STOCK';
    const MINI_SHOPS_DISABLED_BY_USER = 'MINI_SHOPS_DISABLED_BY_USER';
    const MISSING_CHECKOUT = 'MISSING_CHECKOUT';
    const MISSING_CHECKOUT_CURRENCY = 'MISSING_CHECKOUT_CURRENCY';
    const MISSING_COLOR = 'MISSING_COLOR';
    const MISSING_COUNTRY_OVERRIDE_IN_SHIPPING_PROFILE = 'MISSING_COUNTRY_OVERRIDE_IN_SHIPPING_PROFILE';
    const MISSING_INDIA_COMPLIANCE_FIELDS = 'MISSING_INDIA_COMPLIANCE_FIELDS';
    const MISSING_SHIPPING_PROFILE = 'MISSING_SHIPPING_PROFILE';
    const MISSING_SIZE = 'MISSING_SIZE';
    const MISSING_TAX_CATEGORY = 'MISSING_TAX_CATEGORY';
    const NOT_ENOUGH_IMAGES = 'NOT_ENOUGH_IMAGES';
    const PART_OF_PRODUCT_LAUNCH = 'PART_OF_PRODUCT_LAUNCH';
    const PRODUCT_EXPIRED = 'PRODUCT_EXPIRED';
    const PRODUCT_ITEM_NOT_VISIBLE = 'PRODUCT_ITEM_NOT_VISIBLE';
    const PRODUCT_NOT_APPROVED = 'PRODUCT_NOT_APPROVED';
    const PRODUCT_NOT_DOMINANT_CURRENCY = 'PRODUCT_NOT_DOMINANT_CURRENCY';
    const PRODUCT_OUT_OF_STOCK = 'PRODUCT_OUT_OF_STOCK';
    const PRODUCT_URL_EQUALS_DOMAIN = 'PRODUCT_URL_EQUALS_DOMAIN';
    const PROPERTY_PRICE_CURRENCY_NOT_SUPPORTED = 'PROPERTY_PRICE_CURRENCY_NOT_SUPPORTED';
    const PROPERTY_PRICE_TOO_HIGH = 'PROPERTY_PRICE_TOO_HIGH';
    const PROPERTY_PRICE_TOO_LOW = 'PROPERTY_PRICE_TOO_LOW';
    const PROPERTY_VALUE_CONTAINS_HTML_TAGS = 'PROPERTY_VALUE_CONTAINS_HTML_TAGS';
    const PROPERTY_VALUE_DESCRIPTION_CONTAINS_OFF_PLATFORM_LINK = 'PROPERTY_VALUE_DESCRIPTION_CONTAINS_OFF_PLATFORM_LINK';
    const PROPERTY_VALUE_FORMAT = 'PROPERTY_VALUE_FORMAT';
    const PROPERTY_VALUE_MISSING = 'PROPERTY_VALUE_MISSING';
    const PROPERTY_VALUE_MISSING_WARNING = 'PROPERTY_VALUE_MISSING_WARNING';
    const PROPERTY_VALUE_NON_POSITIVE = 'PROPERTY_VALUE_NON_POSITIVE';
    const PROPERTY_VALUE_STRING_EXCEEDS_LENGTH = 'PROPERTY_VALUE_STRING_EXCEEDS_LENGTH';
    const PROPERTY_VALUE_STRING_TOO_SHORT = 'PROPERTY_VALUE_STRING_TOO_SHORT';
    const PROPERTY_VALUE_UPPERCASE_WARNING = 'PROPERTY_VALUE_UPPERCASE_WARNING';
    const QUALITY_DUPLICATED_DESCRIPTION = 'QUALITY_DUPLICATED_DESCRIPTION';
    const QUALITY_ITEM_LINK_BROKEN = 'QUALITY_ITEM_LINK_BROKEN';
    const QUALITY_ITEM_LINK_REDIRECTING = 'QUALITY_ITEM_LINK_REDIRECTING';
    const RETAILER_ID_NOT_PROVIDED = 'RETAILER_ID_NOT_PROVIDED';
    const SHOPIFY_ITEM_MISSING_SHIPPING_PROFILE = 'SHOPIFY_ITEM_MISSING_SHIPPING_PROFILE';
    const SUBSCRIPTION_INFO_NOT_ENABLED_FOR_FEED = 'SUBSCRIPTION_INFO_NOT_ENABLED_FOR_FEED';
    const TAX_CATEGORY_NOT_SUPPORTED_IN_UK = 'TAX_CATEGORY_NOT_SUPPORTED_IN_UK';
    const UNSUPPORTED_PRODUCT_CATEGORY = 'UNSUPPORTED_PRODUCT_CATEGORY';
    const VARIANT_ATTRIBUTE_ISSUE = 'VARIANT_ATTRIBUTE_ISSUE';
}
