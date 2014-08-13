<?php
/**
 * Console Helper
 *
 * @author    Giang Nguyen (tn.hoanggiang@gmail.com)
 * @link      http://smartexts.com
 * @copyright Copyright &copy; 2014, smartexts.com.
 * @license   https://github.com/bluecloudy/yiidoctrine2
 */

use Symfony\Component\Console\Helper\HelperSet;
use Doctrine\ORM\Tools\Console\ConsoleRunner;

// Get EntityManager
$entityManager = Yii::app()->doctrine->getEntityManager();
/**
 * @var Doctrine\ORM\EntityManager $entityManager
 */

$helperSet = new \Symfony\Component\Console\Helper\HelperSet(array(
	'db' => new \Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper($entityManager->getConnection()),
	'em' => new \Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper($entityManager)
));

$commands = array();

if (!($helperSet instanceof HelperSet))
{
	foreach ($GLOBALS as $helperSetCandidate)
	{
		if ($helperSetCandidate instanceof HelperSet)
		{
			$helperSet = $helperSetCandidate;
			break;
		}
	}
}

\Doctrine\ORM\Tools\Console\ConsoleRunner::run($helperSet, $commands);