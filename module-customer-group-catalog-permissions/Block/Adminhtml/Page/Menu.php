<?php
declare(strict_types=1);

namespace Aheadworks\CustGroupCatPermissions\Block\Adminhtml\Page;

use Magento\Backend\Block\Template;

/**
 * Page Menu
 *
 * @method Menu setTitle(string $title)
 * @method string getTitle()
 */
class Menu extends Template
{
    /**
     * Block template filename
     *
     * @var string
     */
    protected $_template = 'Aheadworks_CustGroupCatPermissions::page/menu.phtml';
}
