<?php

namespace BackendBundle\Entity;

/**
 * Linea
 */
class Linea
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
     * @var integer
     */
    private $preciolinea;

    /**
     * @var \BackendBundle\Entity\Pack
     */
    private $pack;

    /**
     * @var \BackendBundle\Entity\Producto
     */
    private $producto;

    /**
     * @var \BackendBundle\Entity\Reserva
     */
    private $reserva;


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
     * @return Linea
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
     * Set preciolinea
     *
     * @param integer $preciolinea
     *
     * @return Linea
     */
    public function setPreciolinea($preciolinea)
    {
        $this->preciolinea = $preciolinea;

        return $this;
    }

    /**
     * Get preciolinea
     *
     * @return integer
     */
    public function getPreciolinea()
    {
        return $this->preciolinea;
    }

    /**
     * Set pack
     *
     * @param \BackendBundle\Entity\Pack $pack
     *
     * @return Linea
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
     * @return Linea
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

    /**
     * Set reserva
     *
     * @param \BackendBundle\Entity\Reserva $reserva
     *
     * @return Linea
     */
    public function setReserva(\BackendBundle\Entity\Reserva $reserva = null)
    {
        $this->reserva = $reserva;

        return $this;
    }

    /**
     * Get reserva
     *
     * @return \BackendBundle\Entity\Reserva
     */
    public function getReserva()
    {
        return $this->reserva;
    }
}

