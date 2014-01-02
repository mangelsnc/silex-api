<?php

namespace Album;

class Album
{
    private $uid;
    private $title;
    private $author;
    private $year;
    private $genre;

    public function __construct()
    {
        $this->uid = $this->generateUID();
    }

    private function generateUID()
    {
        $uid = substr(strtoupper(md5(uniqid())), 12, 8);

        return $uid;
    }

    public function setUID($uid)
    {
        return $this->uid = $uid;
    }

    public function getUID()
    {
        return $this->uid;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setAuthor($author)
    {
        $this->author = $author;
    }

    public function getAuthor()
    {
        return $this->author;
    }

    public function setYear($year)
    {
        $this->year = $year;
    }

    public function getYear()
    {
        return $this->year;
    }

    public function setGenre($genre)
    {
        $this->genre = $genre;
    }

    public function getGenre()
    {
        return $this->genre;
    }

    public function __toString()
    {
        return $this->title;
    }

    public function toArray()
    {
        return get_object_vars($this);
    }

    public function toJSON()
    {
        return json_encode(
                $this->toArray(), 
                JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT
        );
    }


}