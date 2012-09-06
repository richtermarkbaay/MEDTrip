<?php 

namespace HealthCareAbroad\AdminBundle\Events;

final class MedicalProcedureTypeEvents
{
	const ON_ADD_MEDICAL_PROCEDURE_TYPE = 'event.medical_procedure_type.add';
	const ON_EDIT_MEDICAL_PROCEDURE_TYPE = 'event.medical_procedure_type.edit';
	const ON_DELETE_MEDICAL_PROCEDURE_TYPE = 'event.medical_procedure_type.delete';
}