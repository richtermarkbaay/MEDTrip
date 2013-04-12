<?php 
namespace HealthCareAbroad\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HealthCareAbroad\AdminBundle\Entity\StaticPage
 */
class StaticPage
{
    
    const SECTION_ADMIN = 1;
    const SECTION_CLIENT_ADMIN = 2;
    const SECTION_FRONTEND = 3;
    
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
    private $url;

    /**
     * @var string
     */
    private $websiteSection;

    /**
     * @var string
     */
    private $content;


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
     * @return StaticPage
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
     * Set url
     *
     * @param string $url
     * @return StaticPage
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

    /**
     * Set websiteSection
     *
     * @param string $websiteSection
     * @return StaticPage
     */
    public function setWebsiteSection($websiteSection)
    {
        $this->websiteSection = $websiteSection;
    
        return $this;
    }

    /**
     * Get websiteSection
     *
     * @return string 
     */
    public function getWebsiteSection()
    {
        return $this->websiteSection;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return StaticPage
     */
    public function setContent($content)
    {
        $this->content = $content;
    
        return $this;
    }

    /**
     * Get content
     *
     * @return string 
     */
    public function getContent()
    {
        return $this->content;
    }
}