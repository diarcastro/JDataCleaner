<?php

/*
 *  Created by Diego Castro <diego.castro@knowbi.com>
 */

/**
 * Limpieza de parámetros y datos con los filtros del joomla
 * También codifica datos para impresión en HTML
 *
 * @author Diego Castro <diego.castro@knowbi.com>
 * @copyright (c) 2015, Diego Castro
 * @version 1.0
 */
class JDataCleaner extends JInput{
  /**
   * Vista para poder ejecutar el método escape()
   * @var JView
   */
  private $_view=null;

  /**
   * Instancia de la clase
   * @var JDataCleaner
   */
  private static $_instance;

  public static function getInstance(){
    if(!self::$_instance){
      self::$_instance=new JDataCleaner();
    }
    return self::$_instance;
  }

  /**
   * Retorna la instancia de la Vista
   * @return JView
   */
  protected function getView(){
    if(!$this->_view){
      $this->_view=new JView();
    }
    return $this->_view;
  }

  /**
   * Escapa un valor para que evitar XSS almacenado
   *
   * If escaping mechanism is either htmlspecialchars or htmlentities, uses
   * {@link $_encoding} setting.
   *
   * @param   mixed  $var  The output to escape.
   * @return  mixed  The escaped value.
   *
   * @since   11.1
   */
  public function escape($var){
    return $this->getView()->escape($var);
  }
  /**
   * Limpia todos los valores de $_POST
   * @param string $key Clave dentro del array $_POST, si no la envía se tomará toda el array $_POST
   * @param string $filter Filtro a aplicar
   * @return mixed
   */
  public function cleanPost($key=null,$filter='string'){
    return $this->purify($key?$_POST[$key]:$_POST,$filter);
  }
  /**
   * Limpia todos los valores del $_GET
   * @param string $filter Filtro a aplicar
   * @return mixed
   */
  public function cleanGet($filter='string'){
    return $this->purify($_GET,$filter);
  }
  /**
   * Limpia un recurso para que no tenga caracteres maliciosos
   * @param mixed $data Datos a limpiar
   * @param string $type Tipo de limpieza
   * @return mixed Datos limpios
   */
  public function purify($data,$type='string'){
    if(isset($data) && is_array($data)){
      $data=$this->_purifyArray($data,$type);
    }elseif(isset($data) && !is_array($data)){
      $data=$this->filter->clean(trim($data),$type);
    }
    return $data;
  }
  /**
   * Limpia un recurso para que no tenga caracteres maliciosos
   * @param mixed $data Datos a limpiar
   * @param string $type Tipo de limpieza
   * @return mixed Datos limpios
   * @see purify
   */
  public function clean($data,$type='string'){
    return $this->purify($data,$type);
  }

  /**
   * Limpia recursivamente un array de datos
   * @param array $arrayData
   * @param string $type Tipo de limpieza
   * @return mixed datos limpios
   */
  protected function _purifyArray(array $arrayData,$type='string'){
    foreach($arrayData as $key=> $value){
      if(isset($value) && is_array($value)){
        $arrayData[$key]=$this->_purifyArray($value,$type);
      }elseif(isset($value)){
        $arrayData[$key]=$this->filter->clean(trim($value),$type);
      }
    }
    return $arrayData;
  }

}
