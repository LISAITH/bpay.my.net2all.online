<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use mikehaertl\wkhtmlto\Pdf;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Twig\Environment;

class PdfService
{
    private $kernel;

    public function __construct(EntityManagerInterface $entityManager, Environment $environment, KernelInterface $kernel, ParameterBagInterface $parameterBag)
    {
        $this->kernel = $kernel;
    }

    /**
     * @throws \Exception
     */
    public function generatePDF(array $option, array $html, string $filename)
    {
        try {
            $pdf = new Pdf([
                    'ignoreWarnings' => true,
                    'commandOptions' => ['useExec' => true],
                    'no-outline',         // Make Chrome not complain
                    'margin-top' => 0,
                    'encoding' => 'UTF-8',
                    'margin-right' => 0,
                    'margin-bottom' => 0,
                    'margin-left' => 0,
                    // Default page options
                    'disable-smart-shrinking',
                    'user-style-sheet' => !empty($option['css_filename']) ? $this->kernel->getProjectDir().sprintf('%s%s', '\\public\\css\\', $option['css_filename']) : '', ]
            );
            foreach ($html as $content) {
                $pdf->addPage($content);
            }
            if (!$pdf->saveAs(sprintf('%s%s%s', $this->kernel->getProjectDir(), '\\public\\invoice\\', $filename))) {
                throw new \Exception('error');
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function generatePDFAndSend(array $option, $content, string $filename)
    {
        try {
            $pdf = new Pdf([
                    'ignoreWarnings' => true,
                    'commandOptions' => ['useExec' => true],
                    'no-outline',         // Make Chrome not complain
                    'margin-top' => 0,
                    'encoding' => 'UTF-8',
                    'margin-right' => 0,
                    'margin-bottom' => 0,
                    'margin-left' => 0,
                    // Default page options
                    'disable-smart-shrinking',
                    'user-style-sheet' => !empty($option['css_filename']) ? $this->kernel->getProjectDir().sprintf('%s%s', '\\public\\css\\', $option['css_filename']) : '', ]
            );

            $pdf->addPage($content);

            if (!$pdf->send($filename)) {
                throw new \Exception($pdf->getError());
            }
            /*  if (!$pdf->saveAs(sprintf('%s%s%s', $this->kernel->getProjectDir(), '\\public\\invoice\\', $filename))) {
                  throw new \Exception('error');
              }*/
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
