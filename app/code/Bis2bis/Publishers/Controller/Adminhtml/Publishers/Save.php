<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 *
 * @package   Bis2bis\Publishers\Controller\Adminhtml\Publishers
 * @author    Beatriz Graciani Sborz
 *
 */

namespace Bis2bis\Publishers\Controller\Adminhtml\Publishers;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\Result\Redirect;
use Bis2bis\Publishers\Model\PublisherFactory;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Exception\LocalizedException;

class Save extends Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected \Magento\Framework\View\Result\PageFactory $resultPageFactory;

    /**
     * @var PublisherFactory
     */
    protected PublisherFactory $publisherFactory;

    /**
     * @var UploaderFactory
     */
    protected UploaderFactory $uploaderFactory;

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected \Magento\Framework\Filesystem\Directory\WriteInterface $mediaDirectory;

    /**
     * Constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param PublisherFactory $publisherFactory
     * @param UploaderFactory $uploaderFactory
     * @param Filesystem $filesystem
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        PublisherFactory $publisherFactory,
        UploaderFactory $uploaderFactory,
        Filesystem $filesystem
    ) {
        parent::__construct($context);
        $this->publisherFactory = $publisherFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->uploaderFactory = $uploaderFactory;
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
    }

    /**
     * Save Publisher entity.
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute(): Redirect
    {
        $data = $this->getRequest()->getPostValue();
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('entity_id');

        try {
            if (isset($data['logo'][0]['tmp_name']) && !empty($data['logo'][0]['tmp_name'])) {
                $tempFilePath = $data['logo'][0]['path'] . '/' . $data['logo'][0]['file'];
                $data['logo'][0]['tmp_name'] = $tempFilePath;
                $data['logo'] = $this->uploadAndValidateImage($id, $data['logo'][0]);
            } elseif (isset($data['logo'][0]['name'])) {
                $data['logo'] = $data['logo'][0]['name'];
            } else {
                unset($data['logo']);
            }

            if (empty($data['name']) || !preg_match('/^[a-zA-Z0-9\s]{1,200}$/', $data['name'])) {
                throw new LocalizedException(__('The name must be alphanumeric and up to 200 characters.'));
            }

            $model = $this->publisherFactory->create();
            if (!empty($id)) {
                $model->load($id);
                if (!$model->getId()) {
                    $this->messageManager->addErrorMessage(__('This publisher no longer exists.'));
                    return $resultRedirect->setPath('*/*/index');
                }
                $model->addData($data);
            } else {
                $model->setData($data);
            }

            $model->save();
            $this->messageManager->addSuccessMessage(__('You saved the publisher.'));
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the publisher.'));
        }

        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Handles logo image upload and validation.
     *
     * @param int|string $idPublisher
     * @param array $fileData
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function uploadAndValidateImage($idPublisher, $fileData): string
    {
        try {
            $uploader = $this->uploaderFactory->create(['fileId' => $fileData]);
            $uploader->setAllowedExtensions(['jpg', 'jpeg', 'png']);
            $uploader->setAllowRenameFiles(true);
            $uploader->setAllowCreateFolders(true);
            $uploader->validateFile();

            $uploadDir = $this->mediaDirectory->getAbsolutePath(
                'bis2bis/publishers/logo/publisher/' . $idPublisher
            );

            $result = $uploader->save($uploadDir);
            if (!$result || !isset($result['file'])) {
                throw new LocalizedException(__('File upload failed.'));
            }

            $relativePath = $this->mediaDirectory->getRelativePath('bis2bis/publishers/logo/publisher')
                . '/' . $idPublisher . '/' . $result['file'];

            return $relativePath;
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new LocalizedException(__('Image upload failed: %1', $e->getMessage()));
        }
    }

    /**
     * Check if current user has permission to save publishers.
     *
     * @return bool
     */
    protected function _isAllowed(): bool
    {
        return $this->_authorization->isAllowed('Bis2bis_Publishers::edit');
    }
}
