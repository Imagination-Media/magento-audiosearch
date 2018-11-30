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
use Magento\MediaStorage\Model\File\Uploader;
use Magento\MediaStorage\Model\File\UploaderFactory;

class Index extends Action
{
    const UPLOAD_DIRECTORY = "audiosearch";

    /**
     * @var UploaderFactory
     */
    private $uploaderFactory;

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
     * @param UploaderFactory $uploaderFactory
     * @param Filesystem $filesystem
     * @param JsonFactory $jsonFactory
     * @param Helper $helper
     */
    public function __construct(
        Context $context,
        UploaderFactory $uploaderFactory,
        Filesystem $filesystem,
        JsonFactory $jsonFactory,
        Helper $helper
    ) {
        parent::__construct($context);
        $this->uploaderFactory = $uploaderFactory;
        $this->fileSystem = $filesystem;
        $this->jsonFactory = $jsonFactory;
        $this->helper = $helper;
    }


    public function execute()
    {
        $files = $_FILES;
        if (key_exists("audio_file", $files) &&  strpos($files["audio_file"]["type"], "audio/") !== false) {
            try {
                $mediaDir = $this->fileSystem->getDirectoryWrite(DirectoryList::MEDIA);
                $target = $mediaDir->getAbsolutePath(self::UPLOAD_DIRECTORY);

                /** @var $uploader Uploader */
                $uploader = $this->uploaderFactory->create(['fileId' => 'audio_file']);

                /** rename file name if already exists */
                $uploader->setAllowRenameFiles(true);

                $result = $uploader->save($target);
                if ($result['file']) {
                    $fullPath = $result['path'] . "/" . $result['file'];
                    $url = $this->helper->getSearchUrl($fullPath);
                    return $this->jsonFactory->create()->setData($url);
                }
            } catch (\Exception $e) {
                /**
                 * Error
                 */
            }
        }
        return $this->jsonFactory->create()->setData('');
    }
}
