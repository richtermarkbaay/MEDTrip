<?php
/**
 * test file for executing a plain php page similar to 
 * /search/ajaxLoadAllSearchOptions?type=all
 */
$start = \microtime(true);
include 'include.php';
ini_set('display_errors', 1);
$mysql = connect();
$u = $mysql->query('USE `healthcareabroad`');
$loadTermsSql = "SELECT a.id AS value, a.name AS label FROM terms AS a INNER JOIN search_terms AS b ON a.id = b.term_id WHERE b.status = 1 GROUP BY a.name ORDER BY a.name ASC";

$result = $mysql->query($loadTermsSql, MYSQLI_USE_RESULT);
$treatments = array();
while ($row = $result->fetch_array(MYSQLI_ASSOC)){
    $treatments[] = $row;
}

// free result
$result->close();

$sql = "SELECT a.country_name AS label, CONCAT(CAST(a.country_id AS CHAR), '-0') AS value, a.country_name as country, a.country_name AS orderedLabel FROM search_terms AS a WHERE a.status = 1 UNION SELECT CONCAT(a.city_name, ', ', a.country_name) AS label, CONCAT(CAST(a.country_id AS CHAR), '-', CAST(a.city_id AS CHAR)) AS value, a.country_name as country, CONCAT(a.country_name, a.city_name) AS orderedLabel FROM search_terms AS a WHERE a.city_id IS NOT NULL AND a.status = 1 GROUP BY label ORDER BY orderedLabel ASC";
$result = $mysql->query($sql);
$destinations = array();
while($row = $result->fetch_array(MYSQLI_ASSOC)){
    $destinations[] = $row;
}
$result->close();
$end = \microtime(true); 
$diff = $end-$start;
header('Content-Type: application/json');
echo \json_encode(array('treatments' => $treatments, 'destinations' => $destinations, 'executionTime' => $diff));
