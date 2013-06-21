INSERT INTO search_terms 
(term_id, institution_id, institution_medical_center_id, term_document_id, document_id, type, status, specialization_id, sub_specialization_id, treatment_id, country_id, city_id, specialization_name, sub_specialization_name, treatment_name, country_name, city_name) 
SELECT a.term_id, f.id as institution_id, e.id as institution_medical_center_id, a.id as term_document_id, b.id as document_id, 3 as type, (IF(e.status = 2 AND f.status = 9, 1, 0) ) as status, i.id as specialization_id, h.id as sub_specialization_id, b.id as treatment_id, j.id as country_id, k.id as city_id, i.name as specialization_name, h.name as sub_specialization_name, b.name as treatment_name, j.name as country_name, k.name as city_name
FROM term_documents AS a 
LEFT JOIN treatments AS b ON a.document_id = b.id
INNER JOIN institution_treatments AS c ON b.id = c.treatment_id
LEFT JOIN institution_specializations AS d ON d.id = c.institution_specialization_id
LEFT JOIN institution_medical_centers AS e ON e.id = d.institution_medical_center_id
LEFT JOIN institutions AS f ON e.institution_id = f.id
LEFT JOIN treatment_sub_specializations AS g ON b.id = g.sub_specialization_id
LEFT JOIN sub_specializations AS h ON h.id = g.sub_specialization_id
LEFT JOIN specializations AS i ON d.specialization_id = i.id
LEFT JOIN countries AS j ON f.country_id = j.id
LEFT JOIN cities AS k ON f.city_id = k.id
WHERE a.type = 3

UNION

SELECT a.term_id, h.id as institution_id, g.id as institution_medical_center_id, a.id as term_document_id, b.id as document_id, 2 as type, (IF(g.status = 2 AND h.status = 9, 1, 0)) as status, i.id as specialization_id, b.id as sub_specialization_id, d.id as treatment_id, j.id country_id, k.id as city_id, i.name as specialization_name, b.name as sub_specialization_name, d.name as treatment_name, j.name as country_name, k.name as city_name
FROM term_documents AS a 
LEFT JOIN sub_specializations AS b ON a.document_id = b.id
LEFT JOIN treatment_sub_specializations AS c ON b.id = c.sub_specialization_id
LEFT JOIN treatments AS d ON d.id = c.treatment_id
INNER JOIN institution_treatments AS e ON d.id = e.treatment_id
LEFT JOIN institution_specializations AS f ON f.id = e.institution_specialization_id
LEFT JOIN institution_medical_centers AS g ON g.id = f.institution_medical_center_id
LEFT JOIN institutions AS h ON g.institution_id = h.id
LEFT JOIN specializations AS i ON f.specialization_id = i.id
LEFT JOIN countries AS j ON h.country_id = j.id
LEFT JOIN cities AS k ON h.city_id = k.id
WHERE a.type = 2

UNION

SELECT a.term_id, e.id as institution_id, d.id as institution_medical_center_id, a.id as term_document_id, b.id as document_id, 1 as type, (IF(d.status = 2 AND e.status = 9, 1, 0)) as status, b.id as specialization_id, null as sub_specialization_id, null as treatment_id, j.id country_id, k.id as city_id, b.name as specialization_name, null as sub_specialization_name, null as treatment_name, j.name as country_name, k.name as city_name
FROM term_documents AS a 
LEFT JOIN specializations AS b ON a.document_id = b.id
INNER JOIN institution_specializations AS c ON c.specialization_id = b.id
LEFT JOIN institution_medical_centers AS d ON c.institution_medical_center_id = d.id
LEFT JOIN institutions AS e ON d.institution_id = e.id
LEFT JOIN countries AS j ON e.country_id = j.id
LEFT JOIN cities AS k ON e.city_id = k.id
WHERE a.type = 1;