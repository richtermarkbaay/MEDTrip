<?php

namespace HealthCareAbroad\HelperBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HealthCareAbroad\HelperBundle\Entity\News
 */
class News
{
	
	const STATUS_ACTIVE = 1;
	const STATUS_INACTIVE = 0;
	
	
    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var string $title
     */
    private $title;

    /**
     * @var string $description
     */
    private $description;

    /**
     * @var string $slug
     */
    private $slug;

    /**
     * @var smallint $status
     */
    private $status;

    /**
     * @var datetime $dateCreated
     */
    private $dateCreated;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
    
    public function __toString()
    {
    	return $this->getTitle();
    }
    
    public function slugify($text)
    {
    	// replace non letter or digits by -
    	$text = preg_replace('#[^\\pL\d]+#u', '-', $text);
    
    	// trim
    	$text = trim($text, '-');
    
    	// transliterate
    	if (function_exists('iconv'))
    	{
    		$text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    	}
    
    	// lowercase
    	$text = strtolower($text);
    
    	// remove unwanted characters
    	$text = preg_replace('#[^-\w]+#', '', $text);
    
    	if (empty($text))
    	{
    		return 'n-a';
    	}
    
    	return $text;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return News
     */
    public function setTitle($title)
    {
        $this->title = $title;
        $this->setSlug($this->title);
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return News
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return News
     */
    public function setSlug($slug)
    {
        $this->slug = $this->slugify($slug);
    }

    /**
     * Get slug
     *
     * @return string 
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set status
     *
     * @param smallint $status
     * @return News
     */
    public function setStatus($status)
    {
     	  $this->status = $status;
        return $this;
    }

    /**
     * Get status
     *
     * @return smallint 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set dateCreated
     *
     * @param datetime $dateCreated
     * @return News
     */
    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;
        return $this;
    }

    /**
     * Get dateCreated
     *
     * @return datetime 
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }
}