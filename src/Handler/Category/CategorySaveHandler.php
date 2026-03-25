<?php

namespace App\Handler\Category;

use App\Repository\CategoryRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sx\Message\Response\ResponseHelperInterface;

class CategorySaveHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly ResponseHelperInterface $helper,
        private readonly CategoryRepository $repository,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $data = (array) $request->getParsedBody();
        if (!isset($data['name'])) {
            return $this->helper->create(400);
        }
        $this->repository->updateCategories((array) $data['name']);
        return $this->helper->create(204);
    }
}
