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

\Magento\Framework\Component\ComponentRegistrar::register(
    \Magento\Framework\Component\ComponentRegistrar::MODULE,
    'ImaginationMedia_AudioSearch',
    __DIR__
);
