<?php

namespace Pumukit\SchemaBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Pumukit\SchemaBundle\Document\Broadcast
 *
 * @MongoDB\Document(repositoryClass="Pumukit\SchemaBundle\Repository\BroadcastRepository")
 */
class Broadcast
{
    const BROADCAST_TYPE_PUB = 'public';
    const BROADCAST_TYPE_PRI = 'private';
    const BROADCAST_TYPE_COR = 'corporative';

  /**
   * @var int $id
   *
   * @MongoDB\Id
   */
  private $id;

  /**
   * @var ArrayCollection $multimedia_objects
   *
   * @MongoDB\ReferenceMany(targetDocument="MultimediaObject", mappedBy="broadcast")
   */
  private $multimedia_objects;

  /**
   * @var string $name
   *
   * @MongoDB\String
   */
  private $name;

  /**
   * @var string $broadcast_type_id
   *
   * @MongoDB\String
   */
  private $broadcast_type_id = self::BROADCAST_TYPE_PUB;

  /**
   * @var string $passwd
   *
   * @MongoDB\String
   */
  private $passwd;

  /**
   * @var boolean $default_sel
   *
   * @MongoDB\Boolean
   */
  private $default_sel = false;

  /**
   * @var string $description
   *
   * @MongoDB\Raw
   */
  private $description = array('en' => '');

  /**
   * @var locale $locale
   */
  private $locale;

  /**
   * Get id
   *
   * @return int
   */
  public function getId()
  {
      return $this->id;
  }

  /**
   * Add multimedia_object
   *
   * @param MultimediaObject $multimedia_object
   */
  public function addMultimediaObject(MultimediaObject $multimedia_object)
  {
      $this->multimedia_objects[] = $multimedia_object;
      $multimedia_object->setBroadcast($this);
  }

  /**
   * Remove multimedia_object
   *
   * @param MultimediaObject $multimedia_object
   */
  public function removeMultimediaObject(MultimediaObject $multimedia_object)
  {
      $this->multimedia_objects->removeElement($multimedia_object);
  }

  /**
   * Contains multimedia_object
   *
   * @param MultimediaObject $multimedia_object
   *
   * @return boolean
   */
  public function containsMultimediaObject(MultimediaObject $multimedia_object)
  {
      return $this->multimedia_objects->contains($multimedia_object);
  }

  /**
   * Get multimedia_objects
   *
   * @return ArrayCollection
   */
  public function getMultimediaObjects()
  {
      return $this->multimedia_objects;
  }

  /**
   * Set name
   *
   * @param string $name
   */
  public function setName($name)
  {
      $this->name = $name;
  }

  /**
   * Get name
   *
   * @return string
   */
  public function getName()
  {
      return $this->name;
  }

  /**
   * Set broadcast_type_id
   *
   * @param string $broadcast_type_id
   */
  public function setBroadcastTypeId($broadcast_type_id)
  {
      $this->broadcast_type_id = $broadcast_type_id;
  }

  /**
   * Get broadcast_type_id
   *
   * @return string
   */
  public function getBroadcastTypeId()
  {
      return $this->broadcast_type_id;
  }

  /**
   * Set passwd
   *
   * @param string $passwd
   */
  public function setPasswd($passwd)
  {
      $this->passwd = $passwd;
  }

  /**
   * Get passwd
   *
   * @return string
   */
  public function getPasswd()
  {
      return $this->passwd;
  }

  /**
   * Set default_sel
   *
   * @param boolean $defatul_sel
   */
  public function setDefaultSel($default_sel)
  {
      $this->default_sel = $default_sel;
  }

  /**
   * Get default_sel
   *
   * @return boolean
   */
  public function getDefaultSel()
  {
      return $this->default_sel;
  }

  /**
   * Set description
   *
   * @param string $description
   */
  public function setDescription($description, $locale = null)
  {
      if ($locale == null) {
          $locale = $this->locale;
      }
      $this->description[$locale] = $description;
  }

  /**
   * Get description
   *
   * @return string
   */
  public function getDescription($locale = null)
  {
      if ($locale == null) {
          $locale = $this->locale;
      }
      if (!isset($this->description[$locale])) {
          return null;
      }

      return $this->description[$locale];
  }

  /**
   * Set I18n description
   *
   * @param array $description
   */
  public function setI18nDescription(array $description)
  {
      $this->description = $description;
  }

  /**
   * Get i18n description
   *
   * @return array
   */
  public function getI18nDescription()
  {
      return $this->description;
  }

  /**
   * Set locale
   *
   * @param string $locale
   */
  public function setLocale($locale)
  {
      $this->locale = $locale;
  }

  /**
   * Get locale
   *
   * @return string
   */
  public function getLocale()
  {
      return $this->locale;
  }

  /**
   * Clone Direct
   *
   * @return Direct
   */
  public function cloneResource()
  {
      $aux = clone $this;
      $aux->id = null;

      return $aux;
  }

  /**
   * to String
   */
  public function __toString()
  {
      return $this->broadcast_type_id;
  }

}
