<?php

/**
 * This controller will demo the usage of annotations to configure
 * the route to the right controller action
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ConferenceController extends AbstractController
{
    /**
     * This will route / to the controller action below the annotation
     * The controller action responds basic html body with an image
     *
     * If you define a GET parameter 'hello' in the URL,
     * there will be a greeting message in the response
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    #[Route('/', name: 'homepage')]
    public function index(Request $request): Response
    {
        $greet = '';
        if($name = $request->query->get('hello')) {
            $greet = sprintf('<h1>Hello %s!</h1>', htmlspecialchars($name));
        }

        return new Response(<<<EOF
<html>
    <body>
        $greet
        <img src="/images/under-construction.gif" />
    </body>
</html>
EOF
        );
    }
}
