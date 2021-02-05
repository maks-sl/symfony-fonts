<?php

declare(strict_types=1);

namespace App\Controller\Panel;

use App\Annotation\Guid;

use App\Model\Font\Entity\Font;
use App\Model\Font\Service\File\FileManager;
use App\Model\Font\UseCase\Create;
use App\Model\Font\UseCase\Edit;
use App\Model\Font\UseCase\Activate;
use App\Model\Font\UseCase\Hide;
use App\Model\Font\UseCase\Remove;
use App\Model\Font\UseCase\Files;

use App\ReadModel\Font\Filter;
use App\ReadModel\Font\FontFetcher;

use App\Controller\ErrorHandler;

use League\Flysystem\FilesystemException;
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


    /**
     * @Route("/{id}", name=".show", requirements={"id"=Guid::PATTERN})
     * @param Font $font
     * @return Response
     */
    public function show(Font $font): Response
    {
        return $this->render('panel/fonts/show.html.twig', compact('font'));
    }

    /**
     * @Route("/create", name=".create")
     * @param Request $request
     * @param Create\Handler $handler
     * @return Response
     */
    public function create(Request $request, Create\Handler $handler): Response
    {
        $command = new Create\Command();

        $form = $this->createForm(Create\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                return $this->redirectToRoute('fonts');
            } catch (\DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('panel/fonts/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name=".edit")
     * @param Font $font
     * @param Request $request
     * @param Edit\Handler $handler
     * @return Response
     */
    public function edit(Font $font, Request $request, Edit\Handler $handler): Response
    {
        $command = Edit\Command::fromFont($font);

        $form = $this->createForm(Edit\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                return $this->redirectToRoute('fonts.show', ['id' => $font->getId()]);
            } catch (\DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('panel/fonts/edit.html.twig', [
            'font' => $font,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/activate", name=".activate", methods={"POST"})
     * @param Font $font
     * @param Request $request
     * @param Activate\Handler $handler
     * @return Response
     */
    public function activate(Font $font, Request $request, Activate\Handler $handler): Response
    {
        if (!$this->isCsrfTokenValid('font-activate', $request->request->get('token'))) {
            return $this->redirectToRoute('fonts.show', ['id' => $font->getId()]);
        }

        $command = new Activate\Command($font->getId()->getValue());

        try {
            $handler->handle($command);
        } catch (\DomainException $e) {
            $this->errors->handle($e);
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('fonts.show', ['id' => $font->getId()]);
    }

    /**
     * @Route("/{id}/hide", name=".hide", methods={"POST"})
     * @param Font $font
     * @param Request $request
     * @param Hide\Handler $handler
     * @return Response
     */
    public function hide(Font $font, Request $request, Hide\Handler $handler): Response
    {
        if (!$this->isCsrfTokenValid('font-hide', $request->request->get('token'))) {
            return $this->redirectToRoute('fonts.show', ['id' => $font->getId()]);
        }

        $command = new Hide\Command($font->getId()->getValue());

        try {
            $handler->handle($command);
        } catch (\DomainException $e) {
            $this->errors->handle($e);
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('fonts.show', ['id' => $font->getId()]);
    }

    /**
     * @Route("/{id}/delete", name=".delete", methods={"POST"})
     * @param Font $font
     * @param Request $request
     * @param Remove\Handler $handler
     * @return Response
     */
    public function delete(Font $font, Request $request, Remove\Handler $handler): Response
    {
        if (!$this->isCsrfTokenValid('delete', $request->request->get('token'))) {
            return $this->redirectToRoute('fonts.show', ['id' => $font->getId()]);
        }

        $command = new Remove\Command($font->getId()->getValue());

        try {
            $handler->handle($command);
        } catch (\DomainException $e) {
            $this->errors->handle($e);
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('fonts');
    }

    //////////// FILES

    /**
     * @Route("/{id}/files", name=".files")
     * @param Font $font
     * @param Request $request
     * @param Files\Add\Handler $handler
     * @param FileManager $fileManager
     * @return Response
     * @throws FilesystemException
     */
    public function files(Font $font, Request $request, Files\Add\Handler $handler, FileManager $fileManager): Response
    {
        $command = new Files\Add\Command($font->getId()->getValue());

        $form = $this->createForm(Files\Add\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $files = $form->get('files')->getData();
            try {
                $command->files = $fileManager->uploadFiles($files, $font);
                $handler->handle($command);
                return $this->redirectToRoute('fonts.show', ['id' => $font->getId()]);
            } catch (\DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('panel/fonts/files.html.twig', [
            'font' => $font,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/files/{file_id}/delete", name=".files.delete", methods={"POST"})
     * @param Font $font
     * @param string $file_id
     * @param Request $request
     * @param Files\Remove\Handler $handler
     * @return Response
     */
    public function fileDelete(Font $font, string $file_id, Request $request, Files\Remove\Handler $handler): Response
    {
        if (!$this->isCsrfTokenValid('delete-file', $request->request->get('token'))) {
            return $this->redirectToRoute('fonts.show', ['id' => $font->getId()]);
        }

        $command = new Files\Remove\Command($font->getId()->getValue(), $file_id);

        try {
            $handler->handle($command);
        } catch (\DomainException $e) {
            $this->errors->handle($e);
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('fonts.show', ['id' => $font->getId()]);
    }
}
