<?php
declare(strict_types=1);

namespace Aheadworks\CustGroupCatPermissions\Model\Service;

use DomDocument;
use DOMXPath;

/**
 * Class DomManager
 */
class DomManager
{
    /**
     * Create dom document from HTML
     *
     * @param string $html
     * @return DOMDocument
     */
    public function createDomFromHtml(string $html): DOMDocument
    {
        libxml_use_internal_errors(true);
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->loadHTML($html);
        libxml_use_internal_errors(false);

        return $dom;
    }

    /**
     * Prepare XPath
     *
     * @param DOMDocument $document
     * @return DOMXPath
     */
    public function prepareXpath(DOMDocument $document): DOMXPath
    {
        return new DOMXPath($document);
    }

    /**
     * Convert dom to HTML
     *
     * @param DOMDocument $document
     * @return string
     */
    public function convertDomToHtml(DomDocument $document): string
    {
        if ($document->doctype) {
            $document->removeChild($document->doctype);
        }
        $resultHtml = $document->saveHTML();
        $resultHtml = str_replace(
            ['<html>', '</html>', '<head>', '</head>', '<body>', '</body>'],
            '',
            $resultHtml
        );

        return $resultHtml;
    }
}
