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

        $collectionRegistry = $this->app['prometheus.registry']->getMetricFamilySamples();
         $counter = $collectionRegistry->getOrRegisterCounter(
            'http',
            'requests_total',
            'total request count',
            ['code']
        );
        $counter->inc(['200']);
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
        $renderer = new RenderTextFormat();
        $result = $renderer->render($this->app['prometheus.registry']->getMetricFamilySamples());
        header('Content-type: ' . RenderTextFormat::MIME_TYPE);
        echo $result;
        exit;
    }
}
