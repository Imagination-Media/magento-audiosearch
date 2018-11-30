<?php

/**
 * Audio Search
 *
 * Use Google's cloud Speech to Text api to listen audio and make a search based on what the customer said.
 *
 * @package ImaginationMedia\AudioSearch
 * @author Igor Ludgero Miura <igor@imaginationmedia.com>
 * @copyright Copyright (c) 2018 Imagination Media (https://www.imaginationmedia.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

namespace ImaginationMedia\AudioSearch\Model;

use Google\Cloud\Speech\SpeechClient;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\UrlInterface;

class Helper
{
    const CONFIG_MODULE_ENABLED = "audiosearch/general/enable";
    const CONFIG_PROJECT_ID = "audiosearch/general/project_id";
    const CONFIG_LANGUAGE = "general/locale/code";

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var UrlInterface
     */
    private $url;

    /**
     * Helper constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param UrlInterface $url
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        UrlInterface $url
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->url = $url;
    }

    /**
     * Get config from admin
     * @param string $path
     * @return string
     */
    public function getConfigValue(string $path) : string
    {
        return (string)$this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Check if module is enabled on admin
     * @return bool
     */
    public function isEnabled() : bool
    {
        return ((int)$this->getConfigValue(self::CONFIG_MODULE_ENABLED) === 1);
    }

    /**
     * Get search url
     * @param string $fileName
     * @return string
     */
    public function getSearchUrl(string $fileName) : string
    {
        $projectId = $this->getConfigValue(self::CONFIG_PROJECT_ID);

        $speech = new SpeechClient([
            'projectId' => $projectId,
            'languageCode' => $this->getConfigValue(self::CONFIG_LANGUAGE),
        ]);

        $options = [
            'encoding' => 'LINEAR16'
        ];

        $results = $speech->recognize(fopen($fileName, 'r'), $options);

        $finalValue = "";
        $confidence = 0;
        foreach ($results as $result) {
            if ($result->alternatives()[0]['confidence'] > $confidence) {
                $confidence = $result->alternatives()[0]['confidence'];
                $finalValue = $result->alternatives()[0]['transcript'];
            }
        }

        /**
         * Remove repeated values
         */
        $values = array_unique(explode(" ", $finalValue));
        $finalValue = "";
        foreach ($values as $value) {
            if ($finalValue !== "") {
                $finalValue .= " " . $value;
            } else {
                $finalValue = $value;
            }
        }

        if ($finalValue !== '') {
            return $this->url->getUrl('catalogsearch/result', ['q' => $finalValue]);
        }
        return '';
    }
}
