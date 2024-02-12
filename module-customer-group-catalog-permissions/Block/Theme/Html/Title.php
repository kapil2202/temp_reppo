<?php
namespace Aheadworks\CustGroupCatPermissions\Block\Theme\Html;

use Magento\Theme\Block\Html\Title as MagentoTitle;

/**
 * Class Title
 * @package Aheadworks\CustGroupCatPermissions\Block\Theme\Html
 */
class Title extends MagentoTitle
{
    /**
     * Config path to 'Translate Title' header settings
     */
    protected const XML_PATH_HEADER_TRANSLATE_TITLE = 'design/header/translate_title';
}
