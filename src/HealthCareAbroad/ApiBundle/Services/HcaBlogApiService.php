<?php

namespace HealthCareAbroad\ApiBundle\Services;

use Doctrine\Bundle\DoctrineBundle\Registry;

/**
 * 
 * @author Adelbert D. Silla
 *
 */
class HcaBlogApiService
{
    protected $doctrine;

    protected $connection;

    public function setDoctrine(Registry $doctrine)
    {
        $this->doctrine = $doctrine;

        $this->connection = $doctrine->getConnection('hca_blog');
    }

    
    /**
     * @param array $criteria
     * @return array
     */
    function getBlogs($criteria = array()) {

        $limit = isset($criteria['limit']) ? $criteria['limit'] : 10; 

        // SubQuery for selecting attachment (thumbnail) data
        $thumbnailSubQuery = "SELECT b1.post_id, REPLACE(a1.guid, a1.post_title, concat(a1.post_title, '-64x64')) AS thumbnail_url FROM wp_postmeta AS b1
                                LEFT JOIN wp_posts AS a1 ON b1.meta_value = a1.ID
                                WHERE b1.meta_key = :thumbnailMetaKey";

//         if($cat = $request->get('cat')) {
//             $arrCategories = explode(',', $cat);
//             $catPlaceholder = $this->formatCategoriesPlaceholder($arrCategories);

//             $query = "SELECT a.*, b.thumbnail_url, (SELECT count(c.object_id) FROM wp_term_relationships as c LEFT JOIN wp_terms AS d ON c.term_taxonomy_id = d.term_id WHERE a.ID = c.object_id AND name IN ($catPlaceholder) GROUP BY c.object_id) AS cnt FROM wp_posts AS a
//                 LEFT JOIN ($subQuery) AS b ON a.ID = b.post_id
//                 WHERE a.post_parent = :postParent AND a.post_type = :type AND a.post_status = :status ORDER BY a.post_date DESC LIMIT $limit";

//             $stmt = $conn->prepare($query);
//             foreach($arrCategories as $i => $each) {
//                 if(trim($each))
//                     $stmt->bindValue("cat_$i", $each, \PDO::PARAM_STR);
//             }

//         } else {
            $query = "SELECT a.ID AS id, a.post_title AS title, c.meta_value AS description, CONCAT(substring_index(a.guid,'?',1), a.post_name) AS url, b.thumbnail_url FROM wp_posts AS a 
                LEFT JOIN ($thumbnailSubQuery) AS b ON a.ID = b.post_id 
                LEFT JOIN wp_postmeta AS c ON a.ID = c.post_id AND c.meta_key = :shortDescMetaKey  
                WHERE a.post_parent = :postParent AND a.post_type = :type AND a.post_status = :status ORDER BY a.post_date DESC LIMIT $limit";

            $stmt = $this->connection->prepare($query);
//         }

            $stmt->bindValue('shortDescMetaKey', 'short_description');
            $stmt->bindValue('thumbnailMetaKey', '_thumbnail_id');
            $stmt->bindValue('postParent', 0);
            $stmt->bindValue('type', 'post');
            $stmt->bindValue('status', 'publish');
            $stmt->execute();

            $results = $stmt->fetchAll();

            return $results;
    }

    private function formatCategoriesPlaceholder(array $categoriesArr = array()) {
        $catPlaceholder = '';
        foreach($categoriesArr as $i => $each) {
            if(trim($each))
                $catPlaceholder .= ", :cat_$i";
        }

        return substr($catPlaceholder, 1);
    }

    /**
     * Query to get post_id and name
     */
    //     if($cat = $request->get('cat')) {
    //         $categoriesArr = explode(',', $cat);
    
    //         $catPlaceholder = '';
    //         foreach($categoriesArr as $i => $each) {
    //             $catPlaceholder .= ", :cat_$i";
        //         }
    
        //         //". substr($catPlaceholder, 1) ."
        //         echo $query = "SELECT a.object_id, b.name FROM wp_term_relationships as a LEFT JOIN wp_terms AS b ON a.term_taxonomy_id = b.term_id WHERE name IN (".substr($catPlaceholder, 1).") GROUP BY a.object_id";
        //         //            echo $query = "SELECT * FROM wp_terms WHERE name IN (:categories)";
        //         $stmt = $conn->prepare($query);
        //         var_dump($conn::PARAM_STR_ARRAY);
        //         //$stmt->bindValue('categories', $cat, $conn::PARAM_STR_ARRAY);
    
        //         foreach($categoriesArr as $i => $each) {
        //             $stmt->bindValue("cat_$i", $each, \PDO::PARAM_STR);
        //         }
    
        //         $stmt->execute();
        //         $results = $stmt->fetchAll();
    
        //         var_dump($results);
        //     }

}