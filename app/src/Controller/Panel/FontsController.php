<?php

declare(strict_types=1);

namespace App\Controller\Panel;

use App\ReadModel\Font\Filter;
use App\ReadModel\Font\FontFetcher;

use App\Controller\ErrorHandler;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/panel/fonts", name="fonts")
 */
class FontsController extends AbstractController
{
    private const PER_PAGE = 50;

    private $errors;

    public function __construct(ErrorHandler $errors)
    {
        $this->errors = $errors;
    }

    /**
     * @Route("", name="")
     * @param Request $request
     * @param FontFetcher $fonts
     * @return Response
     */
    public function index(Request $request, FontFetcher $fonts): Response
    {
        $filter = new Filter\Data();
        $form = $this->createForm(Filter\Form::class, $filter);
        $form->handleRequest($request);

        $pagination = $fonts->all(
            $filter,
            $request->query->getInt('page', 1),
            self::PER_PAGE,
            $request->query->get('sort'),
            $request->query->get('direction')
        );

        return $this->render('panel/fonts/index.html.twig', [
            'pagination' => $pagination,
            'form' => $form->createView(),
        ]);
    }

}
