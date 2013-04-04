<?php

namespace HealthCareAbroad\HelperBundle\Entity;

class PageMetaConfiguration
{
    const PAGE_TYPE_STATIC = 1;
    
    const PAGE_TYPE_INSTITUTION =2;
    
    const PAGE_TYPE_SEARCH_RESULTS = 3;
    
    const PAGE_TYPE_INSTITUTION_MEDICAL_CENTER = 4;
    
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $keywords;

    /**
     * @var string
     */
    private $description;

    /**
     * @var integer
     */
    private $pageType;
    
    /**
     * @var string
     */
    private $url;


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
     * Set title
     *
     * @param string $title
     * @return PageMetaConfiguration
     */
    public function setTitle($title)
    {
        $this->title = $title;
    
        return $this;
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
     * Set keywords
     *
     * @param string $keywords
     * @return PageMetaConfiguration
     */
    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;
    
        return $this;
    }

    /**
     * Get keywords
     *
     * @return string 
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return PageMetaConfiguration
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
     * Set pageType
     *
     * @param integer $pageType
     * @return PageMetaConfiguration
     */
    public function setPageType($pageType)
    {
        $this->pageType = $pageType;
    
        return $this;
    }

    /**
     * Get pageType
     *
     * @return integer 
     */
    public function getPageType()
    {
        return $this->pageType;
    }

    /**
     * Set url
     *
     * @param string $url
     * @return PageMetaConfiguration
     */
    public function setUrl($url)
    {
        $this->url = $url;
    
        return $this;
    }

    /**
     * Get url
     *
     * @return string 
     */
    public function getUrl()
    {
        return $this->url;
    }
}