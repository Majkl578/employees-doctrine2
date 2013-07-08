<?php

namespace Benchmark;

use Benchmark\Entities\Employee;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Michael Moravec
 */
class BenchmarkCommand extends Command
{
	protected function configure()
	{
		$this->setName('benchmark:run');
	}

	public function execute(InputInterface $input, OutputInterface $output)
	{
		/** @var EntityManagerHelper $emHelper */
		$emHelper = $this->getHelper('em');
		$em = $emHelper->getEntityManager();

		$qb = $em->createQueryBuilder()
			->from('Benchmark\Entities\Employee', 'e')
			->select('e')
			->innerJoin('e.salaries', 's')
			->addSelect('s')
			->innerJoin('e.affiliatedDepartments', 'd')
			->addSelect('d')
			->innerJoin('d.department', 'dd')
			->addSelect('dd')
			->setMaxResults(500)
			->getQuery();

		$paginator = new Paginator($qb);

		foreach ($paginator->getIterator() as $emp) {
			/** @var Employee $emp */

			// $output->writeln
			echo sprintf('%s %s (%d):', $emp->getFirstName(), $emp->getLastName(), $emp->getId()), PHP_EOL;

			// $output->writeln
			echo "\tSalaries:", PHP_EOL;
			foreach ($emp->getSalaries() as $salary) {
				// $output->writeln
				echo "\t\t", $salary->getAmount(), PHP_EOL;
			}

			// $output->writeln
			echo "\tDepartments:", PHP_EOL;
			foreach ($emp->getAffiliatedDepartments() as $department) {
				// $output->writeln
				echo "\t\t" . $department->getDepartment()->getName(), PHP_EOL;
			}
		}
	}
}
