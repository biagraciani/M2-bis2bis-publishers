<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 *
 * @package   Bis2bis\Publishers\Controller\Adminhtml\Publishers
 * @author
 */

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
 * Este controller é responsável por receber os dados do formulário na área administrativa,
 * processar o upload do arquivo de logo, validar os dados e salvar a entidade Publisher
 * utilizando o PublisherRepositoryInterface para persistência dos dados.
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
     * Construtor
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
     * Executa a ação de salvar a entidade Publisher.
     *
     * Este método realiza as seguintes operações:
     * - Recupera os dados enviados via POST.
     * - Processa o upload da imagem de logo, se houver.
     * - Valida o campo "name" para garantir que seja alfanumérico e tenha no máximo 200 caracteres.
     * - Carrega o Publisher existente ou instancia um novo, conforme o parâmetro 'entity_id'.
     * - Atualiza os dados da entidade e a persiste através do PublisherRepository.
     * - Exibe mensagens de sucesso ou erro conforme o resultado da operação.
     *
     * @return Redirect Resultado da ação de redirecionamento.
     */
    public function execute(): Redirect
    {
        // Recupera os dados enviados pelo formulário
        $data = $this->getRequest()->getPostValue();
        // Cria o objeto de redirecionamento
        $resultRedirect = $this->resultRedirectFactory->create();
        // Obtém o ID da entidade Publisher, se presente
        $id = $this->getRequest()->getParam('entity_id');

        try {
            // Processa o upload do arquivo de logo, se houver
            if (isset($data['logo'][0]['tmp_name']) && !empty($data['logo'][0]['tmp_name'])) {
                // Constrói o caminho completo do arquivo temporário
                $tempFilePath = $data['logo'][0]['path'] . '/' . $data['logo'][0]['file'];
                // Atualiza o valor do tmp_name com o caminho completo
                $data['logo'][0]['tmp_name'] = $tempFilePath;
                // Realiza o upload e validação da imagem e atualiza o campo logo com o caminho relativo do arquivo
                $data['logo'] = $this->uploadAndValidateImage($id, $data['logo'][0]);
            } elseif (isset($data['logo'][0]['name'])) {
                $data['logo'] = $data['logo'][0]['name'];
            } else {
                unset($data['logo']);
            }

            // Valida o campo "name": deve ser alfanumérico e conter até 200 caracteres
            if (empty($data['name']) || !preg_match('/^[a-zA-Z0-9\s]{1,200}$/', $data['name'])) {
                throw new LocalizedException(__('The name must be alphanumeric and up to 200 characters.'));
            }

            // Se houver ID, carrega a entidade existente; caso contrário, cria uma nova
            if (!empty($id)) {
                $model = $this->publisherRepository->getById($id);
                $model->addData($data);
            } else {
                $model = $this->publisherFactory->create();
                $model->setData($data);
            }

            $this->publisherRepository->save($model);

            $this->messageManager->addSuccessMessage(__('You saved the publisher.'));
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the publisher.'));
        }

        return $resultRedirect->setPath('*/*/');
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
     * Verifica se o usuário atual tem permissão para salvar publishers.
     *
     * Este método utiliza o sistema de ACL do Magento para verificar se o usuário logado possui
     * a permissão necessária, definida pelo recurso 'Bis2bis_Publishers::edit'.
     *
     * @return bool Retorna true se o usuário tiver permissão; caso contrário, false.
     */
    protected function _isAllowed(): bool
    {
        return $this->_authorization->isAllowed('Bis2bis_Publishers::edit');
    }
}
