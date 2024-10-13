<?php

namespace App\Handler\Contact;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sx\Message\Response\ResponseHelperInterface;

class ContactLoadHandler implements RequestHandlerInterface
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
                'address' => "Marvin Dennis\n7981 Commodo Road\n05903 Neiva",
                'income' => 146.22,
                'expense' => 0,
            ],
            [
                'id' => 2,
                'name' => "Linus Zimmerman",
                'mail' => "curabitur.vel@morbi.com",
                'phone' => "",
                'address' => "Linus Zimmerman\n259-7856 Nisl Av.\n59756 Roccabruna",
                'income' => 389.60,
                'expense' => 0,
            ],
            [
                'id' => 3,
                'name' => "Octavia Hampton",
                'mail' => "",
                'phone' => "(0494) 01977234",
                'address' => "Octavia Hampton\n784-5943 Id, St.\n53845 Schulen",
                'income' => 238.60,
                'expense' => 0,
            ],
            [
                'id' => 4,
                'name' => "Jack Chavez",
                'mail' => "",
                'phone' => "",
                'address' => "",
                'income' => 0,
                'expense' => -403.00,
            ],
        ];
        $contact = $contacts[$request->getQueryParams()['id'] - 1] ?? null;
        if (!$contact) {
            return $this->helper->create(404, 'Kontakt nicht gefunden');
        }
        return $this->helper->create(200, $contact);
    }
}
