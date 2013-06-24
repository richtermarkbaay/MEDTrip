SELECT imc.* FROM `institution_medical_centers` imc
INNER JOIN `institution` inst ON imc.institution_id = inst.id
WHERE inst.status = 9
AND imc.status != 2


UPDATE `institution_medical_centers` imc
INNER JOIN `institutions` inst ON imc.institution_id = inst.id
SET imc.status = 2
WHERE inst.status =9
AND imc.status !=2
AND inst.id >=266  


DELETE institution_medical_center_doctors
FROM `institution_medical_center_doctors`
LEFT JOIN doctors ON `doctor_id` = doctors.id
WHERE doctors.id IS NULL

DELETE institution_medical_center_doctors
FROM `institution_medical_center_doctors`
LEFT JOIN institution_medical_centers ON `institution_medical_center_id` = institution_medical_centers.id
WHERE institution_medical_centers.id IS NULL

# update ranking by institution
UPDATE `institution_medical_centers` imc
SET imc.`ranking_points` = 1
WHERE imc.`institution_id` IN (65)

SELECT t.id, t.name, count(it.`treatment_id`) as cnt_link
FROM `treatments` t
INNER JOIN `institution_treatments` it ON t.id = it.`treatment_id`
GROUP BY t.id
ORDER BY cnt_link DESC
LIMIT 2


SELECT t.id, t.name, count(it.`treatment_id`) as cnt_link
FROM `treatments` t
INNER JOIN `institution_treatments` it ON t.id = it.`treatment_id`
WHERE t.id = 777
GROUP BY t.id
ORDER BY cnt_link DESC
LIMIT 2
