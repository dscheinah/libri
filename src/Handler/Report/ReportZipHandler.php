<?php

namespace App\Handler\Report;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sx\Message\Response;
use ZipStream\ZipStream;

class ReportZipHandler implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $zip = new ZipStream(outputName: 'dummy.zip');
        $zip->finish();
        return new Response();
    }
}
