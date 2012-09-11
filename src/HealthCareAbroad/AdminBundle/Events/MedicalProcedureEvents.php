<?php 

namespace HealthCareAbroad\AdminBundle\Events;

final class MedicalProcedureEvents
{
	const ON_ADD_MEDICAL_PROCEDURE = 'event.medical_procedure.add';
	const ON_EDIT_MEDICAL_PROCEDURE = 'event.medical_procedure.edit';
	const ON_DELETE_MEDICAL_PROCEDURE = 'event.medical_procedure.delete';
}