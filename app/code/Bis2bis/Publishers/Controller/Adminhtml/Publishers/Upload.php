<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 *
 * @package     Bis2bis\Publishers\Controller\Adminhtml\Publishers
 * @author      Beatriz Graciani Sborz
 */

declare(strict_types=1);

namespace Bis2bis\Publishers\Controller\Adminhtml\Publishers;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\UrlInterface;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Controller responsible for handling image file uploads for publishers.
 */
class Upload extends Action
{
    /**
     * @var UploaderFactory
     */
    protected UploaderFactory $uploaderFactory;

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected Filesystem\Directory\WriteInterface $mediaDirectory;

    /**
     * @var StoreManagerInterface
     */
    protected StoreManagerInterface $storeManager;

    /**
     * Upload constructor.
     *
     * @param Context $context
     * @param UploaderFactory $uploaderFactory
     * @param Filesystem $filesystem
     * @param StoreManagerInterface $storeManager
     * @throws FileSystemException
     */
    public function __construct(
        Context $context,
        UploaderFactory $uploaderFactory,
        Filesystem $filesystem,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->uploaderFactory = $uploaderFactory;
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->storeManager = $storeManager;
    }

    /**
     * Execute file upload logic and return JSON response.
     *
     * @return ResponseInterface|Json|ResultInterface
     */
    public function execute()
    {
        /** @var Json $jsonResult */
        $jsonResult = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        $files = $this->getRequest()->getFiles();

        if (!isset($files['logo'])) {
            return $jsonResult->setData([
                'errorcode' => 0,
                'error' => __('No file uploaded.')
            ]);
        }

        try {
            $fileUploader = $this->uploaderFactory->create(['fileId' => $files['logo']]);
            $fileUploader->setAllowedExtensions(['jpg', 'jpeg', 'png']);
            $fileUploader->setAllowRenameFiles(true);
            $fileUploader->setAllowCreateFolders(true);
            $fileUploader->setFilesDispersion(false);
            $fileUploader->validateFile();

            $uploadPath = $this->mediaDirectory->getAbsolutePath('tmp/imageUploader/images/');
            $result = $fileUploader->save($uploadPath);

            if (!$result || !isset($result['file'])) {
                throw new LocalizedException(__('File upload failed.'));
            }

            $mediaUrl = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
            $result['url'] = $mediaUrl
                . 'tmp/imageUploader/images/'
                . ltrim(str_replace('\\', '/', $result['file']), '/');

            return $jsonResult->setData($result);
        } catch (LocalizedException $e) {
            return $jsonResult->setData([
                'errorcode' => 0,
                'error' => $e->getMessage()
            ]);
        } catch (Exception $e) {
            return $jsonResult->setData([
                'errorcode' => 0,
                'error' => __('An error occurred, please try again later.')
            ]);
        }
    }

    /**
     * Check if admin user has permission to upload publisher images.
     *
     * @return bool
     */
    protected function _isAllowed(): bool
    {
        return $this->_authorization->isAllowed('Bis2bis_Publishers::create');
    }
}
