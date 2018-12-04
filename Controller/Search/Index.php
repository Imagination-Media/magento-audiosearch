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

namespace ImaginationMedia\AudioSearch\Controller\Search;

use ImaginationMedia\AudioSearch\Model\Helper;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Filesystem;

class Index extends Action
{
    const UPLOAD_DIRECTORY = "audiosearch";

    /**
     * @var Filesystem
     */
    private $fileSystem;

    /**
     * @var JsonFactory
     */
    private $jsonFactory;

    /**
     * @var Helper
     */
    private $helper;

    /**
     * Index constructor.
     * @param Context $context
     * @param Filesystem $filesystem
     * @param JsonFactory $jsonFactory
     * @param Helper $helper
     */
    public function __construct(
        Context $context,
        Filesystem $filesystem,
        JsonFactory $jsonFactory,
        Helper $helper
    ) {
        parent::__construct($context);
        $this->fileSystem = $filesystem;
        $this->jsonFactory = $jsonFactory;
        $this->helper = $helper;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $files = $_FILES;
        $data = $this->getRequest()->getParams();
        $url = '';
        if (isset($files['data']) && strpos($files['data']['type'], 'audio/') !== false) {
            try {
                $mediaDir = $this->fileSystem->getDirectoryWrite(DirectoryList::MEDIA);
                $target = $mediaDir->getAbsolutePath(self::UPLOAD_DIRECTORY);
                $fileName = $target . "/" . $data['fname'];
                $result = move_uploaded_file(
                    $_FILES['data']['tmp_name'],
                    $fileName
                );
                if ($result) {
                    $url = $this->helper->getSearchUrl($fileName);
                }
            } catch (\Exception $e) {
                /**
                 * Error
                 */
            }
        }
        return $this->jsonFactory->create()->setData($url);
    }
}
