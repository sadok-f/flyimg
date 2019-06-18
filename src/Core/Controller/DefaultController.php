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
}
