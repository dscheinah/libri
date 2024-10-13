<?php

namespace App\Handler\Contact;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sx\Message\Response\ResponseHelperInterface;

class ContactListHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly ResponseHelperInterface $helper,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $contacts = [
            [
                'id' => 1,
                'name' => "Marvin Dennis",
                'mail' => "a.magna@ullamcorper.org",
                'phone' => "(032360) 530107",
            ],
            [
                'id' => 2,
                'name' => "Linus Zimmerman",
                'mail' => "curabitur.vel@morbi.com",
                'phone' => "",
            ],
            [
                'id' => 3,
                'name' => "Octavia Hampton",
                'mail' => "",
                'phone' => "(0494) 01977234",
            ],
            [
                'id' => 4,
                'name' => "Jack Chavez",
                'mail' => "",
                'phone' => "",
            ],
        ];
        $search = $request->getQueryParams()['search'] ?? null;
        if ($search) {
            $search = mb_strtolower($search);
            $contacts = array_filter($contacts, static function ($contact) use ($search) {
                return str_contains(mb_strtolower($contact['name']), $search);
            });
        }
        return $this->helper->create(200, array_values($contacts));
    }
}
