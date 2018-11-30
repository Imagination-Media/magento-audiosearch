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

namespace ImaginationMedia\AudioSearch\Block;

use Magento\Framework\View\Element\Template;

class Search extends Template
{
    public function getFormUrl() : string
    {
        return $this->_urlBuilder->getUrl("audiosearch/search");
    }
}
