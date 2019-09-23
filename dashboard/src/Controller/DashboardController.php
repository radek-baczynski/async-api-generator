<?php

namespace App\Controller;

use Aws\Credentials\Credentials;
use Aws\S3\S3Client;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Yaml\Yaml;

class DashboardController extends AbstractController
{
    const BUCKET_NAME    = 'async-docs.dq.docplanner.io';
    const DOCS_S3_BUCKET = 'http://async-docs.dq.docplanner.io';

    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }


    /**
     * @Route("/", name="dashboard")
     */
    public function index()
    {
        $credentials = new Credentials($this->getParameter('s3Key'), $this->getParameter('s3Secret'));

        $s3 = new S3Client([
            'version'     => 'latest',
            'region'      => 'eu-central-1',
            'credentials' => $credentials,
        ]);

        $apps = $this->fetchApps($s3);
        $apps = $this->fetchBranches($apps, $s3);
        $apps = $this->fetchDocs($apps, $s3);


        return $this->render('dashboard/index.html.twig', [
            'controller_name' => 'DashboardController',
            'apps'            => $apps,
        ]);
    }

    private function fetchApps(S3Client $s3): array
    {
        $appsRsp = $s3->listObjects([
            'Bucket'    => self::BUCKET_NAME,
            'Prefix'    => '',
            'Delimiter' => '/',
        ]);

        $apps = [];
        foreach ($appsRsp['CommonPrefixes'] ?? [] as $app) {
            $appName = trim($app['Prefix'], '/');

            if ($appName) {
                $apps[$appName] = [];
            }
        }

        return $apps;
    }

    private function fetchBranches(array $apps, S3Client $s3): array
    {
        foreach ($apps as $appName => &$app) {

            $branchesRsp = $s3->listObjects([
                'Bucket'    => self::BUCKET_NAME,
                'Prefix'    => $appName . '/',
                'Delimiter' => '/',
            ]);

            foreach ($branchesRsp['CommonPrefixes'] ?? [] as $branch) {
                $branchName = str_replace('.', '/', basename(trim($branch['Prefix'], '/')));

                $app['branches'][] = ['name' => $branchName, 'key' => $branch['Prefix']];
            }
        }

        return $apps;
    }

    private function fetchDocs(array $apps, S3Client $s3)
    {
        foreach ($apps as &$app) {
            foreach ($app['branches'] as &$branch) {

                try {
                    $docDirs = $s3->listObjects([
                        'Bucket'    => self::BUCKET_NAME,
                        'Prefix'    => $branch['key'],
                        'Delimiter' => '/',
                    ]);

                    foreach ($docDirs['CommonPrefixes'] ?? [] as $docDir) {
                        $docRsp = $s3->getObject([
                            'Key'    => $docDir['Prefix'] . 'async_api.yml',
                            'Bucket' => self::BUCKET_NAME,
                        ]);

                        $doc = Yaml::parse((string) $docRsp->get('Body'));
                        $doc['url'] = self::DOCS_S3_BUCKET.'/'.$docDir['Prefix'].'index.html';

                        $branch['docs'][] = $doc;
                    }

                } catch (\Exception $e) {
                    $this->logger->error($e);
                }
            }
        }

        return $apps;
    }
}
