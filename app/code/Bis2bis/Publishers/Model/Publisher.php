<?php
declare(strict_types=1);

namespace Bis2bis\Publishers\Model;

use Magento\Framework\Model\AbstractModel;
use Bis2bis\Publishers\Api\Data\PublisherInterface;

/**
 * Publisher Model
 */
class Publisher extends AbstractModel implements PublisherInterface
{
    /**
     * Initialize resource model.
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init('Bis2bis\Publishers\Model\ResourceModel\Publisher');
    }

    /**
     * Get the publisher ID.
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->getData(self::ENTITY_ID);
    }

    /**
     * Set the publisher ID.
     *
     * @param int $id
     * @return $this
     */
    public function setId($id)
    {
        return $this->setData(self::ENTITY_ID, $id);
    }

    /**
     * Get the publisher name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * Set the publisher name.
     *
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * Get the publisher status.
     *
     * @return bool
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * Set the publisher status.
     *
     * @param bool $status
     * @return $this
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * Get the publisher address.
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->getData(self::ADDRESS);
    }

    /**
     * Set the publisher address.
     *
     * @param string $address
     * @return $this
     */
    public function setAddress($address)
    {
        return $this->setData(self::ADDRESS, $address);
    }

    /**
     * Get the publisher logo.
     *
     * @return string|null
     */
    public function getLogo()
    {
        return $this->getData(self::LOGO);
    }

    /**
     * Set the publisher logo.
     *
     * @param string $logo
     * @return $this
     */
    public function setLogo($logo)
    {
        return $this->setData(self::LOGO, $logo);
    }

    /**
     * Get the publisher CNPJ.
     *
     * @return string|null
     */
    public function getCnpj()
    {
        return $this->getData(self::CNPJ);
    }

    /**
     * Set the publisher CNPJ.
     *
     * @param string $cnpj
     * @return $this
     */
    public function setCnpj($cnpj)
    {
        return $this->setData(self::CNPJ, $cnpj);
    }
}
