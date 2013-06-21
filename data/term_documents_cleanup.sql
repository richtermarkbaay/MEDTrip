SELECT t.id, t.name, COUNT(td.id) as cnt_duplicate
FROM `term_documents` td INNER JOIN `terms` t
ON td.term_id = t.id
GROUP BY td.term_id, td.document_id, td.type
HAVING cnt_duplicate > 1