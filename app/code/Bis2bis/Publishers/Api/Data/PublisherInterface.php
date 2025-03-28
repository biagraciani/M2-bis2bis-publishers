<?php
namespace Bis2bis\Publishers\Api\Data;

interface PublisherInterface
{
    public const ENTITY_ID = 'entity_id';
    public const NAME      = 'name';
    public const STATUS    = 'status';
    public const ADDRESS   = 'address';
    public const LOGO      = 'logo';
    public const CNPJ      = 'cnpj';

    /**
     * Retorna o ID da editora.
     *
     * @return int|null
     */
    public function getId();

    /**
     * Define o ID da editora.
     *
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * Retorna o nome da editora.
     *
     * @return string
     */
    public function getName();

    /**
     * Define o nome da editora.
     *
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * Retorna o status da editora.
     *
     * @return bool
     */
    public function getStatus();

    /**
     * Define o status da editora.
     *
     * @param bool $status
     * @return $this
     */
    public function setStatus($status);

    /**
     * Retorna o endereço da editora.
     *
     * @return string
     */
    public function getAddress();

    /**
     * Define o endereço da editora.
     *
     * @param string $address
     * @return $this
     */
    public function setAddress($address);

    /**
     * Retorna o logo da editora.
     *
     * @return string|null
     */
    public function getLogo();

    /**
     * Define o logo da editora.
     *
     * @param string $logo
     * @return $this
     */
    public function setLogo($logo);

    /**
     * Retorna o CNPJ da editora.
     *
     * @return string|null
     */
    public function getCnpj();

    /**
     * Define o CNPJ da editora.
     *
     * @param string $cnpj
     * @return $this
     */
    public function setCnpj($cnpj);
}
