<?php
/**
 * 
 * @author adelbertsilla
 *
 */
namespace HealthCareAbroad\InstitutionBundle\Services;

use Doctrine\ORM\Query;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;

use Doctrine\Bundle\DoctrineBundle\Registry;

class InstitutionGalleryService
{
    /**
     * @param $doctrine Registry
     */
    protected $doctrine;

    public function setDoctrine(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
    }
    
    public function getInstitutionPhotos($institutionId)
    {
        $connection = $this->doctrine->getConnection();

        $query = "SELECT b.* FROM gallery_media AS a 
                    INNER JOIN media AS b ON a.media_id = b.id LEFT JOIN gallery AS c ON a.gallery_id = c.id   
                    WHERE c.institution_id = :institutionId 
                    ORDER BY b.id DESC";
        $stmt = $connection->prepare($query);
        $stmt->bindValue('institutionId', $institutionId);
        $stmt->execute();

        return $stmt->fetchAll(Query::HYDRATE_ARRAY); 
    }
    
    public function institutionHasPhotos($institutionId)
    {
        $connection = $this->doctrine->getConnection();

        $query = "SELECT a.media_id FROM gallery_media AS a LEFT JOIN gallery AS b ON a.gallery_id = b.id  WHERE b.institution_id = :institutionId LIMIT 1";
        $stmt = $connection->prepare($query);
        $stmt->bindValue('institutionId', $institutionId);
        $stmt->execute();
        $result = $stmt->fetchAll(Query::HYDRATE_ARRAY);
        
        return !empty($result);
    }
    
    public function getInstitutionMedicalCenterPhotos($institutionMedicalCenterId)
    {
        $connection = $this->doctrine->getConnection();
        $query = "SELECT b.*, c.id AS institution_medical_center_id, c.name AS institution_medical_center_name 
                    FROM institution_medical_center_media AS a 
                    INNER JOIN media AS b ON a.media_id = b.id
                    INNER JOIN institution_medical_centers AS c ON a.institution_medical_center_id = c.id 
                    WHERE a.institution_medical_center_id = :centerId ORDER BY b.id DESC";

        $stmt = $connection->prepare($query);
        $stmt->bindValue('centerId', $institutionMedicalCenterId);
        $stmt->execute();
        
        return $stmt->fetchAll(Query::HYDRATE_ARRAY);
    }
    
    public function getPhotosLinkedToMedicalCenter($institutionId)
    {
        $centersWithPhotos = array();

        $connection = $this->doctrine->getConnection();
        $query = "SELECT a.*, b.name AS institution_medical_center_name FROM institution_medical_center_media AS a INNER JOIN institution_medical_centers AS b ON a.institution_medical_center_id = b.id WHERE b.institution_id = :institutionId";
        $stmt = $connection->prepare($query);
        $stmt->bindValue('institutionId', $institutionId);
        $stmt->execute();
        
        $results = $stmt->fetchAll(Query::HYDRATE_ARRAY);
        
        foreach($results as $each) {
            $centersWithPhotos[$each['media_id']][$each['institution_medical_center_id']] = $each['institution_medical_center_name'];
        }
        
        //var_dump($centersWithPhotos); exit;
        
        return $centersWithPhotos;
    }
}