<?php
namespace Aheadworks\CustGroupCatPermissions\Block\Adminhtml\System\Config;

use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Config\Block\System\Config\Form\Field as SystemConfigFormField;
use Magento\Backend\Block\Template\Context;
use Aheadworks\CustGroupCatPermissions\Model\NativePermissions\Resolver as NativePermissionsResolver;

/**
 * Class EnableModule
 *
 * @package Aheadworks\CustGroupCatPermissions\Block\Adminhtml\System\Config
 */
class EnableModule extends SystemConfigFormField
{
    /**
     * @var NativePermissionsResolver
     */
    private $nativePermissionsResolver;

    /**
     * @param Context $context
     * @param NativePermissionsResolver $nativePermissionsResolver
     * @param array $data
     */
    public function __construct(
        Context $context,
        NativePermissionsResolver $nativePermissionsResolver,
        array $data = []
    ) {
        $this->nativePermissionsResolver = $nativePermissionsResolver;
        parent::__construct($context, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $this->addCustomAfterElementHtml($element);
        return parent::_getElementHtml($element);
    }

    /**
     * Adding custom html after rendered element
     *
     * @param AbstractElement $element
     */
    private function addCustomAfterElementHtml(AbstractElement $element)
    {
        $currentAfterElementHtml = $element->getAfterElementHtml();
        if ($this->isNeedToAddSelectWithAlertWidget()) {
            $customAfterElementHtml = $this->getSelectWithAlertWidgetHtml($element);
            $element->setAfterElementHtml($currentAfterElementHtml . $customAfterElementHtml);
        }
    }

    /**
     * Check if need to add select with alert widget
     *
     * @return bool
     */
    private function isNeedToAddSelectWithAlertWidget()
    {
        return $this->nativePermissionsResolver->isEnterpriseCatalogPermissionsEnabled();
    }

    /**
     * Retrieve custom html for adding select with alert widget
     *
     * @param AbstractElement $element
     * @return string
     */
    private function getSelectWithAlertWidgetHtml(AbstractElement $element)
    {
        $elementId = $element->getId();
        $preparedAlertMessage = $this->getPreparedAlertMessage();

        $customAfterElementHtml = <<<HTML
<script type="text/x-magento-init">
{
    "*": {
        "awCpSelectWithAlert": {
            "selectId": "{$elementId}",
            "alertMessage": {$preparedAlertMessage}
        }
    }
}
</script>
HTML;

        return $customAfterElementHtml;
    }

    /**
     * Retrieve prepared message for the alert modal
     *
     * @return string
     */
    private function getPreparedAlertMessage()
    {
        return json_encode(
            __(
                'Please disable <a href=""%1"">native category permissions</a> to continue.',
                $this->nativePermissionsResolver->getEnterpriseCatalogPermissionsSettingsPageUrl()
            )
        );
    }
}
