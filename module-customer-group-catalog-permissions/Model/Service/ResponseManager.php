<?php
declare(strict_types=1);

namespace Aheadworks\CustGroupCatPermissions\Model\Service;

use Magento\Framework\App\Response\RedirectInterface;

/**
 * Class ResponseManager
 */
class ResponseManager
{
    /**
     * ResponseManager constructor.
     *
     * @param RedirectInterface $redirect
     */
    public function __construct(private RedirectInterface $redirect)
    {
    }

    /**
     * Get current redirect referer url key
     *
     * @return string
     */
    public function getCurrentRedirectRefererUrlKey(): string
    {
        $refererUrl = $this->redirect->getRefererUrl();
        return $this->retrieveUrlKey($refererUrl);
    }

    /**
     * Retrieve url key
     *
     * @param string $url
     * @return string
     */
    private function retrieveUrlKey(string $url): string
    {
        $urlKey = '';
        $tokensArray = explode('/', (string) $url);
        if (count($tokensArray)) {
            $lastUrlKey = array_pop($tokensArray);
            $extensionPosition = strrpos($lastUrlKey, '.html');
            if ($extensionPosition !== false) {
                $normalizedUrlKey = substr($lastUrlKey, 0, $extensionPosition);
            } else {
                $normalizedUrlKey = $lastUrlKey;
            }
            $urlKey = $normalizedUrlKey;
        }

        return $urlKey;
    }
}
