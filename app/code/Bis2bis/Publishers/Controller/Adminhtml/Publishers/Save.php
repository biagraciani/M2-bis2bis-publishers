<?php
declare(strict_types=1);

namespace Bis2bis\Publishers\Controller\Adminhtml\Publishers;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\Result\Redirect;
use Bis2bis\Publishers\Api\PublisherRepositoryInterface;
use Bis2bis\Publishers\Model\PublisherFactory;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Exception\LocalizedException;

/**
 * Save Publisher Controller
 *
 * Recebe os dados do formulário, processa o upload do logo, valida os dados
 * (nome e CNPJ) e salva a entidade Publisher utilizando o PublisherRepositoryInterface.
 *
 * @package Bis2bis\Publishers\Controller\Adminhtml\Publishers
 */
class Save extends Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected \Magento\Framework\View\Result\PageFactory $resultPageFactory;

    /**
     * @var PublisherRepositoryInterface
     */
    protected PublisherRepositoryInterface $publisherRepository;

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
     * @param PublisherRepositoryInterface $publisherRepository
     * @param PublisherFactory $publisherFactory
     * @param UploaderFactory $uploaderFactory
     * @param Filesystem $filesystem
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        PublisherRepositoryInterface $publisherRepository,
        PublisherFactory $publisherFactory,
        UploaderFactory $uploaderFactory,
        Filesystem $filesystem
    ) {
        parent::__construct($context);
        $this->resultPageFactory   = $resultPageFactory;
        $this->publisherRepository = $publisherRepository;
        $this->publisherFactory    = $publisherFactory;
        $this->uploaderFactory     = $uploaderFactory;
        $this->mediaDirectory      = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
    }

    /**
     * Execute the save action.
     *
     * @return Redirect
     * @throws LocalizedException
     */
    public function execute(): Redirect
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        $id = $this->getRequest()->getParam('entity_id');
        
        try {
            // Processa o upload do logo, se houver
            if (isset($data['logo'][0]['tmp_name']) && !empty($data['logo'][0]['tmp_name'])) {
                $data['logo'][0]['tmp_name'] = $data['logo'][0]['path'] . '/' . $data['logo'][0]['file'];;
                $data['logo'] = $this->uploadAndValidateImage($id, $data['logo'][0]);
            } elseif (isset($data['logo'][0]['name'])) {
                $data['logo'] = $data['logo'][0]['name'];
            } else {
                unset($data['logo']);
            }

            // Validação do nome
            if (!empty($data['name'])) {
                $this->validateName($data['name']);
            }else{
                throw new LocalizedException(__('The publisher name is required .'));
            }

            // Validação do cnpj
            if (!empty($data['cnpj'])) {
                $this->validateCnpj($data['cnpj']);
            }

            // Carrega a entidade existente ou cria um novo publiser
            if (!empty($id)) {
                $publisher = $this->publisherRepository->getById($id);
                $publisher->addData($data);
            } else {
                $publisher = $this->publisherFactory->create();
                $publisher->setData($data);
            }

            $this->publisherRepository->save($publisher);
            $this->messageManager->addSuccessMessage(__('You saved the publisher.'));
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the publisher.'));
            return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
        }
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Validates the publisher name.
     *
     * The name must be alphanumeric and contain up to 200 characters.
     *
     * @param string $name
     * @return void
     * @throws LocalizedException
     */
    protected function validateName(string $name): void
    {
        if (empty($name) || !preg_match('/^[a-zA-Z0-9\s]{1,200}$/', $name)) {
            throw new LocalizedException(__('The name must be alphanumeric and up to 200 characters.'));
        }
    }
    /**
     * Validates the CNPJ.
     *
     * Removes non-numeric characters and validates length, repeated digits,
     * and verifying digits using the standard algorithm.
     *
     * @param string $cnpj
     * @return void
     * @throws LocalizedException
     */
    protected function validateCnpj(string $cnpj): void
    {
        //valida se contem letras
        if (preg_match('/[a-zA-Z]/', $cnpj)) {
            throw new LocalizedException(__('Invalid CNPJ '.$cnpj));
        }

        // Remove caracteres não numéricos
        $cnpj = preg_replace('/\D/', '', $cnpj);

        // O CNPJ deve ter 14 dígitos
        if (strlen($cnpj) !== 14) {
            throw new LocalizedException(__('Invalid CNPJ '.$cnpj));
        }

        // Verifica se todos os dígitos são iguais
        if (preg_match('/(\d)\1{13}/', $cnpj)) {
            throw new LocalizedException(__('Invalid CNPJ '.$cnpj));
        }

        $digits = str_split($cnpj);

        // Primeiro dígito verificador
        $sum = 0;
        $multipliers = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        for ($i = 0; $i < 12; $i++) {
            $sum += $digits[$i] * $multipliers[$i];
        }
        $remainder = $sum % 11;
        $firstVerifyingDigit = ($remainder < 2 ? 0 : 11 - $remainder);

        if ((int)$digits[12] !== $firstVerifyingDigit) {
            throw new LocalizedException(__('Invalid CNPJ '.$cnpj));
        }

        // Segundo dígito verificador
        $sum = 0;
        $multipliers = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        for ($i = 0; $i < 13; $i++) {
            $sum += $digits[$i] * $multipliers[$i];
        }
        $remainder = $sum % 11;
        $secondVerifyingDigit = ($remainder < 2 ? 0 : 11 - $remainder);

        if ((int)$digits[13] !== $secondVerifyingDigit) {
            throw new LocalizedException(__('Invalid CNPJ '.$cnpj));
        }
    }

    /**
     * Realiza o upload e validação da imagem do logo.
     *
     * Este método:
     * - Cria uma instância do uploader com os dados do arquivo.
     * - Define as extensões permitidas (jpg, jpeg, png).
     * - Permite a renomeação do arquivo e criação de pastas, se necessário.
     * - Valida o arquivo antes do upload.
     * - Salva o arquivo no diretório configurado e retorna o caminho relativo do arquivo.
     *
     * @param int|string $idPublisher ID do Publisher utilizado para definir o diretório de upload.
     * @param array $fileData Dados do arquivo enviado.
     * @return string Caminho relativo do arquivo de logo.
     * @throws LocalizedException Caso ocorra algum erro no upload ou na validação do arquivo.
     */
    public function uploadAndValidateImage($idPublisher, array $fileData): string
    {
        try {
            // Cria a instância do uploader com os dados do arquivo
            $uploader = $this->uploaderFactory->create(['fileId' => $fileData]);
            // Define as extensões permitidas para o upload
            $uploader->setAllowedExtensions(['jpg', 'jpeg', 'png']);
            // Permite que o arquivo seja renomeado em caso de conflito
            $uploader->setAllowRenameFiles(true);
            // Permite a criação de novas pastas, se necessário
            $uploader->setAllowCreateFolders(true);
            // Valida o arquivo antes de proceder com o upload
            $uploader->validateFile();

            // Define o caminho absoluto para o diretório de upload, utilizando o ID do Publisher
            $uploadDir = $this->mediaDirectory->getAbsolutePath(
                'bis2bis/publishers/logo/publisher/' . $idPublisher
            );

            // Realiza o upload do arquivo para o diretório especificado
            $result = $uploader->save($uploadDir);
            // Verifica se o upload foi realizado com sucesso
            if (!$result || !isset($result['file'])) {
                throw new LocalizedException(__('File upload failed.'));
            }

            // Constrói o caminho relativo do arquivo a partir do diretório de mídia
            $relativePath = $this->mediaDirectory->getRelativePath('bis2bis/publishers/logo/publisher')
                . '/' . $idPublisher . '/' . $result['file'];

            return $relativePath;
        } catch (LocalizedException $e) {
            // Lança novamente as exceções já localizadas
            throw $e;
        } catch (\Exception $e) {
            // Envolve e lança uma exceção genérica em caso de falha
            throw new LocalizedException(__('Image upload failed: %1', $e->getMessage()));
        }
    }

    /**
     * Check if the current user is allowed to save publishers.
     *
     * @return bool
     */
    protected function _isAllowed(): bool
    {
        return $this->_authorization->isAllowed('Bis2bis_Publishers::edit');
    }
}
