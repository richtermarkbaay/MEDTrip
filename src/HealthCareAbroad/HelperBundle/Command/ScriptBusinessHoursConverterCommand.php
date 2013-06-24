<?php
/**
 * @author Adelbert Silla
 */

namespace HealthCareAbroad\HelperBundle\Command;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use HealthCareAbroad\InstitutionBundle\Entity\BusinessHour;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class ScriptBusinessHoursConverterCommand extends ContainerAwareCommand
{     
    protected function configure()
    {
        $this->setName('script:convertBusinessHours')->setDescription('Convert Business Hours JSON string to businessHours table entry.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getEntityManager();
        $qb = $em->createQueryBuilder();
        $result = $qb->select('a')
                     ->from('InstitutionBundle:InstitutionMedicalCenter', 'a')
                     ->where('a.oldBusinessHours IS NOT NULL')
                     ->getQuery()->getResult();

        $defaultTimeFrom = new \DateTime(date('Y-m-d' ) . ' 08:00:00 AM');
        $defaultTimeTo = new \DateTime(date('Y-m-d' ). ' 08:00:00 PM');

        foreach($result as $i => $each) {
            $persistData = false;
            $oldBusinessHours = json_decode($each->getOldBusinessHours(), true);
            
            if(is_array($oldBusinessHours)) {

                $output->writeln("clinicId: " . $each->getId());
    
                foreach($oldBusinessHours as $day => $time) {
                    $businessHour = new BusinessHour();
                    $weekdayBitValue = $this->_getBusinessHoursWeekdayBitValue($day);
                    $output->write("$day: ");
                    
                    if(isset($time['from']) && isset($time['to']) && $time['from'] && $time['to']) {
                        $businessHour->setOpening(new \DateTime(date('Y-m-d' ). ' ' .$time['from']));
                        $businessHour->setClosing(new \DateTime(date('Y-m-d' ). ' ' .$time['to']));

                        $output->write("from: " . $time['from'] . " to: " . $time['to']);
    
                    } else if(isset($time['isOpen'])) {
                        $businessHour->setOpening($defaultTimeFrom);
                        $businessHour->setClosing($defaultTimeTo);
   
                        $output->write(" - from: " . $businessHour->getOpening()->format('h:m:s') . ", to: " . $businessHour->getClosing()->format('h:m:s'));
                    }
    
                    if(isset($time['notes']) && $time['notes']) {
                        $businessHour->setNotes($time['notes']);
                        $output->writeln(" - notes: " . $time['notes']);
                    }
    
                    if($businessHour->getOpening() && $businessHour->getClosing() && $weekdayBitValue) {
                        $businessHour->setWeekdayBitValue($weekdayBitValue);
                        $businessHour->setInstitutionMedicalCenter($each);
                        $each->addBusinessHour($businessHour);

                        $persistData = true;
                    }
    
                    $output->writeln('');
                }

                $each->setOldBusinessHours(null);
                if($persistData) {
                    $em->persist($each);
                }
            }

        }
        
        $em->flush();

        // Print end of script
        $output->writeln('end of ' . $this->getName() . ' script');
    }

    /**
     * 
     * @param unknown_type $scriptName
     * @return string
     */
    private function runScript($scriptName = '')
    {
        if(!$scriptName) return;

        return shell_exec("app/console $scriptName > /dev/null &");
    }


    private function _getBusinessHoursWeekdayBitValue($day)
    {
        $daysBitValues = array(
            'monday' => 1,
            'tuesday' => 2,
            'wednesday' => 4,
            'thursday' => 8,
            'friday' => 16,
            'saturday' => 32,
            'sunday' => 64
        );

        $day = strtolower($day);

        return isset($daysBitValues[$day]) ? $daysBitValues[$day] : null; 
    }
}