<?php

namespace HealthCareAbroad\HelperBundle\Command;

use Doctrine\Bundle\DoctrineBundle\Registry;

use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Console\Input\InputInterface;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class PopulateInstitutionAccountOwnerEmailCommand extends ContainerAwareCommand
{
    /**
     * @var Registry
     */
    private $doctrine;
    
    /**
     * @var OutputInterface
     */
    private $output;
    
    private $nonUniqueContactEmails = array();
    
    protected function configure()
    {
        $this->setName('helper:populateAccountOwner')->setDescription('Update institution account owner email with institution contact email');   
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->doctrine = $this->getContainer()->get('doctrine');
        $this->output = $output;
        
        $sql = "SELECT inst.id as institution_id, inst.contact_email, inst.name as institution_name, cg_a.id as account_id, cg_a.first_name, cg_a.last_name, cg_a.email as account_email ".
        "FROM `healthcareabroad`.`institutions` inst ".
        "INNER JOIN `healthcareabroad`.`institution_users` iu ON inst.id = iu.`institution_id` ".
        "INNER JOIN `chromedia_global`.`accounts` cg_a ON iu.`account_id` = cg_a.`id` ".
        "WHERE 1=1 ".
        "AND cg_a.email LIKE '%healthcareabroad.com' ".
        "AND inst.contact_email IS NOT NULL ".
        "AND LENGTH(TRIM(inst.contact_email)) != 0 ".
        "ORDER BY inst.`contact_email` ASC";
        
        $connection = $this->doctrine->getConnection();
        $statement = $connection->prepare($sql);
        $statement->execute();
        
        $this->output->writeln("------------------------------------------------------------");
        $this->output->writeln('Updating '.$statement->rowCount().' institutions');
        while ($row = $statement->fetch()){
            $this->processData($row);
        }
        $this->output->writeln("------------------------------------------------------------");
        
    }
    
    private function processData($data)
    {
        $accountEmail = $data['account_email'];
        $contactEmail = $data['contact_email'];
        $this->output->write("Change account email of institution #".$data['institution_id']." from {$accountEmail} -> {$contactEmail}");
        
        // check if there is already an account using $contactEmail as email
        //$account = $this->findAccountByEmail($contactEmail);
        $isUniqueContactEmail = $this->isUniqueContactEmail($contactEmail);
        if (!$isUniqueContactEmail) {
            $this->output->writeln('[NOT OK - Contact email is not unique]');
        }
        else {
            $this->updateEmail($data['account_id'], $accountEmail, $contactEmail);
            $this->output->writeln('[OK]');
        }
    }
    
    private function isUniqueContactEmail($contactEmail)
    {
        if (\in_array($contactEmail, $this->nonUniqueContactEmails)) {
            // we already processed this email and it is not unique
            return false;    
        }
        
        $sql = "SELECT * FROM `healthcareabroad`.`institutions` WHERE `contact_email` = :email";
        $stmt = $this->doctrine->getConnection()->prepare($sql);
        $stmt->bindValue('email', $contactEmail);
        $stmt->execute();
        $rowCount = $stmt->rowCount(); 
        if ($rowCount > 1) {
            // non unique email
            $this->nonUniqueContactEmails[] = $contactEmail;
            return false;
        }
        elseif (1 == $rowCount){
            return true;
        }
        else {
            return 0; // contact email not found. this should not be reached
        }
    }
    
    private function findAccountByEmail($email)
    {
        $sql = "SELECT * FROM `chromedia_global`.`accounts` WHERE `email` = :email";
        $stmt = $this->doctrine->getConnection()->prepare($sql);
        $stmt->bindValue('email', $email);
        $stmt->execute();
        $account = $stmt->fetch();
        
        return $account;
    }
    
    private function updateEmail($accountId, $accountEmail, $contactEmail)
    {
        $updateSql = "UPDATE `chromedia_global`.`accounts` SET `email` = :contactEmail WHERE `id` = :accountId AND `email` = :accountEmail";
        $statement = $this->doctrine->getConnection()->prepare($updateSql);
        $statement->bindValue('accountId', $accountId);
        $statement->bindvalue('accountEmail', $accountEmail);
        $statement->bindValue('contactEmail', $contactEmail);
        $statement->execute();
    }
}