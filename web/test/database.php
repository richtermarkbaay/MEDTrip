<?php
$pages = <<< PAGES
<ul>
    <li><a href="/test/plaintext.html">Plaintext</a></li>
    <li><a href="/test/institution.html">Institution markup</a></li>
    <li><a href="/test/database.php">Page with 50 Database Queries</a></li>
    <li><a href="/test/multiple-ajax.php">Multiple ajax</a></li>
    <li><a href="/symfony-test/baseline">Baseline</a></li>
    <li><a href="/symfony-test/baseline-resources">Baseline with resources</a></li>
    <li><a href="/symfony-test/frontend-base">Frontend base</a></li>
    <li><a href="/symfony-test/ajax">Symfony with multiple ajax requests</a></li>
</ul>
PAGES;

echo $pages;

function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}
//exit;

$pdo = new PDO("mysql:host=staging-hca-db.cn3rrwophu7o.us-east-1.rds.amazonaws.com;dbname=healthcareabroad", 'root', 'cfe_#602_:D');
//$pdo = new PDO("mysql:host=healthcare-dev;dbname=healthcareabroad", 'root', 'hcadbadmin');

$sql = "
        SELECT SQL_NO_CACHE b.id specialization_id, b.name specialization_name, b.slug specialization_slug, c.id sub_specialization_id, c.name sub_specialization_name, c.slug sub_specialization_slug, d.id treatment_id, d.name treatment_name, d.slug treatment_slug
        FROM search_terms AS a
        LEFT JOIN specializations AS b ON a.specialization_id = b.id
        LEFT JOIN sub_specializations AS c ON a.sub_specialization_id = c.id
        LEFT JOIN treatments AS d ON a.treatment_id = d.id
        WHERE a.status = 1
        GROUP BY b.id, c.id, d.id
        ORDER BY b.id, c.id, d.id

                ";


$time_start = microtime_float();
for ($i = 0; $i < 50; $i++) {
    $pdo->query($sql);
}
$time_end = microtime_float();

$time = $time_end - $time_start;

echo '<b>', $time, '</b><br/>';

// foreach ($pdo->query($sql) as $row) {
//     var_dump($row, '<br/>');
// }
$pdo = null;