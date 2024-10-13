<?php

namespace App\Handler\Master;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sx\Message\Response\ResponseHelperInterface;

class MasterLoadHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly ResponseHelperInterface $helper,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->helper->create(200, [
            'address' => "Cecilia Becker\nP.O. Box 568, 3980 Lectus, Rd.\n27645 İmamoğlu",
            'account' => "DE63 0432 7861 0112 9837",
            'number' => "R#nummer#-#jahr#-#checksum#",
        ]);
    }
}
