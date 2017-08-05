<?php

namespace BackendBundle\Entity;

/**
 * Lineapack
 */
class Lineapack
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $cantidad;

    /**
     * @var \BackendBundle\Entity\Pack
     */
    private $pack;

    /**
     * @var \BackendBundle\Entity\Producto
     */
    private $producto;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set cantidad
     *
     * @param integer $cantidad
     *
     * @return Lineapack
     */
    public function setCantidad($cantidad)
    {
        $this->cantidad = $cantidad;

        return $this;
    }

    /**
     * Get cantidad
     *
     * @return integer
     */
    public function getCantidad()
    {
        return $this->cantidad;
    }

    /**
     * Set pack
     *
     * @param \BackendBundle\Entity\Pack $pack
     *
     * @return Lineapack
     */
    public function setPack(\BackendBundle\Entity\Pack $pack = null)
    {
        $this->pack = $pack;

        return $this;
    }

    /**
     * Get pack
     *
     * @return \BackendBundle\Entity\Pack
     */
    public function getPack()
    {
        return $this->pack;
    }

    /**
     * Set producto
     *
     * @param \BackendBundle\Entity\Producto $producto
     *
     * @return Lineapack
     */
    public function setProducto(\BackendBundle\Entity\Producto $producto = null)
    {
        $this->producto = $producto;

        return $this;
    }

    /**
     * Get producto
     *
     * @return \BackendBundle\Entity\Producto
     */
    public function getProducto()
    {
        return $this->producto;
    }
}

