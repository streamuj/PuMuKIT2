<?php

namespace Pumukit\SchemaBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * Pumukit\SchemaBundle\Document\EmbeddedEventSession.
 *
 * @MongoDB\EmbeddedDocument
 */
class EmbeddedEventSession
{
    /**
     * @var int
     *
     * @MongoDB\Id
     */
    private $id;

    /**
     * @var date
     *
     * @MongoDB\Field(type="date")
     */
    private $start;

    /**
     * @var date
     *
     * @MongoDB\Field(type="date")
     */
    private $ends;

    /**
     * @var int
     *
     * @MongoDB\Field(type="int")
     */
    private $duration = 0;

    /**
     * @var string
     *
     * @MongoDB\Field(type="string")
     */
    private $notes;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return date
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * @param date $start
     */
    public function setStart($start)
    {
        $this->start = $start;
    }

    /**
     * @return date
     */
    public function getEnds()
    {
        return $this->ends;
    }

    /**
     * @param date $ends
     */
    public function setEnds($ends)
    {
        $this->ends = $ends;
    }

    /**
     * @return int
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * @param int $duration
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;
    }

    /**
     * @return string
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * @param string $notes
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;
    }
}
