<?php

namespace Core\Controller;

use Core\Entity\Response;
use \Prometheus\CollectorRegistry;
use \Prometheus\RenderTextFormat;

class DefaultController extends CoreController
{
    /**
     * @return string
     */
    public function indexAction()
    {
        return $this->render('Default/index');
    }

    /**
     * @param string $options
     * @param string $imageSrc
     *
     * @return Response
     * @throws \Exception
     */
    public function uploadAction(string $options, string $imageSrc = null): Response
    {
        $image = $this->imageHandler()->processImage($options, $imageSrc);

        $this->response->generateImageResponse($image);

        return $this->response;
    }

    /**
     * @param string $options
     * @param string $imageSrc
     *
     * @return Response
     * @throws \Exception
     */
    public function pathAction(string $options, string $imageSrc = null): Response
    {
        $image = $this->imageHandler()->processImage($options, $imageSrc);

        $this->response->generatePathResponse($image);

        return $this->response;
    }

    /**
     * @return string
     */
    public function metricsAction()
    {
        
        $adapter = new \Prometheus\Storage\InMemory();

        $registry = new CollectorRegistry($adapter);
        $renderer = new RenderTextFormat();
        $result = $renderer->render($registry->getMetricFamilySamples());
        header('Content-type: ' . RenderTextFormat::MIME_TYPE);
        echo $result;
        exit;
    }
}
